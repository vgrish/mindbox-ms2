<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Workers\Traits;

trait GetProductListDataTrait
{
    public function getProductListData(array $productList): array
    {
        $result = [];

        foreach ($productList as $item) {
            $websiteId = null;

            if (isset($item['product'], $item['product']['ids'])) {
                $websiteId = $item['product']['ids']['website'];
            }

            if ($websiteId) {
                if (!isset($result[$websiteId])) {
                    $result[$websiteId] = $item;
                    $result[$websiteId]['count'] = $item['count'];
                } else {
                    $result[$websiteId]['count'] += $item['count'];
                }
            }
        }

        return \array_values($result);
    }
}
