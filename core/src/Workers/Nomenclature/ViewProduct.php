<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Workers\Nomenclature;

use Vgrish\MindBox\MS2\Dto\Data\Nomenclature\ViewProductDataDto;
use Vgrish\MindBox\MS2\Tools\Extensions;
use Vgrish\MindBox\MS2\Worker;
use Vgrish\MindBox\MS2\WorkerResult;

class ViewProduct extends Worker
{
    protected static string $operation = 'Website.ViewProduct';
    protected static bool $isAsyncOperation = true;
    protected static bool $isClientRequired = true;

    public function process(): WorkerResult
    {
        if ($this->isClientBot()) {
            return $this->error();
        }

        $resource = $this->modx?->resource;

        if (!$this->isResourceAvailable($resource)) {
            return $this->error();
        }

        if (!$this->isResourceProduct($resource)) {
            return $this->error();
        }

        // NOTE: если установлен модуль msOptionsPrice и есть модификации, то не генерируем событие просмотра товара
        if (Extensions::isExist('msOptionsPrice') && $this->modx->getCount(\msopModification::class, ['rid' => $resource->get('id')])) {
            return $this->error();
        }

        if ($websiteId = $this->app->getNomenclatureWebsiteId($resource)) {
            $data = [
                'viewProduct' => [
                    'product' => [
                        'ids' => [
                            'website' => $websiteId,
                        ],
                    ],
                ],
            ];

            $data = $this->formatData(ViewProductDataDto::class, $data);

            return $this->success($data);
        }

        return $this->error();
    }
}
