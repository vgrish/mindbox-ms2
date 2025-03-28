<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Dto\Entities\Feeds;

class Category
{
    #[\CuyZ\Valinor\Mapper\Object\Constructor()]
    public function __construct(
        #[\Vgrish\MindBox\MS2\Dto\Casters\StringCaster()]
        public int|string $id,
        /**
         * @var null|int|non-empty-string ```<Идентификатор родителя на сайте>```
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\StringCaster()]
        public null|int|string $parent,
        /**
         * @var non-empty-string ```<Название на сайте>```
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\StringCaster()]
        public string $pagetitle,
        /**
         * @var int|string
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        public int|string $websiteId,
        /**
         * @var null|int|string
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        public null|int|string $parentWebsiteId,
    ) {
        $this->id = $this->websiteId;
        $this->parent = $this->parentWebsiteId;
    }
}
