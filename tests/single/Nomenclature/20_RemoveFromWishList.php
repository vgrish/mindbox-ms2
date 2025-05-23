<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

use Vgrish\MindBox\MS2\App;
use Vgrish\MindBox\MS2\WorkerResult;
use Vgrish\MindBox\MS2\Workers\Nomenclature\RemoveFromWishList;

/** @var modX $modx */
$modx->initialize();
$app = new App($modx);

$c = $modx->newQuery(\msProduct::class, ['published' => 1]);

if ($modx->resource = $modx->getObject(msProduct::class, $c)) {
    $worker = new RemoveFromWishList($app, [
        'props' => [
            'method' => 'remove',
            'key' => $modx->resource->get('id'),
        ],
    ]);

    return $worker->run();
}

return new WorkerResult(false);
