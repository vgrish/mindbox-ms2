<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Workers\Nomenclature;

use Vgrish\MindBox\MS2\Worker;
use Vgrish\MindBox\MS2\WorkerResult;

class ClearWishList extends Worker
{
    protected static string $operation = 'Website.ClearWishList';
    protected static bool $isAsyncOperation = false;
    protected static bool $isClientRequired = true;

    public function process(): WorkerResult
    {
        $params = $this->event->params;
        $props = $params['props'] ?? [];
        $method = (string) ($props['method'] ?? '');

        if ('clear' !== $method) {
            return $this->error();
        }

        return $this->success();
    }
}
