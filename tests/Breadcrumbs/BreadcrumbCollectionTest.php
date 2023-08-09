<?php

namespace Tests\EnjoysCMS\Breadcrumbs;

use EnjoysCMS\Core\Breadcrumbs\BreadcrumbCollection;
use EnjoysCMS\Core\Breadcrumbs\BreadcrumbUrlResolverInterface;
use PHPUnit\Framework\TestCase;
use Tests\EnjoysCMS\Traits\MockHelper;

class BreadcrumbCollectionTest extends TestCase
{

    use MockHelper;

    private BreadcrumbUrlResolverInterface $urlResolver;

    protected function setUp(): void
    {
        $this->urlResolver = new class () implements BreadcrumbUrlResolverInterface {
            public function resolve(array $dataUrl): string
            {
                return current($dataUrl);
            }
        };
    }

    public function testGetLastBreadcrumb()
    {
        $bc = new BreadcrumbCollection($this->urlResolver);
        $this->assertNull($bc->getLastBreadcrumb());
        $bc->add('/url', 'title');
        $bc->setLastBreadcrumb('Last Title', '/last_url');

        $this->assertCount(1, $bc);

        $lastBc = $bc->getLastBreadcrumb();

        $this->assertSame('/last_url', $lastBc->getUrl());
        $this->assertSame('Last Title', $lastBc->getTitle());

        $bc->setAppendLastBreadcrumb()->setLastBreadcrumb('Replaced Last Title And Append', '/last_url');

        $replacedLastBc = $bc->getLastBreadcrumb();

        $this->assertCount(2, $bc);
        $this->assertSame('Replaced Last Title And Append', $replacedLastBc->getTitle());
    }


    public function testGetKeyValueArray()
    {
        $bc = new BreadcrumbCollection($this->urlResolver);
        $bc->add('url1', 'Url 1')
            ->add('url2', 'Url 2')
            ->add('url3', 'Url 3');

        $this->assertSame([
            "url1" => "Url 1",
            "url2" => "Url 2",
            "url3" => "Url 3",
        ], $bc->getKeyValueArray());
    }

    public function testGetBreadcrumbs()
    {
        $bc = new BreadcrumbCollection($this->urlResolver);
        $bc->add('url1', 'Url 1')
            ->add('url2', 'Url 2')
            ->add('url3', 'Url 3');

        $this->assertCount(3, $bc->getIterator());
    }

    public function testRemove()
    {
        $bc = new BreadcrumbCollection($this->urlResolver);
        $bc->add('url1', 'Url 1')
            ->add('url2', 'Url 2')
            ->add('url3', 'Url 3');

        $this->assertCount(3, $bc);

        $bc->remove('url2');
        $this->assertCount(2, $bc);

        $bc->add('url4', 'Url 4')
            ->add('url5', 'Url 5');

        $this->assertCount(4, $bc);
    }
}
