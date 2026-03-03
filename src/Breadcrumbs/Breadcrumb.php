<?php

namespace EnjoysCMS\Core\Breadcrumbs;

class Breadcrumb implements BreadcrumbInterface
{
    private ?string $title = null;
    private ?string $url = null;

    public function __construct(
        private readonly BreadcrumbUrlResolverInterface $urlResolver
    ) {
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->getTitle() ?? '';
    }

    #[\Override]
    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;
        return $this;
    }


    #[\Override]
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param array{string, ?array}|string|null $dataUrl
     */
    public function setUrl(string|array|null $dataUrl, bool $skipRoute = false): static
    {
        if ($dataUrl === null) {
            $this->url = '';
            return $this;
        }
        $dataUrl = (array)$dataUrl;

        if ($skipRoute){
            $this->url = $dataUrl[0];
            return $this;
        }

        /** @var array{string, ?array} $dataUrl */
        $this->url = $this->urlResolver->resolve($dataUrl);
        return $this;
    }

}
