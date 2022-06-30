<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Components\Pagination;

use EnjoysCMS\Core\Exception\NotFoundException;

/**
 * Class Pagination Helper Class
 */
final class Pagination
{
    private int $currentPage;
    private int $limitItems;
    private int $totalItems = 0;
    private int $totalPages = 0;
    private int $offset;
    private ?int $nextPage = null;
    private ?int $prevPage = null;
    private bool $active = false;

    public function __construct($currentPage, $limitItems)
    {
        $this->currentPage = $this->initCurrentPage((int)$currentPage);
        $this->limitItems = $this->initLimitItems($limitItems);
        $this->offset = $this->initOffset();
    }

    /**
     * @throws NotFoundException
     */
    public function setTotalItems(int $count): void
    {
        $this->totalItems = $count;

        $this->initTotalPages();
        $this->setNextPage();
        $this->setPrevPage();
    }

    /**
     * @throws NotFoundException
     */
    private function initTotalPages(): void
    {
        $this->totalPages = (int)ceil($this->getTotalItems() / $this->getLimitItems());

        if ($this->totalPages === 0) {
            return;
        }

        $this->validate();
    }

    private function initCurrentPage(int $currentPage): int
    {
        return ($currentPage < 1) ? 1 : $currentPage;
    }

    private function initLimitItems($limitItems): int
    {
        return (int)$limitItems;
    }

    public function getNextPage(): ?int
    {
        return $this->nextPage;
    }


    private function setNextPage(): void
    {
        $nextPage = $this->getCurrentPage() + 1;
        if ($nextPage > $this->getTotalPages()) {
            return;
        }

        $this->nextPage = $nextPage;
    }

    public function getPrevPage(): ?int
    {
        return $this->prevPage;
    }


    private function setPrevPage(): void
    {
        $prevPage = $this->getCurrentPage() - 1;
        if ($prevPage < 1) {
            return;
        }
        $this->prevPage = $prevPage;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    private function setActive(bool $active): void
    {
        $this->active = $active;
    }

    private function initTotalItems($totalItems): int
    {
        return (int)$totalItems;
    }

    private function initOffset(): int
    {
        return $this->getLimitItems() * ($this->getCurrentPage() - 1);
    }

    public function getLimitItems(): int
    {
        return $this->limitItems;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @throws NotFoundException
     */
    private function validate(): void
    {
        if ($this->getTotalPages() < $this->getCurrentPage()) {
            throw new NotFoundException(sprintf('Max page is %s, you are try get %s', $this->getTotalPages(), $this->getCurrentPage()));
        }

        if ($this->getTotalPages() === 1) {
            return;
        }
        $this->setActive(true);
    }
}
