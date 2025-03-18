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
class EmailCaster
{
    public function normalize(null|int|string $value): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!\preg_match('/^\S+@\S+[.]\S+$/', $value)) {
            return null;
        }

        return \trim((string) $value);
    }
}
