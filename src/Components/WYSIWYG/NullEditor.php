<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Components\WYSIWYG;


final class NullEditor implements WysiwygInterface
{
    private string $twigTemplate = '@wysisyg/.gitkeep';

    public function getTwigTemplate(){
        return $this->twigTemplate;
    }
}