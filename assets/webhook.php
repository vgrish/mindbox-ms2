<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

\ini_set('apc.cache_by_default', 'Off');

\define('MODX_API_MODE', true);
\define('MODX_REQP', false);

$dir = \realpath(\dirname(__FILE__, 4));

if (\mb_substr($dir, -12) === '/core/vendor') {
    $dir = \str_replace('/core/vendor', '', $dir);
}

if (\file_exists($dir . '/config.core.php')) {
    require_once $dir . '/config.core.php';
}

if (!\defined('MODX_CORE_PATH')) {
    exit('Could not load MODX core');
}

require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';

include_once MODX_CORE_PATH . 'model/modx/modx.class.php';

require_once MODX_CORE_PATH . 'vendor/autoload.php';

/** @var modX $modx */
if (!isset($modx)) {
    $modx = \modX::getInstance(\modX::class);
}

$modx->getService('error', 'error.modError');
$modx->setLogLevel(\modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
$modx->initialize();

use Vgrish\MindBox\MS2\App;
use Vgrish\MindBox\MS2\Tools\Headers;
use Vgrish\MindBox\MS2\WebHookManager;

if (!$app = $modx->services[App::NAME] ?? null) {
    \http_response_code(503);

    exit('Service Unavailable');
}

if (!Headers::validateWebHookAuthorization((string) $app->getSettings('webhook_secret_key', null))) {
    \http_response_code(401);

    exit('Unauthorized');
}

if ($data = \json_decode(\trim(\file_get_contents('php://input')), true)) {
    $data = \array_merge($_REQUEST, $data);
} else {
    $data = $_REQUEST;
}

$operation = (string) ($data['operation'] ?? '');

if (empty($operation)) {
    \http_response_code(500);

    exit(\json_encode('The `operation` must be specified'));
}

try {
    $result = WebHookManager::load($app, $operation, $data);

    if (!$result->success) {
        \http_response_code(400);
    }

    echo \json_encode($result);
} catch (Throwable $e) {
    \http_response_code(500);
    echo \json_encode('Internal Server Error');
}
