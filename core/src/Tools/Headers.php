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
    public static function validateAWebHookAuthorizationHeader(array $headers, string $secretKey): bool
    {
        $header = (string) ($headers['Authorization'] ?? '');
        $prefix = 'WebHookSecretKey ';

        if (!\str_starts_with($header, $prefix)) {
            return false;
        }

        if (\mb_substr($header, \mb_strlen($prefix)) !== $secretKey) {
            return false;
        }

        return true;
    }
}
