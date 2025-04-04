<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Config;

final class Config
{
    public const getNomenclatureWebsiteId = 'getNomenclatureWebsiteId';
    private SettingsConfig $settingsConfig;
    private WorkersConfig $workersConfig;
    private WebHooksConfig $webHooksConfig;
    private ExtensionsConfig $extensionsConfig;

    private function __construct()
    {
    }

    public function getSettingConfig(): SettingsConfig
    {
        return $this->settingsConfig;
    }

    public function getWorkersConfig(): WorkersConfig
    {
        return $this->workersConfig;
    }

    public function getWebhooksConfig(): WebHooksConfig
    {
        return $this->webHooksConfig;
    }

    public function getExtensionsConfig(): ExtensionsConfig
    {
        return $this->extensionsConfig;
    }

    public static function init(): self
    {
        $cfg = new self();

        return $cfg
            ->withSettings(SettingsConfig::fromArray([]))
            ->withWorkers(WorkersConfig::fromArray([]))
            ->withWebHooks(WebHooksConfig::fromArray([]))
            ->withExtensions(ExtensionsConfig::fromArray([]));
    }

    public function withSettings(SettingsConfig $settingsConfig): self
    {
        $cfg = clone $this;
        $cfg->settingsConfig = $settingsConfig;

        return $cfg;
    }

    public function withWorkers(WorkersConfig $workersConfig): self
    {
        $cfg = clone $this;
        $cfg->workersConfig = $workersConfig;

        return $cfg;
    }

    public function withWebHooks(WebHooksConfig $webHooksConfig): self
    {
        $cfg = clone $this;
        $cfg->webHooksConfig = $webHooksConfig;

        return $cfg;
    }

    public function withExtensions(ExtensionsConfig $extensionsConfig): self
    {
        $cfg = clone $this;
        $cfg->extensionsConfig = $extensionsConfig;

        return $cfg;
    }
}
