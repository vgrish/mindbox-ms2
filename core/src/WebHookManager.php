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
    public static function load(App $app, string $operation, array $data): WebHookResult
    {
        $results = [];
        $webhooks = $app->config->getWebhooksConfig()->getHandlersForOperation($operation);

        foreach ($webhooks as $webhook) {
            try {
                /** @var WebHookInterface $hook */
                $hook = new $webhook($app, $data);

                $results[] = $hook->run();
            } catch (\Throwable  $e) {
                $app->modx->log(\modX::LOG_LEVEL_ERROR, \var_export($e->getMessage(), true));
            }
        }

        if (!empty($results)) {
            $result = WebHookResult::merge($results);
        } else {
            $result = new WebHookResult(false, $data);
        }

        return $result;
    }
}
