<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Dto\Entities;

class OrderDto
{
    #[\CuyZ\Valinor\Mapper\Object\Constructor()]
    public function __construct(
        /**
         * null|float|string ```<Стоимость доставки заказа>```.
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\FloatCaster()]
        public null|float|string $deliveryCost,
        /**
         * null|float|string ```<Итоговая сумма, полученная от клиента. Должна учитывать возвраты и отмены. Используется для подсчета среднего чека>```.
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\FloatCaster()]
        public null|float|string $totalPrice,
        /**
         * @var null|string ```<Email>```
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\EmailCaster()]
        public ?string $email,
        /**
         * @var null|string ```<Мобильный телефон>```
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\MobilePhoneCaster()]
        public ?string $mobilePhone,
        /**
         * @var OrderIdDto ```<Идентификатор заказа на сайте>```
         */
        public OrderIdDto $ids,
        /**
         * @var null|array ```<Кастомные поля>```
         */
        public ?array $customFields,
        /**
         * @var null|list<OrderLineDto> ```<Продукты заказа>```
         */
        public ?array $lines,
        /**
         * @var null|list<OrderDiscountPromoCodeDto> ```<Скидки заказа>```
         */
        public ?array $discounts,
        /**
         * @var null|string
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        public ?string $phone,
    ) {
    }

    #[\CuyZ\Valinor\Mapper\Object\Constructor()]
    public static function createFrom($data): self
    {
        return new self(...$data);
    }
}
