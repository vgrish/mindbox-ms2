<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Dto;

class EventDto
{
    #[\CuyZ\Valinor\Mapper\Object\Constructor()]
    public function __construct(
        /** @var positive-int */
        public int $id,
        /** @var non-empty-string */
        public string $operation,
        public bool $is_async_operation,
        /** @var non-empty-string */
        public string $context_key,
        public string $client_uuid,
        public string $client_ip,
        public ?bool $sended,
        public ?bool $rejected,
        public mixed $created_at,
        public mixed $updated_at,
        public mixed $sended_at,
        #[\Vgrish\MindBox\MS2\Dto\Casters\EventDataCaster()]
        public array|string $data,
    ) {
        if (!isset($this->sended)) {
            $this->sended = false;
        }

        if (!isset($this->rejected)) {
            $this->rejected = false;
        }
    }
}
