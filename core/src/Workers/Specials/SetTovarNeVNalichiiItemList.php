<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Workers\Specials;

use Vgrish\MindBox\MS2\Dto\Data\Specials\SetTovarNeVNalichiiItemListDataDto;
use Vgrish\MindBox\MS2\Worker;
use Vgrish\MindBox\MS2\WorkerResult;
use Vgrish\MindBox\MS2\Workers\Traits\GetCustomerDataTrait;

class SetTovarNeVNalichiiItemList extends Worker
{
    use GetCustomerDataTrait;
    protected static string $operation = 'SetTovarNeVNalichiiItemList';
    protected static bool $isAsyncOperation = true;
    protected static bool $isClientRequired = true;

    public function process(): WorkerResult
    {
        $params = $this->event->params;

        $email = \mb_strtolower(\trim((string) ($params['email'] ?? '')), 'utf-8');

        if (!\preg_match('/^\S+@\S+[.]\S+$/', $email)) {
            return $this->error();
        }

        if (!($rid = (int) ($params['rid'] ?? 0)) || !($modification = $params['modification'] ?? [])) {
            return $this->error();
        }

        /** @var \msProduct $resource */
        $resource = $this->modx->getObject(\msProduct::class, $rid);

        if (!$this->isResourceAvailable($resource)) {
            return $this->error();
        }

        if ($websiteId = $this->app->getNomenclatureWebsiteId($resource, $modification)) {
            $ctx = (string) $this->modx?->context?->get('key');

            if ($this->modx->user->isAuthenticated($ctx)) {
                $user = $this->modx->user;
            } else {
                $user = $this->modx->newObject(\modUser::class);
            }

            $data = \array_replace_recursive(
                $this->getCustomerData($user),
                ['customer' => ['email' => $email, 'ids' => ['websiteID' => ' ']]],
            ) + [
                'productList' => [
                    [
                        'count' => 1,
                        'pricePerItem' => $resource->getPrice(),
                        'product' => [
                            'ids' => [
                                'website' => $websiteId,
                            ],
                        ],
                    ],
                ],
            ];

            $data = $this->formatData(SetTovarNeVNalichiiItemListDataDto::class, $data);

            return $this->success($data);
        }

        return $this->error();
    }
}
