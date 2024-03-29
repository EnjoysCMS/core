<?php

namespace EnjoysCMS\Core\Components\TwigExtension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * @TODO    все названия тут нахрен поменять, sanitize вынести в отдельный класс, или даже пакет
 * @deprecated use \EnjoysCMS\Core\Components\Extensions\Twig\CoreTwigExtension
 */
class PickingCssJs extends AbstractExtension
{
    private static array $scripts = [];
    private static array $styles = [];
    private bool $nocatch = false;

    public function getFilters(): array
    {
        return [
            new TwigFilter('scriptsCatcher', [$this, 'scriptsCatcher'], ['is_safe' => ['all']]),
            new TwigFilter('stylesCatcher', [$this, 'stylesCatcher'], ['is_safe' => ['all']]),
            new TwigFilter('nocatch', [$this, 'noCatch'], ['is_safe' => ['all']]),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getStyles', [$this, 'getStyles'], ['is_safe' => ['html']]),
            new TwigFunction('getScripts', [$this, 'getScripts'], ['is_safe' => ['html']]),
        ];
    }


    public function getStyles(): string
    {
        return implode("\n", static::$styles);
    }

    public function getScripts(): string
    {
        return implode("\n", static::$scripts);
    }

    public function noCatch(?string $body): string|null
    {
        return preg_replace("/(<script[^>]*?>)(.*?)(<\/script>)/simu", "$1\n//DO_NOT_CATCH//\n$2$3", $body);
    }

    public function scriptsCatcher(?string $body): string
    {
        preg_match_all("/<script[^>]*?>.*?<\/script>/simu", $body, $scripts);
        foreach ($scripts[0] as $script) {
            if (strpos($script, '//DO_NOT_CATCH//') !== false) {
                continue;
            }
            $body = str_replace($script, "", $body);

            if (!in_array($script, static::$scripts)) {
                static::$scripts[] = $script;
            }
        }

        return $body;
    }

    public function stylesCatcher(?string $body): string
    {
        preg_match_all("/<style[^>]*?>.*?<\/style>/simu", $body, $styles);
        foreach ($styles[0] as $style) {
            $body = str_replace($style, "", $body);
            if (!in_array($style, static::$styles)) {
                static::$styles[] = $style;
            }
        }
        return $body;
    }

    //    public function sanitize(string $body): string
    //    {
    //        $search = array(
    //            '/\>[^\S ]+/s', //strip whitespaces after tags, except space
    //            '/[^\S ]+\</s', //strip whitespaces before tags, except space
    //            '/(\s)+/s', // shorten multiple whitespace sequences
    //            '/<!--(.|\s)*?-->/' // Remove HTML comments
    //        );
    //        $replace = array(
    //            '>',
    //            '<',
    //            '\\1',
    //            ''
    //        );
    //
    //        $blocks = \preg_split('/(<\/?pre[^>]*>)/', $body, 0, \PREG_SPLIT_DELIM_CAPTURE);
    //        $result = '';
    //        foreach ($blocks as $i => $block) {
    //            if ($i % 4 == 2) {
    //                $result .= $block; //break out <pre>...</pre> with \n's
    //            } else {
    //                $result .= \preg_replace($search, $replace, $block);
    //            }
    //        }
    //
    //        return $result;
    //    }
}
