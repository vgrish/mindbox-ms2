<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Dto\Entities\Feeds;

class OfferDto
{
    #[\CuyZ\Valinor\Mapper\Object\Constructor()]
    public function __construct(
        #[\Vgrish\MindBox\MS2\Dto\Casters\StringCaster()]
        public int|string $id,
        /**
         * @var null|int|non-empty-string ```<Идентификатор родителя на сайте>```
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\StringCaster()]
        public null|int|string $categoryId,
        #[\Vgrish\MindBox\MS2\Dto\Casters\StringCaster()]
        public ?bool $available,
        /**
         * @var null|int|non-empty-string ```<Название на сайте>```
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\StringCaster()]
        public null|int|string $name,
        #[\Vgrish\MindBox\MS2\Dto\Casters\StringCaster()]
        public null|int|string $vendor,
        #[\Vgrish\MindBox\MS2\Dto\Casters\StringCaster()]
        public null|int|string $vendorCode,
        #[\Vgrish\MindBox\MS2\Dto\Casters\FloatCaster()]
        public null|float|int|string $price,
        #[\Vgrish\MindBox\MS2\Dto\Casters\StringCaster()]
        public null|float|int|string $oldprice,
        #[\Vgrish\MindBox\MS2\Dto\Casters\StringCaster()]
        public ?string $picture,
        #[\Vgrish\MindBox\MS2\Dto\Casters\StringCaster()]
        public ?string $url,
        #[\Vgrish\MindBox\MS2\Dto\Casters\NonEmptyObjectOrNullCaster()]
        public ?array $options,
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        public int|string $websiteId,
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        public null|int|string $pagetitle,
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        public null|int|string $longtitle,
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        public null|int|string $parentWebsiteId,
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        public string $baseUrl,
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        public ?string $image,
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        public ?string $uri,
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        public null|int|string $count,
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        public null|int|string $article,
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        public null|float|int|string $old_price,
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        public null|int|string $vendorName,
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        public null|int|string $active,
    ) {
        $this->id = $this->websiteId;
        $this->categoryId = $this->parentWebsiteId;

        if (null === $this->available) {
            $this->available = !empty($this->count) && ('0' !== $this->count);

            if (empty($this->active)) {
                $this->available = false;
            }
        }

        if (null === $this->name) {
            $this->name = $this->pagetitle;

            if (!empty($this->longtitle)) {
                $this->name = $this->longtitle;
            }
        }

        if (null === $this->oldprice) {
            $this->oldprice = (int) $this->old_price;
        }

        if ((int) $this->price > $this->oldprice) {
            $this->oldprice = null;
        }

        if (empty($this->oldprice)) {
            $this->oldprice = null;
        }

        if (null === $this->vendorCode) {
            $this->vendorCode = $this->article;
        }

        if (!empty($this->vendorName)) {
            $this->vendor = $this->vendorName;
        }

        if (!empty($this->image)) {
            $this->picture = $this->baseUrl . '/' . \trim($this->image, '/');
        }

        if (!empty($this->uri)) {
            $this->url = $this->baseUrl . '/' . \ltrim($this->uri, '/');
        }
    }
}
