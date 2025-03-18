<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Dto\Entities;

class CartProductListDto
{
    /**
     * @var float|string ```<Количество продуктов>```
     */
    #[\Vgrish\MindBox\MS2\Dto\Casters\FloatCaster()]
    public float|string $count;

    /**
     * @var float|string ```<Цена за единицу>```
     */
    #[\Vgrish\MindBox\MS2\Dto\Casters\FloatCaster()]
    public float|string $pricePerItem;

    /**
     * @var array{ids: ProductIdDto} ```<Идентификатор на сайте>```
     */
    public array $product;
}
