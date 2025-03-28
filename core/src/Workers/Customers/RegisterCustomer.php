<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Workers\Customers;

use Vgrish\MindBox\MS2\Dto\Data\Customers\RegisterCustomerDataDto;
use Vgrish\MindBox\MS2\Worker;
use Vgrish\MindBox\MS2\WorkerResult;
use Vgrish\MindBox\MS2\Workers\Traits\GetCustomerDataTrait;

class RegisterCustomer extends Worker
{
    use GetCustomerDataTrait;
    protected static string $operation = 'Website.RegisterCustomer';
    protected static bool $isAsyncOperation = false;
    protected static bool $isClientRequired = true;

    public function process(): WorkerResult
    {
        $params = $this->event->params;
        $user = $params['user'] ?? null;
        $mode = $params['mode'] ?? null;

        if (\modSystemEvent::MODE_NEW !== $mode) {
            return $this->error();
        }

        if (\is_a($user, \modUser::class)) {
            $data = $this->formatData(RegisterCustomerDataDto::class, $this->getCustomerData($user));

            return $this->success($data);
        }

        return $this->error();
    }
}
