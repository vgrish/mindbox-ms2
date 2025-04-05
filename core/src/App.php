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
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Normalizer\Format;
use CuyZ\Valinor\Normalizer\Normalizer;
use Vgrish\MindBox\MS2\Config\Config;
use Vgrish\MindBox\MS2\Config\SettingsConfig;
use Vgrish\MindBox\MS2\Dto\Entities\HiddenValueDto;
use Vgrish\MindBox\MS2\Http\ApiClient;
use Vgrish\MindBox\MS2\Http\GuzzleSenderFactory;
use Vgrish\MindBox\MS2\Http\RequestSenderFactoryInterface;
use Vgrish\MindBox\MS2\Tools\ClassFinder;
use Vgrish\MindBox\MS2\Tools\Extensions;

class App
{
    public const AUTHOR = 'vgrish';
    public const NAME = 'MindBoxMS2';
    public const NAMESPACE = 'mindbox-ms2';
    public const VERSION = '1.0.6';
    protected ?ApiClient $apiClient = null;

    public function __construct(
        public \modX $modx,
        public Config $config,
    ) {
        $settings = \array_filter(
            $modx->config,
            static fn ($key) => \str_starts_with($key, self::NAMESPACE . '.'),
            \ARRAY_FILTER_USE_KEY,
        );

        $this->config = $config->withSettings(
            SettingsConfig::fromArray($settings),
        );

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

    public static function fromConfigFile(\modX $modx, string $file): self
    {
        $config = null;

        if (\file_exists($file)) {
            $config = require_once $file;
        }

        if (!$config instanceof Config) {
            $config = Config::init();
            $modx->log(\modX::LOG_LEVEL_ERROR, \sprintf('Invalid config file `%s`, expected `%s`', $file, Config::class));
        }

        return self::fromConfig($modx, $config);
    }

    public static function fromConfig(\modX $modx, Config $config): self
    {
        return new self(
            $modx,
            $config,
        );
    }

    public function getSetting(string $key, $default = null, bool $skipEmpty = false): null|array|bool|float|int|string
    {
        $value = $this->config->getSettingConfig()->getSetting($key);

        if ($skipEmpty && empty($value)) {
            $value = $default;
        }

        return $value;
    }

    public function getApiClient(
        array $credentials,
        RequestSenderFactoryInterface $requestSenderFactory = new GuzzleSenderFactory(),
    ): ApiClient {
        if (null === $this->apiClient) {
            $this->apiClient = new ApiClient($credentials, $requestSenderFactory->make());
        }

        return $this->apiClient;
    }

    public function getValinorMapperBuilder(): ?MapperBuilder
    {
        static $mapper = null;

        if (null === $mapper) {
            $cache = new FileSystemCache(MODX_CORE_PATH . 'cache/valinor');

            if ($this->getSetting('development_mode')) {
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
        }

        return $mapper;
    }

    public function getMappper(): ?TreeMapper
    {
        return $this->getValinorMapperBuilder()?->mapper();
    }

    public function getNormalizer(): ?Normalizer
    {
        return $this->getValinorMapperBuilder()?->normalizer(Format::array());
    }

    public function getNomenclatureWebsiteId(null|array|int|\modResource|string $resource = null, null|array|\msopModification $modification = null): null|int|string
    {
        $getter = $this->config->getExtensionsConfig()->getHandlersForExtension(Config::getNomenclatureWebsiteId);

        if (!\is_callable($getter)) {
            $this->modx->log(\modX::LOG_LEVEL_ERROR, \sprintf('Incorrect `%s` getter', Config::getNomenclatureWebsiteId));
            $getter = [Extensions::class, Config::getNomenclatureWebsiteId];
        }

        return $getter($this, $resource, $modification);
    }
}
