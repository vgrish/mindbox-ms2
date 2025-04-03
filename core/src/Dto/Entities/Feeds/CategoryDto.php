<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Dto\Entities\Feeds;

class CategoryDto
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
         * @var null|int|non-empty-string ```<Название на сайте>```
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\StringCaster()]
        public null|int|string $name,
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        public int|string $websiteId,
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        public null|int|string $parentWebsiteId,
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        public null|int|string $pagetitle,
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        public null|int|string $longtitle,
    ) {
        $this->id = $this->websiteId;
        $this->parent = $this->parentWebsiteId;

        if (null === $this->name) {
            $this->name = $this->pagetitle;

            if (!empty($this->longtitle)) {
                $this->name = $this->longtitle;
            }
        }
    }
}
