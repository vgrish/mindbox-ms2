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

* `development_mode` - Режим разработки. Включает вывод данных в журнал MODx, а так же включает отслеживание изменений DTO сущностей в реальном времени.
* `api_endpoint_id` - Идентификатор точки интеграции api
* `api_secret_key` - Секретный ключ api
* `webhook_secret_key` - Секретный ключ webhook
* `bot_patterns` - Регистронезависимый список User-Agent ботов, разделитель "|". По умолчанию - "Yandex|Google|Yahoo|Rambler|Mail|Bot|Spider|Snoopy|Crawler|Finder|curl|Wget|Go-http-client|Postman"
* `nomenclature_website_key` - Ключ номенклатуры ресурсов
* `nomenclature_modification_website_key` - Ключ номенклатуры модификаций
* `nomenclature_website_key_separator` - Разделитель ключей номенклатуры. По умолчанию - "||"
* `nomenclature_category_templates` - Список шаблонов категорий номенклатуры
* `nomenclature_product_templates` - Список шаблонов продуктов номенклатуры


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
 * @see https://github.com/vgrish/mindbox-ms2
 */

use Vgrish\MindBox\MS2\Config;
use Vgrish\MindBox\MS2\Tools\Extensions;
use Vgrish\MindBox\MS2\Webhooks;
use Vgrish\MindBox\MS2\Workers;

$config = Config\Config::init();

$config = $config->withWorkers(
    Config\WorkersConfig::fromArray([
        'OnWebPagePrerender' => [
            Workers\Nomenclature\ViewCategory::class,
            Workers\Nomenclature\ViewProduct::class,
        ],
        'msopOnViewModification' => [
            Workers\Nomenclature\ViewModification::class,
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
    ]),
);

$config = $config->withWebHooks(
    Config\WebHooksConfig::fromArray([
        'GetPromoCodeOnFirstOrder' => [
            Webhooks\PromoCodes\GetPromoCodeOnFirstOrder::class,
        ],
    ]),
);

$config = $config->withExtensions(
    Config\ExtensionsConfig::fromArray([
        Config\Config::getNomenclatureWebsiteId => static fn (...$args) => Extensions::getNomenclatureWebsiteId(...$args),
    ]),
);

return $config;
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

## Сниппет *MindBoxMS2.Nomenclature.feed*
Выводит фид с номенклатурой. Пример вызова
```
[[!MindBoxMS2.Nomenclature.feed?
&categories:showUnpublished=`0`
&products:leftJoin=`{ "TVcount" : {"class": "modTemplateVarResource", "on": "msProduct.id = TVcount.contentid AND TVcount.tmplvarid = '13'" } }`
&products:select=`{ "TVcount": "TVcount.value as count" }`
&products:showUnpublished=`0`
]]
```

## Сниппет *MindBoxMS2.Customers.feed*
Выводит фид с пользователями. Пример вызова
```
[[!MindBoxMS2.Customers.feed?
&useBasicAuth=`1` 
&authUsername=`user`
&authPassword=`password`
]]
```
* useBasicAuth - использовать базовую аутентификацию при выводе данных