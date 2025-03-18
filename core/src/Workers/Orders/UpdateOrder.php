<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Workers\Orders;

use Vgrish\MindBox\MS2\Dto\Data\Orders\UpdateOrderDataDto;
use Vgrish\MindBox\MS2\Worker;
use Vgrish\MindBox\MS2\WorkerResult;
use Vgrish\MindBox\MS2\Workers\Traits\GetOrderDataTrait;

class UpdateOrder extends Worker
{
    use GetOrderDataTrait;
    protected static string $operation = 'Website.UpdateOrder';
    protected static bool $isAsyncOperation = false;
    protected static bool $isClientRequired = false;

    public function process(): WorkerResult
    {
        $params = $this->event->params;
        $mode = $params['mode'] ?? null;

        if (\modSystemEvent::MODE_UPD !== $mode) {
            return $this->error();
        }

        $msOrder = $params['msOrder'] ?? null;

        if (\is_object($msOrder) && \is_a($msOrder, \msOrder::class)) {
            $data = $this->formatData(UpdateOrderDataDto::class, $this->getOrderData($msOrder));

            return $this->success($data);
        }

        return $this->error();
    }
}
