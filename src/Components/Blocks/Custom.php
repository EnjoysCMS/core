<?php


namespace EnjoysCMS\Core\Components\Blocks;


use EnjoysCMS\Core\Components\Blocks\AbstractBlock;

class Custom extends AbstractBlock
{

    public function view()
    {
        $body = $this->block->getBody();

        if ($this->getOption('allowed_html') === 'true') {
            return $body;
        }
        return htmlspecialchars($body, ENT_QUOTES | ENT_SUBSTITUTE);
    }

    public static function stockOptions(): ?array
    {
        return [
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
        ];
    }
}
