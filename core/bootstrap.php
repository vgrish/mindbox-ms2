<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

if (!\defined('MODX_CORE_PATH')) {
    if (\file_exists('/modx/config.core.php')) {
        require '/modx/config.core.php';
    } else {
        $dir = __DIR__;

        while (true) {
            if ('/' === $dir) {
                break;
            }

            if (\file_exists($dir . '/config.core.php')) {
                require $dir . '/config.core.php';

                break;
            }

            if (\file_exists($dir . '/config/config.inc.php')) {
                require $dir . '/config/config.inc.php';

                break;
            }

            $dir = \dirname($dir);
        }
    }

    if (!\defined('MODX_CORE_PATH')) {
        exit('Could not load MODX core');
    }

    include_once MODX_CORE_PATH . 'model/modx/modx.class.php';
}

/** @var modX $modx */
if (!isset($modx)) {
    $modx = \modX::getInstance(\modX::class);
    $modx->initialize();
}

require MODX_CORE_PATH . 'vendor/autoload.php';

use Vgrish\MindBox\MS2\App;

if (!$app = $modx->services[App::NAME] ?? null) {
    $modx->services[App::NAME] = new App($modx);
}