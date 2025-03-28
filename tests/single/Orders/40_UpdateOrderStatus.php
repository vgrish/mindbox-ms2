<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

use Vgrish\MindBox\MS2\App;
use Vgrish\MindBox\MS2\WorkerResult;
use Vgrish\MindBox\MS2\Workers\Orders\UpdateOrderStatus;

/** @var modX $modx */
if ($user = getUser()) {
    $profile = $user->getOne('Profile');
}

$app = new App($modx);
$miniShop2 = $modx->getService('minishop2');
$miniShop2->initialize('web');

if ($user) {
    if (!$msOrder = $modx->getObject(
        \msOrder::class,
        $modx->newQuery(\msOrder::class, [
            'user_id' => $user->get('id'),
        ]),
    )) {
        /** @var msCartHandler $cart */
        $cart = $miniShop2->cart;
        $cart->clean();

        if ($resource = $modx->getObject(msProduct::class, $modx->newQuery(\msProduct::class, ['published' => 1]))) {
            $cart->add($resource->get('id'), 1, ['size' => 'L']);
        }

        /** @var msOrderHandler $order */
        $order = $miniShop2->order;
        $order->set([
            'delivery' => $modx->getObject(
                \msDelivery::class,
                $modx->newQuery(\msDelivery::class, ['active' => true]),
            )?->get('id'),
            'payment' => $modx->getObject(
                \msPayment::class,
                $modx->newQuery(\msPayment::class, ['active' => true]),
            )?->get('id'),
            'email' => $profile->get('email'),
            'phone' => $profile->get('phone'),
            'extfld_lastname' => $user->get('username'),
            'extfld_name' => $user->get('username'),
        ]);

        if ($response = $order->submit()) {
            $response = \is_array($response) ? $response : \json_decode($response, true);
        }

        if ($response['success'] ?? false) {
            $msOrder = $modx->getObject(\msOrder::class, (int) $response['data']['msorder']);
        } else {
            $modx->log(\modX::LOG_LEVEL_ERROR, \var_export($response, true));
        }
    }

    $worker = new UpdateOrderStatus($app, [
        'order' => $msOrder,
        'status' => 3,
    ]);

    return $worker->run();
}

return new WorkerResult(false);
