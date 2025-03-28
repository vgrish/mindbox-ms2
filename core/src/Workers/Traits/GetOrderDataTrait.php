<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Workers\Traits;

use Vgrish\MindBox\MS2\Tools\Extensions;

trait GetOrderDataTrait
{
    public function getOrderData(\msOrder $msOrder): array
    {
        $address = $msOrder->getOne('Address') ?? $this->modx->newObject(\msOrderAddress::class);
        $user = $msOrder->getOne('User') ?? $this->modx->newObject(\modUser::class);

        if (!$profile = $user->getOne('Profile')) {
            $profile = $this->modx->newObject(\modUserProfile::class);
        }

        $discounts = [];

        if (Extensions::isExist('msPromoCode2')) {
            if ($mspcOrder = $this->modx->getObject(\mspcOrder::class, ['order_id' => $msOrder->get('id')])) {
                $discounts = [
                    [
                        'type' => 'promoCode',
                        'promoCode' => [
                            'ids' => [
                                'code' => $mspcOrder->get('code'),
                            ],
                        ],
                        'amount' => $mspcOrder->get('discount_amount'),
                    ],
                ];
            }
        }

        $lines = [];

        /** @var \msOrderProduct $products */
        $products = $this->modx->getCollection(\msOrderProduct::class, ['order_id' => $msOrder->get('id')]);

        foreach ($products as $product) {
            if ($websiteId = $this->app->getNomenclatureWebsiteId($product->get('product_id'), $product->get('options'))) {
                $lines[] = [
                    'basePricePerItem' => $product->get('price'),
                    'quantity' => $product->get('count'),
                    'quantityType' => 'int',
                    'discountedPricePerLine' => $product->get('cost'),
                    'status' => $msOrder->get('status'),
                    'product' => [
                        'ids' => [
                            'website' => $websiteId,
                        ],
                    ],
                ];
            }
        }

        return [
            'customer' => [
                'ids' => [
                    'websiteID' => $user->get('id'),
                ],
                'mobilePhone' => $profile->get('mobilephone'),
                'phone' => $profile->get('phone'),
                'phone2' => $address->get('phone'),
            ],
            'order' => [
                'ids' => [
                    'websiteID' => $msOrder->get('id'),
                ],
                'deliveryCost' => $msOrder->get('delivery_cost'),
                'customFields' => [
                    'deliveryType' => $msOrder->get('delivery'),
                ],
                'totalPrice' => $msOrder->get('cost'),
                'discounts' => $discounts,
                'lines' => $lines,
                'email' => $address->get('email'),
                'mobilePhone' => $address->get('phone'),
            ],
        ];
    }
}
