<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Breadcrumbs;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class BreadcrumbCollection
{

    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    /**
     * @var BreadcrumbInterface[]
     */
    private array $stack = [];


    public function addBreadcrumbWithoutUrl(string $title): BreadcrumbCollection
    {
        $this->add(title: $title);
        return $this;
    }

    /**
     * @param array{string, array}|string|null $data
     * @param string|null $title
     * @return $this
     */
    public function add(string|array|null $data = null, ?string $title = null): BreadcrumbCollection
    {
        $breadcrumb = new Breadcrumb($this->urlGenerator);
        $breadcrumb->setTitle($title);
        $breadcrumb->setUrl($data);
        $this->stack[] = $breadcrumb;

        return $this;
    }


    /**
     * @return BreadcrumbInterface[]
     */
    public function get(): array
    {
        return $this->stack;
    }

    public function getKeyValueArray(): array
    {
        $result = [];
        foreach ($this->stack as $item) {
            $url = $item->getUrl();
            if ($url === null){
                $result[] = $item->getTitle();
                continue;
            }
            $result[$url] = $item->getTitle();
        }
        return $result;
    }

}
