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
class MobilePhoneCaster
{
    public function normalize(null|int|string $value): ?string
    {
        if (null === $value) {
            return null;
        }

        $value = \preg_replace('/[^0-9]/iu', '', (string) $value);

        if (\mb_strlen($value) === 11) {
            if (\str_starts_with($value, '8')) {
                $value = '7' . \mb_substr($value, 1);
            }
        } elseif (\mb_strlen($value) === 10) {
            $value = '7' . $value;
        }

        if (\mb_strlen($value) < 11) {
            return null;
        }

        return \trim((string) $value);
    }
}
