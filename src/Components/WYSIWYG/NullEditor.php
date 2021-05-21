<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Components\WYSIWYG;


final class NullEditor implements WysiwygInterface
{
     public function getTwigTemplate(){
        return __DIR__.'/nulleditor.twig';
    }
}