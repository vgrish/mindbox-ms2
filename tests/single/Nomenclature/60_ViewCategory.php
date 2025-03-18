<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

use Vgrish\MindBox\MS2\App;
use Vgrish\MindBox\MS2\WorkerResult;
use Vgrish\MindBox\MS2\Workers\Nomenclature\ViewCategory;

/** @var modX $modx */
$modx->initialize();
$app = new App($modx);

$c = $modx->newQuery(\msCategory::class, ['published' => 1]);

if ($modx->resource = $modx->getObject(msCategory::class, $c)) {
    $worker = new ViewCategory($app, [
    ]);

    return $worker->run();
}

return new WorkerResult(false);
