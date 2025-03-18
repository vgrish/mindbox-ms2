<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Http;

use Vgrish\MindBox\MS2\Enums\HttpMethod;

class Payload
{
    public function __construct(
        public readonly HttpMethod $method,
        public readonly array $path,
        public readonly array $params,
        public readonly mixed $body,
    ) {
    }
}
