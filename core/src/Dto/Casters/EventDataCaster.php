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
class EventDataCaster
{
    public function normalize(mixed $value): ?string
    {
        if (null === $value) {
            return null;
        }

        if (\is_array($value)) {
            if (empty($value)) {
                $value = '{}';
            } else {
                $value = \json_encode($value, \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
            }
        }

        return $value;
    }
}
