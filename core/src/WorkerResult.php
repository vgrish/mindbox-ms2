<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2;

class WorkerResult
{
    public function __construct(
        public readonly bool $success = false,
        public readonly array $data = [],
    ) {
    }

    public function __toString(): string
    {
        return \json_encode(
            [
                'success' => $this->success,
                'data' => $this->data,
            ],
            \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES,
        );
    }
}
