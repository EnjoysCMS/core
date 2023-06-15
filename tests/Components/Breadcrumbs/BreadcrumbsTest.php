<?php

namespace Tests\EnjoysCMS\Components\Breadcrumbs;

use EnjoysCMS\Core\Breadcrumbs\BreadcrumbCollection;
use PHPUnit\Framework\TestCase;

class BreadcrumbsTest extends TestCase
{

    public function testBreadcrumbAddOnePosition()
    {
        $breadcrumbs = new BreadcrumbCollection();
        $breadcrumbs->add($url = 'testUrl', $title = 'testTitle');
        $bc = $breadcrumbs->getBreadcrumbs();
        $this->assertNotEmpty($bc);
        $this->assertCount(1, $bc);
        $this->assertSame($url, $bc[0]->url);
        $this->assertSame($title, $bc[0]->title);
    }

    public function testBreadcrumbAddManyPosition()
    {
        $breadcrumbs = new BreadcrumbCollection();
        $breadcrumbs->add('testUrl', 'testTitle');
        $breadcrumbs->add($url = null, $title = 'testTitle2');
        $bc = $breadcrumbs->getBreadcrumbs();
        $this->assertNotEmpty($bc);
        $this->assertCount(2, $bc);
        $this->assertSame($url, $bc[1]->url);
        $this->assertSame($title, $bc[1]->title);
    }

    public function testBreadcrumbAddNull()
    {
        $breadcrumbs = new BreadcrumbCollection();
        $breadcrumbs->add();
        $bc = $breadcrumbs->getBreadcrumbs();
        $this->assertNotEmpty($bc);
        $this->assertCount(1, $bc);
        $this->assertNull($bc[0]->url);
        $this->assertNull($bc[0]->title);
    }

    public function testBreadcrumbAddManyNull()
    {
        $breadcrumbs = new BreadcrumbCollection();
        $breadcrumbs->add();
        $breadcrumbs->add();
        $bc = $breadcrumbs->getBreadcrumbs();
        $this->assertNotEmpty($bc);
        $this->assertCount(2, $bc);
        $this->assertNull($bc[0]->url);
        $this->assertNull($bc[0]->title);
        $this->assertNull($bc[1]->url);
        $this->assertNull($bc[1]->title);
    }

}
