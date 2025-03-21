<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Dto;

class WebHookDto
{
    #[\CuyZ\Valinor\Mapper\Object\Constructor()]
    public function __construct(
        /** @var positive-int */
        public int $id,
        /** @var non-empty-string */
        public string $operation,
        public string $transaction_id,
        public string $client_ip,
        #[\Vgrish\MindBox\MS2\Dto\Casters\EventDataCaster()]
        public array|string $data,
    ) {
    }
}
