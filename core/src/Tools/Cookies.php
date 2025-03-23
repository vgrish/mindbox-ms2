<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Tools;

class Cookies
{
    public const MINDBOX_DEVICE_UUID = 'mindboxDeviceUUID';

    public static function setCookie($name, $value, $expires = 0, $httpOnly = true, $sameSite = 'Strict'): void
    {
        $domain = (string) ($_SERVER['HTTP_HOST'] ?? '');

        if (!empty($domain)) {
            $domain = '.' . $domain;
        }

        \setcookie(
            $name,
            $value,
            [
                'expires' => $expires,
                'path' => MODX_BASE_URL,
                'domain' => $domain,
                'httponly' => $httpOnly,
                'secure' => true,
                'samesite' => $sameSite, // None || Lax || Strict
            ],
        );
    }

    public static function getDeviceUUID(bool $required = false): string
    {
        $uuid = (string) ($_COOKIE[self::MINDBOX_DEVICE_UUID] ?? '');

        if (empty($uuid) && $required) {
            $uuid = self::generateDeviceUUID(true);
        }

        return $uuid;
    }

    public static function generateDeviceUUID(bool $set = false): string
    {
        $uuid = \sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            \mt_rand(0, 0xFFFF),
            \mt_rand(0, 0xFFFF),
            \mt_rand(0, 0xFFFF),
            \mt_rand(0, 0x0FFF) | 0x4000,
            \mt_rand(0, 0x3FFF) | 0x8000,
            \mt_rand(0, 0xFFFF),
            \mt_rand(0, 0xFFFF),
            \mt_rand(0, 0xFFFF),
        );

        if ($set) {
            self::setCookie(self::MINDBOX_DEVICE_UUID, $uuid);
        }

        return $uuid;
    }
}
