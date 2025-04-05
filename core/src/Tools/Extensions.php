<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Tools;

use Vgrish\MindBox\MS2\App;

class Extensions
{
    public static function isExist(string $service): bool
    {
        $service = \mb_strtolower($service);

        return \file_exists(\sprintf('%s/components/%s/model/%s/', \rtrim(MODX_CORE_PATH, '/'), $service, $service));
    }

    public static function getNomenclatureWebsiteId(App $app, null|array|int|\modResource|string $resource = null, null|array|\msopModification $modification = null): null|int|string
    {
        static $resourceWebsiteKey = null;
        static $modWebsiteKey = null;
        static $websiteKeySeparator = null;
        static $isModification = null;
        static $resourceCache = [];
        static $modCache = [];

        if (null === $resourceWebsiteKey) {
            $resourceWebsiteKey = (string) $app->getSetting('nomenclature_website_key', 'id', true);
        }

        if (null === $modWebsiteKey) {
            $modWebsiteKey = (string) $app->getSetting('nomenclature_modification_website_key');
        }

        if (null === $websiteKeySeparator) {
            $websiteKeySeparator = (string) $app->getSetting('nomenclature_website_key_separator');
        }

        if (null === $isModification) {
            $isModification = self::isExist('msOptionsPrice');
        }

        $modx = $app->modx;
        $resourceWebsiteId = $modWebsiteId = null;

        $id = null;

        if (null !== $resource) {
            if (\is_numeric($resource)) {
                $id = $resource;
            } elseif (\is_array($resource)) {
                $id = $resource['id'] ?? null;
            } elseif (\is_a($resource, \modResource::class)) {
                $id = $resource->get('id');
            }

            $id = (int) $id;

            if ($id) {
                if (\array_key_exists($id, $resourceCache)) {
                    $resourceWebsiteId = $resourceCache[$id];
                } else {
                    if (\is_array($resource) && \array_key_exists($resourceWebsiteKey, $resource)) {
                        $resourceWebsiteId = $resource[$resourceWebsiteKey];
                    } elseif (\is_a($resource, \modResource::class)) {
                        $resourceWebsiteId = $resource->get($resourceWebsiteKey);
                    }

                    if (null === $resourceWebsiteId && ($o = $modx->getObject(\modResource::class, ['id' => $id]))) {
                        $resourceWebsiteId = $o->get($resourceWebsiteKey);
                    }

                    $resourceCache[$id] = $resourceWebsiteId;
                }
            }
        }

        if (null !== $modification && $isModification && !empty($modWebsiteKey)) {
            $mid = null;

            if (\is_array($modification) && \array_key_exists('modification', $modification)) {
                $mid = (int) ($modification['modification'] ?? 0);
            } elseif (\is_array($modification)) {
                $mid = $modification['id'] ?? null;
            } elseif (\is_a($modification, \msopModification::class)) {
                $mid = $modification->get('id');
            }

            $mid = (int) $mid;

            if ($mid) {
                if (\array_key_exists($mid, $modCache)) {
                    $modWebsiteId = $modCache[$mid];
                } else {
                    if (\is_array($modification) && !\array_key_exists('modification', $modification) && \array_key_exists($modWebsiteKey, $modification)) {
                        $modWebsiteId = $modification[$modWebsiteKey];
                    } elseif (\is_a($modification, \msopModification::class)) {
                        $modWebsiteId = $modification->get($modWebsiteKey);
                    }

                    if (null === $modWebsiteId && ($o = $modx->getObject(\msopModification::class, ['id' => $mid, 'rid' => (int) $id]))) {
                        $modWebsiteId = $o->get($modWebsiteKey);
                    }

                    $modCache[$mid] = $modWebsiteId;
                }
            }
        }

        if (empty($resourceWebsiteId) && empty($modWebsiteId)) {
            return null;
        }

        $websiteId = [];

        if (!empty($websiteKeySeparator)) {
            if (!empty($resourceWebsiteId)) {
                $websiteId[] = $resourceWebsiteId;
            }

            if (!empty($modWebsiteId)) {
                $websiteId[] = $modWebsiteId;
            }

            $websiteId = \implode($websiteKeySeparator, $websiteId);
        } else {
            if (!empty($resourceWebsiteId)) {
                $websiteId = $resourceWebsiteId;
            }

            if (!empty($modWebsiteId)) {
                $websiteId = $modWebsiteId;
            }
        }

        return !empty($websiteId) ? $websiteId : null;
    }
}
