<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Dto\Entities;

class CustomerDto
{
    #[\CuyZ\Valinor\Mapper\Object\Constructor()]
    public function __construct(
        /**
         * @var null|\DateTimeImmutable|string ```<Дата рождения>```
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\BirthDateCaster()]
        public null|\DateTimeImmutable|string $birthDate,
        /**
         * @var null|string ```<Пол>```
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\SexCaster()]
        public ?string $sex,
        /**
         * @var null|string ```<Фамилия>```
         */
        public ?string $lastName,
        /**
         * @var null|string ```<Имя>```
         */
        public ?string $firstName,
        /**
         * @var null|string ```<Отчество>```
         */
        public ?string $middleName,
        /**
         * @var null|string ```<ФИО>```
         */
        public ?string $fullName,
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
         * @var CustomerIdDto ```<Идентификатор на сайте>```
         */
        public CustomerIdDto $ids,
        /**
         * @var null|string
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        public ?string $phone,
        /**
         * @var null|string
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        public ?string $phone2,
    ) {
        if (empty($this->mobilePhone) && !empty($this->phone)) {
            $this->mobilePhone = $this->phone;
        }

        if (empty($this->mobilePhone) && !empty($this->phone2)) {
            $this->mobilePhone = $this->phone2;
        }

        if (!empty($this->lastName) || !empty($this->firstName) || !empty($this->middleName)) {
            $this->fullName = null;
        }
    }
}
