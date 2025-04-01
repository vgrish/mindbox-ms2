<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

use CuyZ\Valinor\Mapper\Source\Source;
use Vgrish\MindBox\MS2\App;
use Vgrish\MindBox\MS2\Dto\Entities\Feeds\CustomerDto;
use Vgrish\MindBox\MS2\Tools\BasicAuth;

/** @var modX $modx */
/** @var array $scriptProperties */
/** @var App $app */
if (!$app = $modx->services[App::NAME] ?? null) {
    return;
}

if ((int) ($scriptProperties['useBasicAuth'] ?? 0)) {
    $authUsername = \trim((string) ($scriptProperties['authUsername'] ?? ''));
    $authPassword = \trim((string) ($scriptProperties['authPassword'] ?? ''));

    if (!BasicAuth::validateAuthorization($authUsername, $authPassword)) {
        \header('WWW-Authenticate: Basic realm="Restricted Area"');
        \header('HTTP/1.0 401 Unauthorized');

        exit;
    }
}

$enclosure = $scriptProperties['enclosure'] ?? '"';
$separator = $scriptProperties['separator'] ?? ';';
$showLog = 0;

if ($modx->user->hasSessionContext('mgr')) {
    $showLog = (int) ($scriptProperties['showLog'] ?? 0);
}

/** @var pdoFetch $pdoFetch */
$fqn = $modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
$path = $modx->getOption('pdofetch_class_path', null, MODX_CORE_PATH . 'components/pdotools/model/', true);

if ($pdoClass = $modx->loadClass($fqn, $path, false, true)) {
    $pdoFetch = new $pdoClass($modx, $scriptProperties);
} else {
    return false;
}

$pdoFetch->addTime('pdoTools loaded.');

$properties = static function (string $prefix, array $defaults) use ($scriptProperties): array {
    $data = [];

    foreach ($scriptProperties as $k => $v) {
        if (\str_starts_with($k, $prefix)) {
            $k = \mb_substr($k, \mb_strlen($prefix));

            if (!empty($v) && \is_string($v) && ('[' === $v[0] || '{' === $v[0])) {
                $tmp = \json_decode($v, true);

                if (\json_last_error() === \JSON_ERROR_NONE) {
                    $v = $tmp;
                }
            }
        }

        if (isset($defaults[$k])) {
            if (\is_array($defaults[$k]) && \is_array($v)) {
                $v = \array_merge($defaults[$k], $v);
            }
        }

        $data[$k] = $v;
    }

    return \array_merge($defaults, $data);
};

$format = static function (string $dtoClass, array $data) use ($app): ?array {
    try {
        $data = $app->getMappper()
            ->map(
                $dtoClass,
                Source::array($data),
            );
        $data = $app->getNormalizer()->normalize($data);
    } catch (\Throwable  $e) {
        $app->modx->log(\modX::LOG_LEVEL_ERROR, \var_export($e->getMessage(), true));
        $app->modx->log(\modX::LOG_LEVEL_ERROR, \var_export($data, true));
        $data = null;
    }

    return $data;
};

$customers = $logs = [];

// Start building "Where" expression
$where = [];

if (empty($showInactive)) {
    $where['modUser.active'] = 1;
}

if (empty($showBlocked)) {
    $where['Profile.blocked'] = 0;
}

// NOTE START customers
$innerJoin = [
    'Profile' => ['class' => 'modUserProfile'],
];
$leftJoin = [];
$select = [
    'modUser' => $modx->getSelectColumns('modUser', 'modUser', '', ['password', 'cachepwd', 'salt', 'session_stale', 'remote_key', 'remote_data', 'hash_class'], true),
    'Profile' => $modx->getSelectColumns('modUserProfile', 'Profile', '', ['id', 'internalKey', 'sessionid'], true),
];

$default = [
    'class' => 'modUser',
    'where' => $where,
    'leftJoin' => $leftJoin,
    'innerJoin' => $innerJoin,
    'select' => $select,
    'sortby' => 'modUser.id',
    'sortdir' => 'ASC',
    'groupby' => 'modUser.id',
    'return' => 'data',
    'nestedChunkPrefix' => 'minishop2_',
    'limit' => 0,
];

// Merge all properties and run!
$props = $properties('customers:', $default);
$pdoFetch->setConfig($props, true);
$rows = $pdoFetch->run();

if ($showLog) {
    $logs[] = $pdoFetch->getTime();
}

if (!empty($rows) && \is_array($rows)) {
    $pls = $props['placeholders'] ?? [];

    foreach ($rows as $k => $row) {
        if (!$websiteId = $row['id'] ?? 0) {
            continue;
        }

        $row['idx'] = $pdoFetch->idx++;
        $row['ExternalIdentityWebsiteID'] = $websiteId;

        $customers[$row['id']] = \array_merge($pls, $row);
    }
}

unset($rows);
// NOTE END customers

$fields = [];
$reflectionClass = new ReflectionClass(CustomerDto::class);
$constructorParams = $reflectionClass->getConstructor()->getParameters();

foreach ($constructorParams as $param) {
    if ($param->isPromoted()) {
        $property = $reflectionClass->getProperty($param->getName());

        if ($property->isPublic()) {
            $fields[] = $param->getName();
        }
    }
}

$defaults = \array_fill_keys($fields, '');

foreach ($customers as $id => $row) {
    if ($row = $format(CustomerDto::class, $row)) {
        $customers[$id] = \array_merge($defaults, $row);
    } else {
        unset($customers[$id]);
    }
}

if (!empty($logs)) {
    $modx->log(\modX::LOG_LEVEL_ERROR, \var_export(\implode("\n", $logs), true));
}

if (!isset($_GET['format'])) {
    \header('Content-Type: text/csv; charset=utf-8');
    \header('Content-Disposition: attachment; filename="customers.csv"');
}

$output = \fopen('php://output', 'wb');
\fwrite($output, \chr(0xEF) . \chr(0xBB) . \chr(0xBF));
\fputcsv($output, $fields, $separator, $enclosure);

foreach ($customers as $row) {
    \fputcsv($output, $row, $separator, $enclosure);
}

\fclose($output);
