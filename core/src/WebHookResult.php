<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2;

class WebHookResult
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

    public static function merge(array $results): self
    {
        if (empty($results) || !\array_reduce(
            $results,
            static fn ($acc, $item) => $acc && $item instanceof self,
            true,
        )) {
            throw new \InvalidArgumentException('All items must be WebHookResult');
        }

        $success = \array_reduce(
            $results,
            static fn ($acc, $result) => $acc && $result->success,
            true,
        );

        $data = \array_merge(
            ...\array_map(static fn ($result) => $result->data, $results),
        );

        return new self($success, $data);
    }
}
