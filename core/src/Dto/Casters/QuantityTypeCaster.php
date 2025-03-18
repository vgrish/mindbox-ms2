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
class QuantityTypeCaster
{
    public function normalize(mixed $value): ?string
    {
        if (null === $value) {
            return null;
        }

        return match (\mb_strtolower($value, 'utf-8')) {
            'int' => 'int',
            'double' => 'double',
            default => null,
        };
    }
}
