<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Dto\Entities;

class OrderLineDto
{
    #[\CuyZ\Valinor\Mapper\Object\Constructor()]
    public function __construct(
        /**
         * @var float|string ```<Базовая цена продукта за единицу продукта>```
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\FloatCaster()]
        public float|string $basePricePerItem,
        /**
         * @var float|string ```<Количество SKU (или продукта, если SKU не передан)>```
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\FloatCaster()]
        public float|string $quantity,
        /**
         * @var null|string ```<Тип количества (int или double)>```
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\QuantityTypeCaster()]
        public string $quantityType,
        /**
         * @var null|float|string ```<Конечная цена за всю линию чека с учетом всех скидок>```
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\FloatCaster()]
        public null|float|string $discountedPricePerLine,
        /**
         * @var null|int|string ```<Статус позиции заказа>```
         */
        public null|int|string $status,
        /**
         * @var OrderLineIdDto ```<Идентификатор продукта на сайте>```
         */
        public OrderLineIdDto $product,
        /**
         * @var null|list<OrderLineDiscountExternalPromoActionDto|OrderLineDiscountPromoCodeDto>
         */
        public ?array $discounts,
    ) {
    }
}
