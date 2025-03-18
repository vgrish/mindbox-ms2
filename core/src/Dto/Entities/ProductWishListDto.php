<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Dto\Entities;

class ProductWishListDto
{
    /**
     * @var null|float|string ```<Цена за единицу>```
     */
    #[\Vgrish\MindBox\MS2\Dto\Casters\FloatCaster()]
    public null|float|string $pricePerItem;

    /**
     * @var array{ids: ProductIdDto} ```<Идентификатор на сайте>```
     */
    public array $product;
}
