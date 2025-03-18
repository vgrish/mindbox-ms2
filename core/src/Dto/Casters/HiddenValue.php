<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Dto\Casters;

use Vgrish\MindBox\MS2\Dto\Entities\HiddenValueDto;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
#[\CuyZ\Valinor\Normalizer\AsTransformer()]
class HiddenValue
{
    public function normalize(mixed $value): HiddenValueDto
    {
        return new HiddenValueDto();
    }
}
