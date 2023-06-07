<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Block;

class UserBlock extends AbstractBlock
{
    public function view(): string
    {
        $body = '';

//        if ($body === null) {
//            return '';
//        }
//
//        if ($this->getOption('allowed_html') === 'true') {
//            return $body;
//        }
        return htmlspecialchars($body, ENT_QUOTES | ENT_SUBSTITUTE);
    }



    public static function getMeta(): array
    {
        return [
            'options' => [
                'allowed_html' => [
                    'value' => 'true',
                    'name' => 'Разрешить использование HTML?',
                    'description' => null,
                    'form' => [
                        'type' => 'radio',
                        'data' => [
                            'true' => 'Да',
                            'false' => 'Нет'
                        ]
                    ]
                ]
            ]
        ];
    }
}
