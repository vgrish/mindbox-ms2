<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Workers\Orders;

use Vgrish\MindBox\MS2\Dto\Data\Orders\UpdateOrderStatusDataDto;
use Vgrish\MindBox\MS2\Worker;
use Vgrish\MindBox\MS2\WorkerResult;

class UpdateOrderStatus extends Worker
{
    protected static string $operation = 'Website.UpdateOrderStatus';
    protected static bool $isAsyncOperation = false;
    protected static bool $isClientRequired = false;

    public function process(): WorkerResult
    {
        $params = $this->event->params;
        $status = (int) ($params['status'] ?? 0);

        if (empty($status)) {
            return $this->error();
        }

        $msOrder = $params['order'] ?? null;

        if (\is_a($msOrder, \msOrder::class)) {
            $data = [
                'orderLinesStatus' => $status,
                'order' => [
                    'ids' => [
                        'websiteID' => $msOrder->get('id'),
                    ],
                ],
            ];

            $data = $this->formatData(UpdateOrderStatusDataDto::class, $data);

            return $this->success($data);
        }

        return $this->error();
    }
}
