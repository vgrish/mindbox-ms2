<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Workers\Subscriptions;

use Vgrish\MindBox\MS2\Dto\Data\Subscriptions\SubscriptionInFooterDataDto;
use Vgrish\MindBox\MS2\Worker;
use Vgrish\MindBox\MS2\WorkerResult;
use Vgrish\MindBox\MS2\Workers\Traits\GetCustomerDataTrait;

class SubscriptionInFooter extends Worker
{
    use GetCustomerDataTrait;
    protected static string $operation = 'Website.SubscriptionInFooter';
    protected static bool $isAsyncOperation = true;
    protected static bool $isClientRequired = true;

    public function process(): WorkerResult
    {
        $params = $this->event->params;

        $email = \mb_strtolower(\trim((string) ($params['email'] ?? '')), 'utf-8');

        if (!\preg_match('/^\S+@\S+[.]\S+$/', $email)) {
            return $this->error();
        }

        $ctx = (string) $this->modx?->context?->get('key');

        if ($this->modx->user->isAuthenticated($ctx)) {
            $user = $this->modx->user;
        } else {
            $user = $this->modx->newObject(\modUser::class);
        }

        $data = $this->formatData(
            SubscriptionInFooterDataDto::class,
            \array_replace_recursive(
                $this->getCustomerData($user),
                ['customer' => ['email' => $email, 'ids' => ['websiteID' => ' ']]],
            ),
        );

        return $this->success($data);
    }
}
