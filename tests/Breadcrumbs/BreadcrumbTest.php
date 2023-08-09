<?php

namespace Tests\EnjoysCMS\Breadcrumbs;

use EnjoysCMS\Core\Breadcrumbs\Breadcrumb;
use EnjoysCMS\Core\Breadcrumbs\BreadcrumbUrlResolverInterface;
use PHPUnit\Framework\TestCase;
use Tests\EnjoysCMS\Traits\MockHelper;

class BreadcrumbTest extends TestCase
{

    use MockHelper;

    private BreadcrumbUrlResolverInterface $urlResolver;

    protected function setUp(): void
    {
        $this->urlResolver = $this->getMock(BreadcrumbUrlResolverInterface::class);
    }

    public function testSetTitle()
    {
        $breadcrumb = new Breadcrumb($this->urlResolver);

        $breadcrumb->setTitle(null);
        $this->assertNull($breadcrumb->getTitle());

        $breadcrumb->setTitle($title = 'title');
        $this->assertSame($title, $breadcrumb->getTitle());
    }

    public function testSetUrlWithSkipResolveRoute()
    {
        $breadcrumb = new Breadcrumb($this->urlResolver);

        $breadcrumb->setUrl(null);
        $this->assertSame('', $breadcrumb->getUrl());

        // with string and  skip resolve route
        $breadcrumb->setUrl($string_url = 'url/is_string', true);
        $this->assertSame($string_url, $breadcrumb->getUrl());

        // with array and skip resolve route
        $breadcrumb->setUrl(
            $array_url = [
                'route_name',
                [
                    'params1' => 1,
                    'params2' => 2,
                ]
            ],
            true
        );
        $this->assertSame($array_url[0], $breadcrumb->getUrl());
    }

    public function testSetUrlWithResolveRoute()
    {
        $this->urlResolver
            ->method('resolve')
            ->willReturnMap([
                [
                    $array_url = [
                        'route_name',
                        [
                            'params1' => 1,
                            'params2' => 2,
                        ]
                    ],
                    '/route?param=1&param=2'
                ],
                [(array)($string_url = 'url/is_string'), 'url/is_string']
            ]);

        $breadcrumb = new Breadcrumb($this->urlResolver);

        $breadcrumb->setUrl(null);
        $this->assertSame('', $breadcrumb->getUrl());

        // with string and resolve route
        $breadcrumb->setUrl($string_url);
        $this->assertSame($string_url, $breadcrumb->getUrl());

        // with array and resolve route
        $breadcrumb->setUrl($array_url);
        $this->assertSame('/route?param=1&param=2', $breadcrumb->getUrl());
    }
}
