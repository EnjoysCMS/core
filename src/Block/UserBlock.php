<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Block;

class UserBlock extends AbstractBlock
{
    public const META = [
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

    public function view(): string
    {
        $body = $this->getEntity()?->getBody();

        if ($body === null) {
            return '';
        }

        if ($this->getBlockOptions()->getValue('allowed_html') === 'true') {
            return $body;
        }
        return htmlspecialchars($body, ENT_QUOTES | ENT_SUBSTITUTE);
    }

}
