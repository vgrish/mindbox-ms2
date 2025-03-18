<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

use Vgrish\MindBox\MS2\App;
use Vgrish\MindBox\MS2\WorkerResult;

require_once MODX_CORE_PATH . 'vendor/autoload.php';

if (!\function_exists('getUser')) {
    function getUser(): ?\modUser
    {
        global $modx;

        if (!$user = $modx->getObject(
            \modUser_mysql::class,
            $modx->newQuery(\modUser::class, ['username' => 'test_mindbox']),
        )) {
            $user = $modx->newObject(\modUser::class);
            $user->fromArray([
                'email' => 'test_mindbox@test.ru',
                'phone' => '79990000001',
                'username' => 'test_mindbox',
            ]);

            if (!$user->save()) {
                return null;
            }
        }

        return \is_a($user, \modUser::class) ? $user : null;
    }
}

$workers = [
    '',
    // Customers
    // '10_RegisterCustomer',
    // '20_AuthorizeCustomer',
    // '30_EditCustomer'

    // Nomenclature
    // '10_AddToWishList',
    // '20_RemoveFromWishList',
    // '30_ClearWishList',
    // '40_SetCart',
    // '50_ClearCart',
    // '60_ViewCategory',
    // '70_ViewProduct',

    // Orders
    // '10_CreateUnauthorizedOrder',
    // '20_CreateAuthorizedOrder',
    // '30_UpdateOrder',
    // '40_UpdateOrderStatus',
];

$path = MODX_CORE_PATH . 'vendor/' . App::AUTHOR . '/' . App::NAMESPACE . '/tests/single/';
$dirs = \array_diff(\scandir($path), ['.', '..']);

$results = [];

foreach ($dirs as $dir) {
    if (\is_dir($path . $dir)) {
        $tests = \array_diff(\scandir($path . $dir), ['.', '..']);

        foreach ($tests as $test) {
            if (!empty($workers) && !\in_array(\pathinfo($test, \PATHINFO_FILENAME), $workers, true)) {
                continue;
            }

            try {
                $result = include $path . $dir . '/' . $test;

                if ($result && \is_a($result, WorkerResult::class)) {
                    $results[$dir . '/' . $test] = $result->success;
                } else {
                    $results[$dir . '/' . $test] = false;
                }
            } catch (\Exception  $e) {
                $results[$dir . '/' . $test] = $e->getMessage();
            } catch (\Throwable  $e) {
                $results[$dir . '/' . $test] = $e->getMessage();
            }
        }
    }
}

echo '<pre>';
echo \sprintf('Всего тестов (%s)', \count($results)) . "\n";

$successfulTests = \array_filter($results, static function ($result) {
    return true === $result;
});

$failedTests = \array_filter($results, static function ($result) {
    return true !== $result;
});

echo \sprintf('Успешные тесты (%s):', \count($successfulTests)) . "\n";

if (\count($successfulTests)) {
    \print_r($successfulTests);
}

echo \sprintf('Неуспешные тесты (%s):', \count($failedTests)) . "\n";

if (\count($failedTests)) {
    \print_r($failedTests);
}

if (false) {
    /** @var modX $modx */
    $app = new \Vgrish\MindBox\MS2\App($modx);
    \Vgrish\MindBox\MS2\EventManager::load($app);
}
