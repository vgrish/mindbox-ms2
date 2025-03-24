<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Tools;

class Headers
{
    public static function getAll(?string $key = null): array
    {
        static $headers = [];

        if (empty($headers)) {
            foreach (getallheaders() as $key => $value) {
                $headers[\mb_strtolower($key)] = $value;
            }
        }

        return $headers;
    }

    public static function get(string $key): string
    {
        return self::getAll()[$key] ?? '';
    }

    public static function validateWebHookAuthorization(string $secretKey): bool
    {
        $header = self::get('authorization');
        $prefix = 'WebHookSecretKey ';

        if (!\str_starts_with($header, $prefix)) {
            return false;
        }

        if (\mb_substr($header, \mb_strlen($prefix)) !== $secretKey) {
            return false;
        }

        return true;
    }

    public static function validateUserAgentIsBot(string $pattern): bool
    {
        $header = self::get('user-agent');

        if (empty($header)) {
            return false;
        }

        $pattern = \array_filter(\array_map('trim', \explode('|', $pattern)));

        if (empty($pattern)) {
            return false;
        }

        if (\preg_match('~(' . \implode('|', $pattern) . ')~i', $header)) {
            return true;
        }

        return false;
    }
}
