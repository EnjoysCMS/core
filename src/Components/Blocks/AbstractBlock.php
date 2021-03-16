<?php


namespace EnjoysCMS\Core\Components\Blocks;


use EnjoysCMS\Core\Entities\Blocks as Entity;
use Enjoys\Traits\Options;
use Twig\Environment;

abstract class AbstractBlock
{

    use Options;

    /**
     * @var Environment
     */
    protected Environment $twig;
    /**
     * @var Entity
     */
    protected Entity $block;

    public function __construct(Environment $twig, Entity $block)
    {
        $this->twig = $twig;
        $this->block = $block;

        $this->setOptions($this->block->getOptionsKeyValue());
    }


    abstract public function view();

    public static function stockOptions(): ?array
    {
        return null;
    }
}
