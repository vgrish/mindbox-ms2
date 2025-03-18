<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Workers\Nomenclature;

use Vgrish\MindBox\MS2\Dto\Data\Nomenclature\ViewCategoryDataDto;
use Vgrish\MindBox\MS2\Worker;
use Vgrish\MindBox\MS2\WorkerResult;

class ViewCategory extends Worker
{
    protected static string $operation = 'Website.ViewCategory';
    protected static bool $isAsyncOperation = true;
    protected static bool $isClientRequired = true;

    public function process(): WorkerResult
    {
        $resource = $this->modx?->resource;

        if (\is_object($resource) && !\is_a($resource, \msProduct::class)) {
            $data = [
                'viewProductCategory' => [
                    'productCategory' => [
                        'ids' => [
                            'website' => $resource->get('id'),
                        ],
                    ],
                ],
            ];

            $data = $this->formatData(ViewCategoryDataDto::class, $data);

            return $this->success($data);
        }

        return $this->error();
    }
}
