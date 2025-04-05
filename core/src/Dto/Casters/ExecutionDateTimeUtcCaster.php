<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Dto\Casters;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
#[\CuyZ\Valinor\Normalizer\AsTransformer()]
class ExecutionDateTimeUtcCaster
{
    public function normalize(mixed $value): ?string
    {
        if (null === $value) {
            $microtime = \microtime(true);
            $seconds = (int) $microtime;
            $milliseconds = (int) (($microtime - $seconds) * 1000);

            $value = \gmdate('d.m.Y H:i:s', $seconds) . '.' . \sprintf('%03d', $milliseconds);
        }

        return $value;
    }
}
