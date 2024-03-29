<?php

namespace EnjoysCMS\Core\Components\Helpers;

/**
 * @deprecated
 */
class Http
{
    public static function getQueryParams(string $currentUrl, array $removedParams = []): string
    {
        $query = [];
        $url = parse_url($currentUrl);
        if (isset($url['query'])) {
            parse_str($url['query'], $query);
            foreach ($removedParams as $key) {
                unset($query[$key]);
            }
        }

        return http_build_query($query);
    }
}
