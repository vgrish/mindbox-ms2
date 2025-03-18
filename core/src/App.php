<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2;

use CuyZ\Valinor\Cache\FileSystemCache;
use CuyZ\Valinor\Cache\FileWatchingCache;
use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Normalizer\Format;
use Vgrish\MindBox\MS2\Dto\Entities\HiddenValueDto;
use Vgrish\MindBox\MS2\Http\ApiClient;
use Vgrish\MindBox\MS2\Http\GuzzleSenderFactory;
use Vgrish\MindBox\MS2\Http\RequestSenderFactoryInterface;
use Vgrish\MindBox\MS2\Tools\ClassFinder;

class App
{
    public const AUTHOR = 'vgrish';
    public const NAME = 'MindBoxMS2';
    public const NAMESPACE = 'mindbox-ms2';
    public const VERSION = '1.0.0';
    public const MINDBOX_DEVICE_UUID = 'mindboxDeviceUUID';
    public \modX $modx;
    protected ?ApiClient $apiClient = null;
    protected static $eventsWorkers;
    protected static $valinorMapper;

    public function __construct(\modX $modx, $config = [])
    {
        $this->modx = $modx;

        // HACK for loading models with namespace
        if (\is_dir(MODX_CORE_PATH . 'components/' . self::NAMESPACE)) {
            $models = ClassFinder::findByRegex(
                '/^Vgrish\\\\MindBox\\\\MS2\\\\Models\\\\(?!.*_mysql$).+$/',
            );

            foreach ($models as $nsClass) {
                $class = self::NAME . \mb_substr($nsClass, \mb_strrpos($nsClass, '\\') + 1);

                if (!isset($modx->map[$class])) {
                    $modx->loadClass(
                        $class,
                        MODX_CORE_PATH . 'components/' . self::NAMESPACE . '/src/Models/' . self::NAME . '/' . self::NAME . '/',
                        true,
                        false,
                    );
                }

                if (!isset($modx->map[$nsClass])) {
                    $modx->map[$nsClass] = $modx->map[$class];
                }
            }
        }
    }

    public function getOption($key, $config = [], $default = null, $skipEmpty = false)
    {
        $option = $default;

        if (!empty($key) && \is_string($key)) {
            if (null !== $config && \array_key_exists($key, $config)) {
                $option = $config[$key];
            } elseif (\array_key_exists(self::NAMESPACE . '.' . $key, $this->modx->config)) {
                $option = $this->modx->getOption(self::NAMESPACE . '.' . $key);
            }
        }

        if ($skipEmpty && empty($option)) {
            $option = $default;
        }

        return $option;
    }

    public function getApiClient(array $credentials, RequestSenderFactoryInterface $requestSenderFactory = new GuzzleSenderFactory()): ApiClient
    {
        if (null === $this->apiClient) {
            $this->apiClient = new ApiClient($credentials, $requestSenderFactory->make());
        }

        return $this->apiClient;
    }

    public function getValinorMapperBuilder(): MapperBuilder
    {
        if (null === self::$valinorMapper) {
            $cache = new FileSystemCache(MODX_CORE_PATH . 'cache/valinor');

            if ((bool) $this->modx->getOption(self::NAMESPACE . '.development_mode', null)) {
                $cache = new FileWatchingCache($cache);
            }

            $mapper = (new MapperBuilder())
                ->withCache($cache)
                ->allowPermissiveTypes()
                ->enableFlexibleCasting()
                ->allowSuperfluousKeys()
                ->registerTransformer(
                    static fn (object $value, callable $next) => \array_filter(
                        $next(),
                        static fn (mixed $value) => !$value instanceof HiddenValueDto,
                    ),
                )->registerTransformer(
                    static fn (object $value, callable $next) => \array_filter(
                        $next(),
                        static fn (mixed $value) => null !== $value,
                    ),
                );

            self::$valinorMapper = $mapper;
        }

        return self::$valinorMapper;
    }

    public function getMappper()
    {
        return $this->getValinorMapperBuilder()?->mapper();
    }

    public function getNormalizer()
    {
        return $this->getValinorMapperBuilder()?->normalizer(Format::array());
    }

    public static function getEventsWorkersFromConfigFile(): array
    {
        if (null === self::$eventsWorkers) {
            $file = MODX_CORE_PATH . 'components/' . self::NAMESPACE . '/config/events.php';

            if (\file_exists($file) && \is_writable(\dirname($file))) {
                self::$eventsWorkers = include $file;
            } else {
                self::$eventsWorkers = [];
            }
        }

        return (array) self::$eventsWorkers;
    }
}
