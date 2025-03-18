<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2;

use CuyZ\Valinor\Mapper\Source\Source;
use Vgrish\MindBox\MS2\Dto\ApiResponseDto;
use Vgrish\MindBox\MS2\Enums\HttpMethod;
use Vgrish\MindBox\MS2\Http\Payload;
use Vgrish\MindBox\MS2\Models\Event;

class EventManager
{
    public static function load(App $app, ?\Closure $callback = null, int $limit = 100): void
    {
        if (null === $callback) {
            $callback = [self::class, 'sendEvent'];
        }

        $modx = $app->modx;
        $c = $modx->newQuery(Event::class);
        $c->where([
            'sended' => false,
            'rejected' => false,
        ]);
        $c->sortby('id', 'ASC');
        $c->select('id');
        $page = 1;

        while (true) {
            $offset = ($page - 1) * $limit;

            $q = clone $c;
            $q->limit($limit, $offset);
            $q->prepare();

            if ($stmt = $modx->prepare($q->toSQL())) {
                if ($stmt->execute()) {
                    if ($stmt->rowCount() > 0) {
                        while ($id = $stmt->fetch(\PDO::FETCH_COLUMN)) {
                            if ($event = $modx->getObject(Event::class, $id, false)) {

                                if (!$event->get('is_async_operation')) {
                                    \usleep(500000);
                                }

                                try {
                                    $callback($app, $event);
                                } catch (\Throwable  $e) {
                                    $modx->log(\modX::LOG_LEVEL_ERROR, \var_export($e->getMessage(), true));
                                }
                            }
                        }
                    } else {
                        break;
                    }
                } else {
                    $modx->log(\xPDO::LOG_LEVEL_ERROR, \var_export($stmt->errorInfo(), true));
                }

                $stmt->closeCursor();
            }

            ++$page;
        }
    }

    public static function clean(App $app, ?\Closure $callback = null): void
    {
        if (null === $callback) {
            $callback = [self::class, 'cleanEvent'];
        }

        try {
            $callback($app);
        } catch (\Throwable  $e) {
            $app->modx->log(\modX::LOG_LEVEL_ERROR, \var_export($e->getMessage(), true));
        }
    }

    public static function sendEvent(App $app, Event $event): void
    {
        $modx = $app->modx;

        $client = $app->getApiClient([
            'SecretKey' => $modx->getOption(App::NAMESPACE . '.api_secret_key', null),
        ]);
        $client->addParamsToHeaders(['X-Customer-IP' => $event->get('client_ip')]);

        $params = [
            'endpointId' => $modx->getOption(App::NAMESPACE . '.api_endpoint_id', null),
            'operation' => $event->get('operation'),
            'deviceUUID' => $event->get('client_uuid'),
        ];

        $payload = new Payload(
            method: HttpMethod::POST,
            path: [$event->get('async') ? 'async' : 'sync'],
            params: $params,
            body: $event->get('data'),
        );

        if ((bool) $modx->getOption(App::NAMESPACE . '.development_mode', null)) {
            $modx->log(\modX::LOG_LEVEL_ERROR, \var_export($client->debug($payload), true));
        }

        try {
            $data = $client->send($payload);
            $data = $app->getMappper()
                ->map(
                    ApiResponseDto::class,
                    Source::array($data),
                );
            $data = $app->getNormalizer()->normalize($data);
        } catch (\Throwable  $e) {
            $data = ['errorMessage' => $e->getMessage()];
            $modx->log(\modX::LOG_LEVEL_ERROR, \var_export($e->getMessage(), true));
        }

        if ($data['success'] ?? false) {
            $event->setFlagSended()->save();
        } else {
            if ($errorMessage = (string) ($data['errorMessage'] ?? '')) {
                $event->setErrorMessage($errorMessage);
            }

            $event->setFlagRejected()->save();
        }
    }

    public static function cleanEvent(App $app): void
    {
        $modx = $app->modx;

        $modx->removeCollection(Event::class, [
            'sended' => true,
        ]);
        $modx->removeCollection(Event::class, [
            'rejected' => true,
        ]);
    }
}
