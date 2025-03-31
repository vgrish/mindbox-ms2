<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

use CuyZ\Valinor\Mapper\Source\Source;
use Vgrish\MindBox\MS2\App;
use Vgrish\MindBox\MS2\Dto\Entities\Feeds\Category;
use Vgrish\MindBox\MS2\Dto\Entities\Feeds\Offer;
use Vgrish\MindBox\MS2\Tools\Url;

/** @var modX $modx */
/** @var array $scriptProperties */
/** @var App $app */
if (!$app = $modx->services[App::NAME] ?? null) {
    return;
}

$miniShop2 = $modx->getService('miniShop2');
$miniShop2->initialize($modx->context->key);

/** @var pdoFetch $pdoFetch */
$fqn = $modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
$path = $modx->getOption('pdofetch_class_path', null, MODX_CORE_PATH . 'components/pdotools/model/', true);

if ($pdoClass = $modx->loadClass($fqn, $path, false, true)) {
    $pdoFetch = new $pdoClass($modx, $scriptProperties);
} else {
    return false;
}

$pdoFetch->addTime('pdoTools loaded.');

$baseUrl = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];

$innerJoin = [];
$leftJoin = [];
$select = [
    'modResource' => !empty($includeContent)
        ? $modx->getSelectColumns('modResource', 'modResource', '', ['type'], true)
        : $modx->getSelectColumns('modResource', 'modResource', '', ['type', 'content'], true),
];
$default = [
    'class' => 'modResource',
    'where' => ['class_key:!=' => 'msProduct'],
    'leftJoin' => $leftJoin,
    'innerJoin' => $innerJoin,
    'select' => $select,
    'sortby' => 'modResource.id',
    'sortdir' => 'ASC',
    'groupby' => 'modResource.id',
    'return' => 'data',
    'nestedChunkPrefix' => 'minishop2_',
    'limit' => 0,
];
// Merge all properties and run!
$pdoFetch->setConfig(\array_merge($default, $scriptProperties), true);
$rows = $pdoFetch->run();

$categories = [];

if (!empty($rows) && \is_array($rows)) {
    foreach ($rows as $k => $row) {
        if (!$websiteId = $app->getNomenclatureWebsiteId($row)) {
            continue;
        }

        $parentWebsiteId = null;

        if (!empty($row['parent'])) {
            $parentWebsiteId = $app->getNomenclatureWebsiteId($row['parent']);
        }

        $row['idx'] = $pdoFetch->idx++;
        $row['websiteId'] = $websiteId;
        $row['parentWebsiteId'] = $parentWebsiteId;

        $categories[$row['id']] = $row;
    }
}

unset($rows);

$innerJoin = [];
$leftJoin = [
    'Data' => ['class' => 'msProductData'],
    'Vendor' => ['class' => 'msVendor', 'on' => 'Data.vendor=Vendor.id'],
    'ProductOption' => ['class' => 'msProductOption', 'on' => 'Data.id = ProductOption.product_id'],
];
$select = [
    'msProduct' => !empty($includeContent)
        ? $modx->getSelectColumns('msProduct', 'msProduct', '', ['type'], true)
        : $modx->getSelectColumns('msProduct', 'msProduct', '', ['type', 'content'], true),
    'Data' => $modx->getSelectColumns('msProductData', 'Data', '', ['id'], true),
    'Vendor' => $modx->getSelectColumns('msVendor', 'Vendor', 'vendor.', ['id'], true),
    'ProductOption' => 'GROUP_CONCAT(CONCAT_WS("[]=",`ProductOption`.`key`,`ProductOption`.`value`) SEPARATOR "&") as options',
];

// TODO
$leftJoin['TVcount'] = [
    'class' => 'modTemplateVarResource',
    'on' => 'msProduct.id = TVcount.contentid AND TVcount.tmplvarid = "13"',
];
$select['TVcount'] = 'TVcount.value as count';

$default = [
    'class' => 'msProduct',
    'where' => ['class_key' => 'msProduct'],
    'leftJoin' => $leftJoin,
    'innerJoin' => $innerJoin,
    'select' => $select,
    'sortby' => 'msProduct.id',
    'sortdir' => 'ASC',
    'groupby' => 'msProduct.id',
    'return' => 'data',
    'nestedChunkPrefix' => 'minishop2_',
    'limit' => 0,
];
// Merge all properties and run!
$pdoFetch->setConfig(\array_merge($default, $scriptProperties), true);
$rows = $pdoFetch->run();

$products = [];

// Process rows
if (!empty($rows) && \is_array($rows)) {
    /** @var msProductData $product */
    $product = $modx->newObject('msProductData');

    foreach ($rows as $k => $row) {
        if (!$websiteId = $app->getNomenclatureWebsiteId($row)) {
            continue;
        }

        $product->fromArray($row, '', true, true);
        $tmp = $row['price'];
        $row['price'] = $product->getPrice($row);
        $row['weight'] = $product->getWeight($row);

        if ($row['price'] < $tmp) {
            $row['old_price'] = $tmp;
        }

        if (empty($row['price']) || empty($row['published']) || !empty($row['deletedon'])) {
            $row['active'] = 0;
        } else {
            $row['active'] = 1;
        }

        $row['options'] = !empty($row['options']) ? Url::parseStr($row['options']) : [];
        $row['idx'] = $pdoFetch->idx++;
        $row['websiteId'] = $websiteId;

        $parentWebsiteId = null;

        if (!empty($row['parent'])) {
            $parentWebsiteId = $app->getNomenclatureWebsiteId($row['parent']);
        }

        $row['parentWebsiteId'] = $parentWebsiteId;
        $row['baseUrl'] = $baseUrl;

        $products[$row['id']] = $row;
    }
}

unset($rows);

$innerJoin = [];
$leftJoin = [
    'Option' => [
        'class' => 'msopModificationOption',
        'on' => 'msopModification.id = Option.mid',
    ],
];

$select = [
    'msopModification' => !empty($includeContent)
        ? $modx->getSelectColumns('msopModification', 'msopModification') . ', `msopModification`.`name` as `pagetitle`'
        : $modx->getSelectColumns('msopModification', 'msopModification', '', ['description'], true) . ', `msopModification`.`name` as `pagetitle`',
    'Option' => 'GROUP_CONCAT(CONCAT_WS("[]=",`Option`.`key`,`Option`.`value`) SEPARATOR "&") as options',
];

$default = [
    'class' => 'msopModification',
    'leftJoin' => $leftJoin,
    'innerJoin' => $innerJoin,
    'select' => $select,
    'sortby' => 'msopModification.id',
    'sortdir' => 'ASC',
    'groupby' => 'msopModification.id',
    'return' => 'data',
    'nestedChunkPrefix' => 'minishop2_',
    'limit' => 0,
];
// Merge all properties and run!
$pdoFetch->setConfig(\array_merge($default, $scriptProperties), true);
$rows = $pdoFetch->run();

$modifications = [];

if (!empty($rows) && \is_array($rows)) {
    foreach ($rows as $k => $row) {
        $product = $products[$row['rid']] ?? [];

        if (!$product || !($websiteId = $app->getNomenclatureWebsiteId($product, $row))) {
            continue;
        }

        $row = \array_merge($product, $row);

        if (empty($row['pagetitle'])) {
            $row['pagetitle'] = $product['pagetitle'] ?? 'id:' . $product['id'];
        }

        if (empty($row['price'])) {
            $row['price'] = $product['price'] ?? 0;
        }

        if (empty($row['old_price'])) {
            $row['old_price'] = $product['old_price'] ?? 0;
        }

        if (empty($row['image'])) {
            $row['image'] = $product['image'] ?? '';
        }

        if (empty($row['article'])) {
            $row['article'] = $product['article'] ?? '';
        }

        if (empty($row['price']) || empty($row['published']) || !empty($row['deletedon'])) {
            $row['active'] = 0;
        }

        $row['options'] = \array_merge(
            $product['options'] ?? [],
            !empty($row['options']) ? Url::parseStr($row['options']) : [],
        );
        $row['idx'] = $pdoFetch->idx++;
        $row['websiteId'] = $websiteId;
        $row['parentWebsiteId'] = $app->getNomenclatureWebsiteId($row['parent']);

        $modifications[$row['id']] = $row;
    }
}

unset($rows);

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

foreach ($categories as $id => $row) {
    if ($row = $format(Category::class, $row)) {
        $categories[$id] = $row;
    } else {
        unset($categories[$id]);
    }
}

foreach ($products as $id => $row) {
    if ($row = $format(Offer::class, $row)) {
        $products[$id] = $row;
    } else {
        unset($products[$id]);
    }
}

foreach ($modifications as $id => $row) {
    if ($row = $format(Offer::class, $row)) {
        $modifications[$id] = $row;
    } else {
        unset($modifications[$id]);
    }
}

try {
    $xml = new \XMLWriter();
    $xml->openMemory();
    $xml->setIndent(true);
    $xml->setIndentString('    ');

    $xml->startDocument('1.0', 'UTF-8');
    $xml->startElement('yml_catalog');
    $xml->writeAttribute('date', \date('Y-m-d H:i'));
    $xml->startElement('shop');

    // NOTE START categories
    $xml->startElement('categories');

    foreach ($categories as $row) {
        $xml->startElement('category');
        $xml->writeAttribute('id', $row['id']);

        if (isset($row['parent'])) {
            $xml->writeAttribute('parentId', $row['parent']);
        }

        $xml->text($row['pagetitle']);
        $xml->endElement();
    }

    // NOTE END categories
    $xml->endElement();

    // NOTE START offers
    $xml->startElement('offers');

    foreach ([$products, $modifications] as $rows) {
        foreach ($rows as $row) {
            $xml->startElement('offer');
            $xml->writeAttribute('id', $row['id']);
            $xml->writeAttribute('available', $row['available'] ? 'true' : 'false');

            if (!empty($row['categoryId'])) {
                $xml->startElement('categoryId');
                $xml->text($row['categoryId']);
                $xml->endElement();
            }

            $xml->startElement('name');
            $xml->text($row['pagetitle']);
            $xml->endElement();

            if (!empty($row['vendorCode'])) {
                $xml->startElement('vendorCode');
                $xml->text($row['vendorCode']);
                $xml->endElement();
            }

            $xml->startElement('price');
            $xml->text($row['price']);
            $xml->endElement();

            if (!empty($row['old_price'])) {
                $xml->startElement('oldprice');
                $xml->text($row['old_price']);
                $xml->endElement();
            }

            if (!empty($row['picture'])) {
                $xml->startElement('picture');
                $xml->text($row['picture']);
                $xml->endElement();
            }

            if (!empty($row['url'])) {
                $xml->startElement('url');
                $xml->text($row['url']);
                $xml->endElement();
            }

            if (!empty($row['options'])) {
                foreach ($row['options'] as $key => $values) {
                    $xml->startElement('param');
                    $xml->writeAttribute('name', (string) $key);
                    $xml->text(\implode('|', $values));
                    $xml->endElement();
                }
            }

            $xml->endElement();
        }
    }

    // NOTE END offers
    $xml->endElement();

    // NOTE END shop
    $xml->endElement();
    // NOTE END yml_catalog
    $xml->endElement();
    $xml->endDocument();

    $output = $xml->outputMemory();
} catch (\Throwable  $e) {
    $output = '';
    $modx->log(\modX::LOG_LEVEL_ERROR, \var_export($e->getMessage(), true));
    $modx->log(\modX::LOG_LEVEL_ERROR, \var_export($row, true));
}

if (isset($_GET['format'])) {
    \header('Content-Type: text/plain; charset=UTF-8');
    echo $output;

    exit;
}

\header('Content-type: text/xml');
echo $output;
