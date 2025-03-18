<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

use Vgrish\MindBox\MS2\App;
use Vgrish\MindBox\MS2\EventManager;

require \dirname(__DIR__) . '/bootstrap.php';

/** @var modX $modx */
if (!$app = $modx->services[App::NAME] ?? null) {
    return;
}

EventManager::load($app);
