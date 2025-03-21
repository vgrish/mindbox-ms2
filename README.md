# mindbox-ms2

# Пакет интеграции сервиса mindbox для магазина MiniShop2 MODX Revolution V.2

Находится в разработке, версии могут не обладать обратной совместимостью. Список изменений можно найти
в [Changelog](CHANGELOG.md).

## Установка пакета
```
composer require vgrish/mindbox-ms2 --update-no-dev
composer exec mindbox-ms2 install
```

## Удаление пакета
```
composer exec mindbox-ms2 remove
composer remove vgrish/mindbox-ms2
```

## Особенности

### Настройки

* `api_endpoint_id` - Идентификатор точки интеграции api
* `api_secret_key` - Секретный ключ api
* `webhook_secret_key` - Секретный ключ webhook
* `development_mode` - Режим разработки. Включает вывод данных в журнал MODx, а так же включает отслеживание изменений DTO сущностей в реальном времени.

## Конфиг
Файл с указанием событий MODx и обработчиков. Расположен в папке пакета 
```
core/components/mindbox-ms2/config.php
```
Пример конфига

```
<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 *
 * @see https://github.com/vgrish/mindbox-ms2
 */

use Vgrish\MindBox\MS2\App;
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
        ],
    ],

    App::WEBHOOKS => [],
];

```

## Cron
Для передачи данных в сервис mindbox необходимо поставить файл на cron
```
core/components/mindbox-ms2/cli/cron.php
```

## Вызов событий из внешнего кода 
Обработчик события можно вызвать вручную, пример вызовов для работы со списком избранного

- Добавление в список избранного
```
/** @var Vgrish\MindBox\MS2\App $app */
if ($app = $modx->services[\Vgrish\MindBox\MS2\App::NAME] ?? null) {
    $worker = new \Vgrish\MindBox\MS2\Workers\Nomenclature\AddToWishList($app, [
        'props' => [
            'method' => 'add',
            'key' => '1', // идентификатор ресурса
        ],
    ]);
    $worker->run();
}
```

- Удаление из списка избранного
```
/** @var Vgrish\MindBox\MS2\App $app */
if ($app = $modx->services[\Vgrish\MindBox\MS2\App::NAME] ?? null) {
    $worker = new \Vgrish\MindBox\MS2\Workers\Nomenclature\RemoveFromWishList($app, [
        'props' => [
            'method' => 'remove',
            'key' => '1', // идентификатор ресурса
        ],
    ]);
    $worker->run();
}
```
- Очистка списка избранного
```
/** @var Vgrish\MindBox\MS2\App $app */
if ($app = $modx->services[\Vgrish\MindBox\MS2\App::NAME] ?? null) {
    $worker = new \Vgrish\MindBox\MS2\Workers\Nomenclature\ClearWishList($app, [
        'props' => [
            'method' => 'clear',
        ],
    ]);
    $worker->run();
}

```
