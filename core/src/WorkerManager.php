<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2;

class WorkerManager
{
    public static function load(App $app, \modSystemEvent $event): void
    {
        $workers = $app->config->getWorkersConfig()->getHandlersForEvent($event->name);

        foreach ($workers as $class) {
            try {
                /** @var WorkerInterface $worker */
                $worker = new $class($app);

                $worker->run();
            } catch (\Throwable  $e) {
                $app->modx->log(\modX::LOG_LEVEL_ERROR, \var_export($e->getMessage(), true));
            }
        }
    }
}
