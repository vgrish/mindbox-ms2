<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2;

class WebHookManager
{
    public static function load(App $app, mixed $webhooks, array $data): WebHookResult
    {
        $result = new WebHookResult(false, $data);

        if (!empty($webhooks)) {
            if (\is_string($webhooks)) {
                $webhooks = [$webhooks];
            }

            if (\is_array($webhooks)) {
                $webhooks = \array_filter($webhooks, static function ($class) {
                    return \is_string($class) && !empty($class);
                });

                $results = [];

                foreach ($webhooks as $webhook) {
                    try {
                        /** @var WebHookInterface $hook */
                        $hook = new $webhook($app, $data);

                        $results[] = $hook->run();
                    } catch (\Throwable  $e) {
                        $app->modx->log(\modX::LOG_LEVEL_ERROR, \var_export($e->getMessage(), true));
                    }
                }

                $result = WebHookResult::merge($results);
            }
        }

        return $result;
    }
}
