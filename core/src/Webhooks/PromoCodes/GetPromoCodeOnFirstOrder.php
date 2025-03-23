<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Webhooks\PromoCodes;

use Vgrish\MindBox\MS2\WebHook;
use Vgrish\MindBox\MS2\WebHookResult;

class GetPromoCodeOnFirstOrder extends WebHook
{
    public function process(): WebHookResult
    {
        $email = $this->config['email'] ?? '';

        if (!\preg_match('/^\S+@\S+[.]\S+$/', $email)) {
            return $this->error();
        }

        $modx = $this->modx;

        if (!$user = $this->app->getCustomerByEmail($email)) {
            return $this->error();
        }

        if ($modx->getCount(\msOrder::class, ['user_id' => $user->get('id')])) {
            return $this->error();
        }

        $modx->newObject(\mspc2Coupon::class);

        if (!\class_exists(\mspc2Coupon::class)) {
            throw new \LogicException(\sprintf('%s the `%s` class does not exist', static::class, 'mspc2Coupon'));
        }

        $code = \sprintf('F-%05d-%s', $user->get('id'), \mb_substr(\str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4));

        $c = $modx->newQuery(\mspc2Coupon::class);
        $c->where([
            'code:LIKE' => \implode('-', \array_slice(\explode('-', $code, 3), 0, 2)) . '%',
        ]);

        if (!$coupon = $modx->getObject(\mspc2Coupon::class, $c)) {
            $coupon = $modx->newObject(\mspc2Coupon::class);
            $coupon->fromArray([
                // код промокода
                'code' => $code,
                // кол-во применений промокода
                'count' => 1,
                // скидка промокода
                'discount' => '5%',
                // описание промокода
                'description' => '',
                // дата начала действия промокода
                'startedon' => \time(),
                // дата окончания действия промокода
                'stoppedon' => 0,
                // Показывать предупреждения
                'showinfo' => true,
                // Скидка на всю корзину
                'allcart' => false,
                // На одну единицу товара
                'oneunit' => false,
                // Отображать цену со скидкой только в корзине.
                'onlycart' => true,
                // Не применять без скидки
                'unsetifnull' => false,
                // Применять только к товарам без старой цены.
                'oldprice' => true,
                // активный промокод
                'active' => true,
            ]);

            if (!$coupon->save()) {
                $coupon = null;
            }
        }

        if (!$coupon || !$coupon->get('active')) {
            return $this->error();
        }

        $data = [
            'user' => $user->get(['id', 'username']),
            'coupon' => $coupon->get(['id', 'list', 'code', 'count', 'discount']),
        ];

        return $this->success($data);
    }
}
