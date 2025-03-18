<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Workers\Nomenclature;

use Vgrish\MindBox\MS2\Dto\Data\Nomenclature\ViewProductDataDto;
use Vgrish\MindBox\MS2\Worker;
use Vgrish\MindBox\MS2\WorkerResult;

class ViewProduct extends Worker
{
    protected static string $operation = 'Website.ViewProduct';
    protected static bool $isAsyncOperation = true;
    protected static bool $isClientRequired = true;

    public function process(): WorkerResult
    {
        $resource = $this->modx?->resource;

        if (\is_object($resource) && \is_a($resource, \msProduct::class)) {
            $data = [
                'viewProduct' => [
                    'product' => [
                        'ids' => [
                            'website' => $resource->get('id'),
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
