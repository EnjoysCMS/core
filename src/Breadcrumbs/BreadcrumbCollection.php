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
     * @var array<int, BreadcrumbInterface>
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
     * @param array{string, array}|string $data
     * @return $this
     */
    public function remove(string|array $data): BreadcrumbCollection
    {
        $tmp = $this->find($data);

        if ($tmp === null){
            return $this;
        }

        $position = $this->findPosition($tmp);
        if ($position === null){
            return $this;
        }

        unset($this->stack[$position]);

        return $this;

    }

    private function findPosition(BreadcrumbInterface $breadcrumb): ?int
    {
        foreach ($this->stack as $position => $item) {
            if ($item === $breadcrumb) {
                return $position;
            }
        }
        return null;
    }

    /**
     * @param BreadcrumbInterface|array{string, array}|string $breadcrumb
     * @return BreadcrumbInterface|null
     */
    private function find(BreadcrumbInterface|string|array $breadcrumb): ?BreadcrumbInterface
    {
        $url = $breadcrumb instanceof BreadcrumbInterface ? $breadcrumb->getUrl() : (new Breadcrumb($this->urlGenerator))->setUrl($breadcrumb)->getUrl();
        foreach ($this->stack as $item) {
            if ($item->getUrl() === $url) {
                return $item;
            }
        }
        return null;
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
            if ($url === null) {
                $result[] = $item->getTitle();
                continue;
            }
            $result[$url] = $item->getTitle();
        }
        return $result;
    }

}
