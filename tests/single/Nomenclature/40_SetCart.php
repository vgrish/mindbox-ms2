<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

use Vgrish\MindBox\MS2\App;
use Vgrish\MindBox\MS2\WorkerResult;
use Vgrish\MindBox\MS2\Workers\Nomenclature\SetCart;

/** @var modX $modx */
$modx->initialize();
$app = new App($modx);
$miniShop2 = $modx->getService('minishop2');
$miniShop2->initialize('web', ['json_response' => true]);

$c = $modx->newQuery(\msProduct::class, ['published' => 1]);

if ($modx->resource = $modx->getObject(msProduct::class, $c)) {
    /** @var msCartHandler $cart */
    $cart = $miniShop2->cart;
    $cart->clean();
    $cart->add($modx->resource->get('id'), 1, ['size' => 'L']);

    $worker = new SetCart($app, [
        'cart' => $cart,
    ]);

    return $worker->run();
}

return new WorkerResult(false);
