<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Dto\Entities;

class CustomerIdDto
{
    /**
     * @var non-empty-string ```<Идентификатор на сайте>```
     */
    public string $websiteID;
}
