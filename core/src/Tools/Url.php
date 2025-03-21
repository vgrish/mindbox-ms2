<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Tools;

use Vgrish\MindBox\MS2\Http\Payload;

class Url
{
    public const API = 'https://api.mindbox.ru/v3/operations';

    public static function make(Payload $payload): string
    {
        return self::makeFromPathAndParams($payload->path, $payload->params);
    }

    public static function makeFromPathAndParams(array $path, array $params = []): string
    {
        return self::prepareUrl($path) . self::prepareQueryParams($params);
    }

    private static function prepareUrl(array $path): string
    {
        return self::API . '/' . \implode('/', $path);
    }

    private static function prepareQueryParams(array $params): string
    {
        $paramsString = \http_build_query($params);

        return '' === $paramsString ? '' : "?{$paramsString}";
    }
}
