<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Breadcrumbs;

final class BreadcrumbCollection
{

    public function __construct(private readonly BreadcrumbUrlResolverInterface $urlResolver)
    {
    }

    /**
     * @var array<int, BreadcrumbInterface>
     */
    private array $stack = [];

    private ?BreadcrumbInterface $lastBreadcrumb = null;



    /**
     * @param array{string, ?array}|string|null $data
     * @param string|null $title
     * @param bool $skipUrlResolve
     * @return $this
     */
    public function add(string|array|null $data = null, ?string $title = null, bool $skipUrlResolve = false): BreadcrumbCollection
    {
        $breadcrumb = new Breadcrumb($this->urlResolver);
        $breadcrumb->setTitle($title);
        $breadcrumb->setUrl($data, $skipUrlResolve);
        $this->stack[] = $breadcrumb;

        return $this;
    }

    /**
     * @param array{string, ?array}|string $data
     * @return $this
     */
    public function remove(string|array $data): BreadcrumbCollection
    {
        $tmp = $this->find($data);

        if ($tmp === null) {
            return $this;
        }

        $position = $this->findPosition($tmp);
        if ($position === null) {
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
     * @param BreadcrumbInterface|array{string, ?array}|string $breadcrumb
     * @return BreadcrumbInterface|null
     */
    private function find(BreadcrumbInterface|string|array $breadcrumb): ?BreadcrumbInterface
    {
        $url = $breadcrumb instanceof BreadcrumbInterface ? $breadcrumb->getUrl() : (new Breadcrumb(
            $this->urlResolver
        ))->setUrl($breadcrumb)->getUrl();

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
    public function getBreadcrumbs(bool $withLastBreadcrumb = false): array
    {

        if ($withLastBreadcrumb){
            if ($this->getLastBreadcrumb() !== null){
                return array_merge($this->stack, [$this->getLastBreadcrumb()]);
            }
        }
        return $this->stack;
    }

    /**
     * @return array<array-key, string|null>
     */
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

        if (null !== $lastBreadcrumb = $this->getLastBreadcrumb()){
            $result[$lastBreadcrumb->getUrl() ?? ''] = $lastBreadcrumb->getTitle();
        }

        return $result;
    }

    /**
     * @param string $title
     * @param array{string, ?array}|string|null $dataUrl
     * @return $this
     */
    public function setLastBreadcrumb(string $title, array|string|null $dataUrl = null): BreadcrumbCollection
    {
        $this->lastBreadcrumb = (new Breadcrumb($this->urlResolver))->setTitle($title)->setUrl($dataUrl);
        return $this;
    }

    public function getLastBreadcrumb(): ?BreadcrumbInterface
    {
        return $this->lastBreadcrumb;
    }

}
