<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2;

use CuyZ\Valinor\Mapper\Source\Source;
use Vgrish\MindBox\MS2\Dto\WebHookDto;

abstract class WebHook implements WebHookInterface
{
    protected \modX $modx;
    protected WebHookResult $result;
    protected static string $operation;
    protected static string $transactionId;
    protected static bool $isDevelopmentMode;

    public function __construct(
        protected App $app,
        protected array $config = [],
    ) {
        $this->modx = $app->modx;

        static::$operation = (string) ($config['operation'] ?? '');
        static::$transactionId = (string) ($config['transactionId'] ?? '');

        if (!isset(static::$operation)) {
            throw new \LogicException(\sprintf('%s must have a `%s`', static::class, '$operation'));
        }

        self::$isDevelopmentMode = (bool) $this->modx->getOption(App::NAMESPACE . '.development_mode', null);
    }

    public function run(bool $debug = false): WebHookResult
    {
        $result = $this->process();

        if ($result->success) {
            $data = [
                'id' => \hrtime(true),
                'operation' => $this->operation(),
                'transaction_id' => $this->getTransactionId(),
                'client_ip' => $this->getClientIp(),
                'data' => $result->data,
            ];

            if (self::$isDevelopmentMode) {
                $this->log($data);
            }

            $data = $this->formatData(WebHookDto::class, $data, false);

            if ($debug) {
                $this->log($data);
            }
        }

        return $result;
    }

    public function log(array $data = []): void
    {
        $info = \var_export(
            [
                'webhook' => static::class,
            ] + \array_filter(\get_class_vars(static::class), static function ($value) {
                return !\is_object($value) && null !== $value;
            }),
            true,
        );

        $this->modx->log(\modX::LOG_LEVEL_ERROR, $info);
        $this->modx->log(\modX::LOG_LEVEL_ERROR, \var_export($data, true));
    }

    public function error(array $data = []): WebHookResult
    {
        return new WebHookResult(false, $data);
    }

    public function success(array $data = []): WebHookResult
    {
        return new WebHookResult(true, $data);
    }

    public function operation(): string
    {
        return static::$operation;
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

    protected function getTransactionId(): string
    {
        return static::$transactionId;
    }

    protected function getClientIp(): string
    {
        return (string) ($_SERVER['REMOTE_ADDR'] ?? '');
    }
}
