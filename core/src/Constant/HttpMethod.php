<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Constant;

enum HttpMethod: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
    case HEAD = 'HEAD';
    case CONNECT = 'CONNECT';
    case OPTIONS = 'OPTIONS';
    case TRACE = 'TRACE';
    case PATCH = 'PATCH';

    public static function makeFrom(HttpMethod|string $method): HttpMethod
    {
        if (!\is_string($method)) {
            return $method;
        }

        $method = \mb_strtoupper($method);
        $enumMethod = self::tryFrom($method);

        if (null === $enumMethod) {
            throw new \InvalidArgumentException(\sprintf('`%s` is not valid HTTP method', $method));
        }

        return $enumMethod;
    }
}
