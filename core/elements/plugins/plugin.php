<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

use Vgrish\MindBox\MS2\App;
use Vgrish\MindBox\MS2\WorkerManager;

/** @var modX $modx */
/** @var array $scriptProperties */
if (!$app = $modx->services[App::NAME] ?? null) {
    return;
}

$eventsWorkers = App::getEventsWorkersFromConfigFile();
$workers = $eventsWorkers[$modx->event->name] ?? [];

if (empty($workers)) {
    return;
}

WorkerManager::load($app, $workers);
