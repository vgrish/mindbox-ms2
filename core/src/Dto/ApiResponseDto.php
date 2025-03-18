<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Dto;

class ApiResponseDto
{
    #[\CuyZ\Valinor\Mapper\Object\Constructor()]
    public function __construct(
        public ?string $status,
        public ?string $errorMessage,
        public ?bool $success,
    ) {
        if (!isset($this->status)) {
            $this->success = false;
        } else {
            if ('Success' === $this->status) {
                $this->success = true;
            }
        }
    }
}
