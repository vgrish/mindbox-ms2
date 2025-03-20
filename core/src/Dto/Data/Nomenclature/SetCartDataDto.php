<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Dto\Data\Nomenclature;

use Vgrish\MindBox\MS2\Dto\Entities\CartProductListDto;
use Vgrish\MindBox\MS2\Dto\Entities\CustomerCartDto;

class SetCartDataDto
{
    /**
     * @var list<CartProductListDto> ```<Продукты корзины>```
     */
    public array $productList;

    #[\Vgrish\MindBox\MS2\Dto\Casters\NonEmptyObjectOrNullCaster()]
    public CustomerCartDto $customer;
}
