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

class RegisterCustomer extends Worker
{
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

        if (\is_object($user) && \is_a($user, \modUser::class)) {
            if (!$profile = $user->getOne('Profile')) {
                $profile = $this->modx->newObject(\modUserProfile::class);
            }

            $data = [
                'customer' => [
                    'sex' => match ($profile->get('gender')) {
                        1 => 'male',
                        2 => 'female',
                        default => null,
                    },
                    'fullName' => $profile->get('fullname'),
                    'email' => $profile->get('email'),
                    'mobilePhone' => $profile->get('mobilephone'),

                    'ids' => [
                        'websiteID' => $user->get('id'),
                    ],
                    'phone' => $profile->get('phone'),
                ],
            ];

            $data = $this->formatData(RegisterCustomerDataDto::class, $data);

            return $this->success($data);
        }

        return $this->error();
    }
}
