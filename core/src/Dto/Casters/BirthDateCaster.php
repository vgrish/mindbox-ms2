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
class BirthDateCaster
{
    public function normalize(mixed $value): ?string
    {
        if (null === $value) {
            return null;
        }

        if (\is_object($value) && \is_a($value, \DateTimeImmutable::class)) {
            $value = $value->format('Y-m-d');
        }

        if (!\preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value)) {
            return null;
        }

        return \trim((string) $value);
    }
}
