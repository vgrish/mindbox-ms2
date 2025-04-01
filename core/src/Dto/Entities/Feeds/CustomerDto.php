<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Dto\Entities\Feeds;

class CustomerDto
{
    #[\CuyZ\Valinor\Mapper\Object\Constructor()]
    public function __construct(
        #[\Vgrish\MindBox\MS2\Dto\Casters\StringCaster()]
        public int|string $ExternalIdentityWebsiteID,
        /**
         * @var null|\DateTimeImmutable|string ```<Дата рождения>```
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\BirthDateCaster()]
        public null|\DateTimeImmutable|string $BirthDate,
        /**
         * @var null|string ```<Пол>```
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\SexCaster()]
        public ?string $Sex,
        /**
         * @var null|string ```<Фамилия>```
         */
        public ?string $LastName,
        /**
         * @var null|string ```<Имя>```
         */
        public ?string $FirstName,
        /**
         * @var null|string ```<Отчество>```
         */
        public ?string $MiddleName,
        /**
         * @var null|string ```<ФИО>```
         */
        public ?string $FullName,
        /**
         * @var null|string ```<Email>```
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\EmailCaster()]
        public ?string $Email,
        /**
         * @var null|int|string ```<Мобильный телефон>```
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\MobilePhoneCaster()]
        public null|int|string $MobilePhone,
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        protected ?array $extended,
        /**
         * @var null|int|string
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        protected null|int|string $id,
        /**
         * @var null|int|string
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        protected null|int|string $active,
        /**
         * @var null|int|string
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        protected null|int|string $blocked,
        /**
         * @var null|int|string
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        protected null|int|string $username,
        /**
         * @var null|int|string
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        protected null|int|string $fullname,
        /**
         * @var null|int|string
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        protected null|int|string $email,
        /**
         * @var null|int|string
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        protected null|int|string $phone,
        /**
         * @var null|int|string
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        protected null|int|string $mobilephone,
        /**
         * @var null|int|string
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        protected null|int|string $dob,
        /**
         * @var null|int|string
         */
        #[\Vgrish\MindBox\MS2\Dto\Casters\HiddenValue()]
        protected null|int|string $gender,
    ) {
        $this->Sex = match ($this->gender) {
            1 => 'male',
            2 => 'female',
            default => null,
        };

        $this->FullName = $this->username;

        if (!empty($this->LastName) || !empty($this->FirstName) || !empty($this->MiddleName)) {
            $this->FullName = null;
        }

        $this->Email = $this->email;

        if (empty($this->MobilePhone) && !empty($this->phone)) {
            $this->MobilePhone = $this->phone;
        }

        if (empty($this->MobilePhone) && !empty($this->mobilephone)) {
            $this->MobilePhone = $this->mobilephone;
        }
    }
}
