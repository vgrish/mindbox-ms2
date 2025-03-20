<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Workers\Nomenclature;

use Vgrish\MindBox\MS2\Dto\Data\Nomenclature\SetCartDataDto;
use Vgrish\MindBox\MS2\Worker;
use Vgrish\MindBox\MS2\WorkerResult;

class SetCart extends Worker
{
    protected static string $operation = 'Website.SetCart';
    protected static bool $isAsyncOperation = false;
    protected static bool $isClientRequired = true;

    public function process(): WorkerResult
    {
        $params = $this->event->params;

        $cart = $params['cart'] ?? null;

        if (\is_object($cart) && \is_a($cart, \msCartInterface::class)) {
            $productList = [];

            foreach ($cart->get() as $row) {
                if (!$count = $row['count'] ?? null) {
                    continue;
                }

                $productList[] = [
                    'count' => $count,
                    'pricePerItem' => $row['price'] ?? 0,
                    'product' => [
                        'ids' => [
                            'website' => $row['id'] ?? null,
                        ],
                    ],
                ];
            }

            if (!empty($productList)) {
                $data = [
                    'productList' => $productList,
                ];

                $ctx = (string) $this->modx?->context?->get('key');

                if ($this->modx->user->isAuthenticated($ctx) && ($profile = $this->modx->user->getOne('Profile'))) {
                    $data = \array_merge($data, [
                        'customer' => [
                            'email' => $profile->get('email'),
                        ],
                    ]);
                }

                $data = $this->formatData(SetCartDataDto::class, $data);

                return $this->success($data);
            }
        }

        return $this->error();
    }
}
