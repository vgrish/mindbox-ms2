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
class FloatCaster
{
    public function __construct(private int|string $precision = 2)
    {
    }

    public function normalize(null|float|int|string $value): ?string
    {
        if (null === $value) {
            return null;
        }

        return (string) \round($value, $this->precision);
    }
}
