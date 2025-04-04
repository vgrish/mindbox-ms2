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
                'nomenclature_category_templates',
                'nomenclature_product_templates' => \array_filter(
                    \array_map('trim', \explode(',', $value)),
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

    public function getSetting(string $key): null|array|int|string
    {
        return $this->settings[$key] ?? null;
    }
}
