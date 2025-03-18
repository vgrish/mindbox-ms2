<?php

declare(strict_types=1);

use Vgrish\MindBox\MS2\Workers;

$events = [
    'OnWebPagePrerender' => [
        Workers\Nomenclature\ViewCategory::class,
        Workers\Nomenclature\ViewProduct::class,
    ],
    'OnWebLogin' => [
        Workers\Customers\AuthorizeCustomer::class,
    ],
    'OnUserActivate' => [
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
    ]
];

return $events;