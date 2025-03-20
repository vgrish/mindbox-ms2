<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2;

use CuyZ\Valinor\Mapper\Source\Source;
use Vgrish\MindBox\MS2\Dto\EventDto;
use Vgrish\MindBox\MS2\Models\Event;
use Vgrish\MindBox\MS2\Tools\Cookies;

abstract class Worker implements WorkerInterface
{
    protected \modX $modx;
    protected \modSystemEvent $event;
    protected WorkerResult $result;
    protected static string $operation;
    protected static bool $isAsyncOperation;
    protected static bool $isClientRequired;
    protected static bool $isDevelopmentMode;

    public function __construct(
        protected App $app,
        protected array $config = [],
    ) {
        $this->modx = $app->modx;

        if (empty($config)) {
            $event = $this->modx->event;
        } else {
            $event = new \modSystemEvent();
            $event->resetEventObject();
            $event->name = 'manual';
            $event->params = $this->config;
        }

        if (!\is_array($event->params)) {
            $event->params = [];
        }

        if (!isset(static::$operation)) {
            throw new \LogicException(\sprintf('%s must have a `%s`', static::class, '$operation'));
        }

        if (!isset(static::$isAsyncOperation)) {
            throw new \LogicException(\sprintf('%s must have a `%s`', static::class, '$isAsyncOperation'));
        }

        if (!isset(static::$isClientRequired)) {
            throw new \LogicException(\sprintf('%s must have a `%s`', static::class, '$isClientRequired'));
        }

        $this->event = $event;

        self::$isDevelopmentMode = (bool) $this->modx->getOption(App::NAMESPACE . '.development_mode', null);
    }

    public function run(bool $debug = false): WorkerResult
    {
        $result = $this->process();

        if ($result->success) {
            $data = [
                'id' => \hrtime(true),
                'operation' => $this->operation(),
                'is_async_operation' => $this->isAsyncOperation(),
                'context_key' => $this->getContextKey(),
                'client_uuid' => $this->getDeviceUUID(),
                'client_ip' => $this->getClientIp(),
                'data' => $result->data,
            ];

            if (self::$isDevelopmentMode) {
                $this->log($data);
            }

            $data = $this->formatData(EventDto::class, $data, false);
            $event = new Event($this->modx);
            $event->fromArray($data, '', true, true);

            if (!$debug) {
                $event->save();
            } else {
                $this->log($event->toArray());
            }
        }

        return $result;
    }

    public function log(array $data = []): void
    {
        $info = \var_export(
            [
                'event' => $this->event->name,
            ] + \array_filter(\get_class_vars(static::class), static function ($value) {
                return !\is_object($value) && null !== $value;
            }),
            true,
        );

        $this->modx->log(\modX::LOG_LEVEL_ERROR, $info);
        $this->modx->log(\modX::LOG_LEVEL_ERROR, \var_export($data, true));
    }

    public function error(array $data = []): WorkerResult
    {
        return new WorkerResult(false, $data);
    }

    public function success(array $data = []): WorkerResult
    {
        return new WorkerResult(true, $data);
    }

    public function formatData(string $dtoClass, array $data, bool $showLog = true): array
    {
        if ($showLog && self::$isDevelopmentMode) {
            $this->log($data);
        }

        $data = $this->app->getMappper()
            ->map(
                $dtoClass,
                Source::array($data),
            );
        $data = $this->app->getNormalizer()->normalize($data);

        if ($showLog && self::$isDevelopmentMode) {
            $this->log($data);
        }

        return $data;
    }

    public function operation(): string
    {
        return static::$operation;
    }

    public function isAsyncOperation(): bool
    {
        return static::$isAsyncOperation;
    }

    public function isClientRequired(): bool
    {
        return static::$isClientRequired;
    }

    protected function getContextKey(): string
    {
        $ctx = (string) $this->modx?->context->get('key');

        return match ($ctx) {
            '' => 'mgr',
            default => $ctx,
        };
    }

    protected function getDeviceUUID(): string
    {
        $uuid = '';

        if ($this->isClientRequired()) {
            $uuid = (string) ($_COOKIE[App::MINDBOX_DEVICE_UUID] ?? '');

            if (empty($uuid)) {
                $uuid = Cookies::generateDeviceUUID(true);
            }
        }

        return $uuid;
    }

    protected function getClientIp(): string
    {
        return (string) ($_SERVER['REMOTE_ADDR'] ?? '');
    }
}
