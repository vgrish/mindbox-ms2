<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Config;

use Vgrish\MindBox\MS2\App;

class SettingsConfig
{
    private array $settings;

    public function __construct(
        array $settings = [],
    ) {
        $this->settings = [];

        foreach ($settings as $key => $value) {
            $key = \str_replace(App::NAMESPACE . '.', '', $key);
            $value = match ($key) {
                'development_mode' => (bool) (int) $value,
                'bot_patterns','nomenclature_website_key', 'nomenclature_modification_website_key' ,'nomenclature_website_key_separator' => \trim((string) $value),
                'nomenclature_category_templates', 'nomenclature_product_templates' => \array_filter(
                    \array_map('intval', \explode(',', $value)),
                ),
                default => $value,
            };

            $this->settings[$key] = $value;
        }
    }

    /**
     * @param array{string:mixed} $config
     *
     * @return static
     */
    public static function fromArray(array $config): self
    {
        return new self($config);
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function getSetting(string $key): null|array|bool|float|int|string
    {
        return $this->settings[$key] ?? null;
    }
}
