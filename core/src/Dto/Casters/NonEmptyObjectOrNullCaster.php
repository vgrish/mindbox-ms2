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
class NonEmptyObjectOrNullCaster
{
    public function normalize(mixed $value): null|array|object
    {
        if (\is_array($value)) {
            if (empty(\array_filter($value))) {
                return null;
            }

            return $value;
        }

        if (\is_object($value)) {
            if (empty(\array_filter(\json_decode(\json_encode($value), true)))) {
                return null;
            }

            return $value;
        }

        return null;
    }
}
