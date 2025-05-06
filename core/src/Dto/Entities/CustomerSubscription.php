<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Dto\Entities;

class CustomerSubscription
{
    public const POINT_OF_CONTACT_EMAIL = 'Email';
    public const POINT_OF_CONTACT_SMS = 'SMS';
    public const POINT_OF_CONTACT_VIBER = 'Viber';
    public const POINT_OF_CONTACT_WEBPUSH = 'Webpush';
    public const POINT_OF_CONTACT_MOBILEPUSH = 'Mobilepush';

    #[\CuyZ\Valinor\Mapper\Object\Constructor()]
    public function __construct(
        /**
         * @var null|string ```<Системное имя бренда подписки клиента>```
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\StringCaster()]
        public ?string $brand = null,
        /**
         * @var string ```<Системное имя канала подписки: Email, SMS, Viber, Webpush, Mobilepush>```
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\StringCaster()]
        public string $pointOfContact = self::POINT_OF_CONTACT_EMAIL,
        /**
         * @var null|string ```<Внешний идентификатор тематики подписки>```
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\StringCaster()]
        public ?string $topic = null,
    ) {
    }
}
