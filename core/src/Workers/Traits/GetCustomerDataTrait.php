<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Workers\Traits;

trait GetCustomerDataTrait
{
    public function getCustomerData(\modUser $user): array
    {
        if (!$profile = $user->getOne('Profile')) {
            $profile = $user->xpdo->newObject(\modUserProfile::class);
        }

        return [
            'customer' => [
                'sex' => match ($profile->get('gender')) {
                    1 => 'male',
                    2 => 'female',
                    default => null,
                },
                'fullName' => $profile->get('fullname'),
                'email' => $profile->get('email'),
                'mobilePhone' => $profile->get('mobilephone'),

                'ids' => [
                    'websiteID' => $user->get('id'),
                ],
                'phone' => $profile->get('phone'),
            ],
        ];
    }
}
