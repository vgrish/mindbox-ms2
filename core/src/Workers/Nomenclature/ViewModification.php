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

class ViewModification extends Worker
{
    protected static string $operation = 'Website.ViewProduct';
    protected static bool $isAsyncOperation = true;
    protected static bool $isClientRequired = true;

    public function process(): WorkerResult
    {
        if ($this->isClientBot()) {
            return $this->error();
        }

        $params = $this->event->params;
        $data = $params['data'] ?? [];

        if (!($rid = (int) ($data['rid'] ?? 0)) || !($modification = $data['modification'] ?? [])) {
            return $this->error();
        }

        if ($websiteId = $this->app->getNomenclatureWebsiteId($rid, $modification)) {
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
