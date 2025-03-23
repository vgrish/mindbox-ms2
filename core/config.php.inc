<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

use Vgrish\MindBox\MS2\App;
use Vgrish\MindBox\MS2\Webhooks;
use Vgrish\MindBox\MS2\Workers;

return [
    App::WORKERS => [
        'OnWebPagePrerender' => [
            Workers\Nomenclature\ViewCategory::class,
            Workers\Nomenclature\ViewProduct::class,
        ],
        'OnWebLogin' => [
            Workers\Customers\AuthorizeCustomer::class,
        ],
        'OnUserSave' => [
            Workers\Customers\RegisterCustomer::class,
        ],
        'OnUserFormSave' => [
            Workers\Customers\EditCustomer::class,
        ],
        'msOnAddToCart' => [
            Workers\Nomenclature\SetCart::class,
        ],
        'msOnChangeInCart' => [
            Workers\Nomenclature\SetCart::class,
            Workers\Nomenclature\ClearCart::class,
        ],
        'msOnRemoveFromCart' => [
            Workers\Nomenclature\SetCart::class,
            Workers\Nomenclature\ClearCart::class,
        ],
        'msOnEmptyCart' => [
            Workers\Nomenclature\ClearCart::class,
        ],
        'msOnSaveOrder' => [
            Workers\Orders\CreateAuthorizedOrder::class,
            Workers\Orders\CreateUnauthorizedOrder::class,
            Workers\Orders\UpdateOrder::class,
        ],
        'msOnChangeOrderStatus' => [
            Workers\Orders\UpdateOrderStatus::class,
        ],
        'msFavoritesOnProcessFavorites' => [
            Workers\Nomenclature\AddToWishList::class,
            Workers\Nomenclature\RemoveFromWishList::class,
            Workers\Nomenclature\ClearWishList::class,
        ],
    ],

    App::WEBHOOKS => [
        'GetPromoCodeOnFirstOrder' => Webhooks\PromoCodes\GetPromoCodeOnFirstOrder::class,
    ],
];
