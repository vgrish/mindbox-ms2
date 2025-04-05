<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Workers\Traits;

trait GetCustomerByEmailTrait
{
    public function getCustomerByEmail(string $email): ?\modUser
    {
        $email = \mb_strtolower(\trim($email), 'utf-8');

        if (!\preg_match('/^\S+@\S+[.]\S+$/', $email)) {
            return null;
        }

        $c = $this->modx->newQuery(\modUser::class);
        $c->leftJoin(\modUserProfile::class, 'Profile');

        $filter = [
            'LOWER(username) = ' . $this->modx->quote($email),
            'OR LOWER(Profile.email) = ' . $this->modx->quote($email),
        ];
        $c->where(\implode(' ', $filter));
        $c->select('modUser.id');

        if (!$customer = $this->modx->getObject(\modUser::class, $c)) {
            $customer = $this->modx->newObject(\modUser::class, [
                'username' => $email,
                'password' => \md5((string) \mt_rand()),
            ]);
            $profile = $this->modx->newObject(\modUserProfile::class, [
                'email' => $email,
                'fullname' => $email,
            ]);
            $customer->addOne($profile);

            if ($customer->save()) {
                $groupRoles = \array_filter(
                    \array_map('trim', \explode(',', $this->modx->getOption('ms2_order_user_groups', null))),
                );

                foreach ($groupRoles as $groupRole) {
                    $groupRole = \explode(':', $groupRole);

                    if (\count($groupRole) > 1 && !empty($groupRole[1])) {
                        if (\is_numeric($groupRole[1])) {
                            $roleId = (int) $groupRole[1];
                        } else {
                            $roleId = $groupRole[1];
                        }
                    } else {
                        $roleId = null;
                    }

                    $customer->joinGroup($groupRole[0], $roleId);
                }
            } else {
                $customer = null;
            }
        }

        return \is_a($customer, \modUser::class) ? $customer : null;
    }
}
