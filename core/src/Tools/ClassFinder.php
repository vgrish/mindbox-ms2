<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Tools;

class ClassFinder
{
    protected static array $classes = [];

    public static function classes(): array
    {
        if (empty(self::$classes)) {
            $defined = \array_merge(
                \get_declared_traits(),
                \get_declared_interfaces(),
                \get_declared_classes(),
            );

            $classmapFile = MODX_CORE_PATH . '/vendor/composer/autoload_classmap.php';

            if (\is_readable($classmapFile)) {
                $classmap = require $classmapFile;

                if ($classmap) {
                    $defined = \array_merge($defined, \array_keys($classmap));
                }
            }

            self::$classes = \array_unique($defined);
        }

        return self::$classes;
    }

    public static function findByRegex(string $pattern): array
    {
        static $cache = [];

        if (isset($cache[$pattern])) {
            return $cache[$pattern];
        }

        $classes = [];

        foreach (self::classes() as $class) {
            if (\preg_match($pattern, $class)) {
                $classes[] = $class;
            }
        }

        $cache[$pattern] = $classes;

        return $classes;
    }
}
