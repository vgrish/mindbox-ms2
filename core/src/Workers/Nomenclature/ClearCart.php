<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Workers\Nomenclature;

use Vgrish\MindBox\MS2\Worker;
use Vgrish\MindBox\MS2\WorkerResult;

class ClearCart extends Worker
{
    protected static string $operation = 'Website.ClearCart';
    protected static bool $isAsyncOperation = false;
    protected static bool $isClientRequired = true;

    public function process(): WorkerResult
    {
        $params = $this->event->params;
        $cart = $params['cart'] ?? null;

        if (!\is_a($cart, \msCartInterface::class)) {
            return $this->error();
        }

        if (empty($cart->get())) {
            return $this->success();
        }

        return $this->error();
    }
}
