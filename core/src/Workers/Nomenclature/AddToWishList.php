<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Workers\Nomenclature;

use Vgrish\MindBox\MS2\Dto\Data\Nomenclature\AddToWishListDto;
use Vgrish\MindBox\MS2\Worker;
use Vgrish\MindBox\MS2\WorkerResult;

class AddToWishList extends Worker
{
    protected static string $operation = 'Website.AddToWishList';
    protected static bool $isAsyncOperation = false;
    protected static bool $isClientRequired = true;

    public function process(): WorkerResult
    {
        $params = $this->event->params;
        $props = $params['props'] ?? [];
        $method = (string) ($props['method'] ?? '');

        if ('add' !== $method || !($id = (int) ($props['key'] ?? 0))) {
            return $this->error();
        }

        /** @var \msProduct $resource */
        $resource = $this->modx->getObject(\msProduct::class, $id);

        if (!$this->isResourceAvailable($resource)) {
            return $this->error();
        }

        if ($websiteId = $this->app->getNomenclatureWebsiteId($resource)) {
            $data = [
                'addProductToList' => [
                    'pricePerItem' => $resource->getPrice(),
                    'product' => [
                        'ids' => [
                            'website' => $websiteId,
                        ],
                    ],
                ],
            ];

            $data = $this->formatData(AddToWishListDto::class, $data);

            return $this->success($data);
        }

        return $this->error();
    }
}
