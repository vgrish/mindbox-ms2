<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Dto\Entities;

class OrderLineDiscountExternalPromoActionDto
{
    /**
     * @var non-empty-string ```<externalPromoAction>```
     */
    public string $type;

    /**
     * @var null|float|string ```<Размер скидки в рублях>```
     */
    #[\Vgrish\MindBox\MS2\Dto\Casters\FloatCaster()]
    public null|float|string $amount;

    /**
     * @var non-empty-list<OrderLineDiscountExternalPromoActionIdCodeDto> ```<Идентификатор промоакции>```
     */
    public array $externalPromoAction;
}
