<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Tools;

class BasicAuth
{
    public static function validateAuthorization(string $validUser, string $validPassword): bool
    {
        $authUsername = $_SERVER['PHP_AUTH_USER'] ?? null;
        $authPassword = $_SERVER['PHP_AUTH_PW'] ?? null;

        return
            !(!$authUsername
            || !$authPassword
            || $authUsername !== $validUser
            || !\password_verify($authPassword, \password_hash($validPassword, \PASSWORD_DEFAULT)));
    }
}
