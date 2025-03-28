<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Workers\Customers;

use Vgrish\MindBox\MS2\Dto\Data\Customers\EditCustomerDataDto;
use Vgrish\MindBox\MS2\Worker;
use Vgrish\MindBox\MS2\WorkerResult;
use Vgrish\MindBox\MS2\Workers\Traits\GetCustomerDataTrait;

class EditCustomer extends Worker
{
    use GetCustomerDataTrait;
    protected static string $operation = 'Website.EditCustomer';
    protected static bool $isAsyncOperation = false;
    protected static bool $isClientRequired = true;

    public function process(): WorkerResult
    {
        $params = $this->event->params;
        $user = $params['user'] ?? null;

        if (\is_a($user, \modUser::class)) {
            $data = $this->formatData(EditCustomerDataDto::class, $this->getCustomerData($user));

            return $this->success($data);
        }

        return $this->error();
    }
}
