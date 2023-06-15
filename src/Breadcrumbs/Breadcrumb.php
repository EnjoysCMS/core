<?php

namespace EnjoysCMS\Core\Breadcrumbs;

use Stringable;

class Breadcrumb implements BreadcrumbInterface, Stringable
{
    private ?string $title = null;
    private ?string $url = null;

    public function __construct(
        private readonly BreadcrumbUrlResolverInterface $urlResolver
    ) {
    }

    public function __toString(): string
    {
        return $this->getTitle() ?? '';
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;
        return $this;
    }


    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param array{string, ?array}|string|null $data
     */
    public function setUrl(string|array|null $data, bool $skipRoute = false): static
    {
        if ($data === null) {
            $this->url = '';
            return $this;
        }
        $data = (array)$data;

        if ($skipRoute){
            $this->url = $data[0];
            return $this;
        }

        /** @var array{string, ?array} $data */
        $this->url = $this->urlResolver->resolve($data);
        return $this;
    }

}
