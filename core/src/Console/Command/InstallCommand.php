<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vgrish\MindBox\MS2\App;

class InstallCommand extends Command
{
    protected static $defaultName = 'install';
    protected static $defaultDescription = 'Install "' . App::NAMESPACE . '" extra for MODX 2';

    public function run(InputInterface $input, OutputInterface $output): int
    {
        $modx = $this->modx;

        $srcPath = MODX_CORE_PATH . 'vendor/' . App::AUTHOR . '/' . App::NAMESPACE;
        $corePath = MODX_CORE_PATH . 'components/' . App::NAMESPACE;
        $assetsPath = MODX_ASSETS_PATH . 'components/' . App::NAMESPACE;

        if (!\is_dir($corePath)) {
            \symlink($srcPath . '/core', $corePath);
            $output->writeln('<info>Created symlink for "core"</info>');
        }

        if (!\is_dir($assetsPath)) {
            \symlink($srcPath . '/assets', $assetsPath);
            $output->writeln('<info>Created symlink for "assets"</info>');
        }

        if (!$modx->getObject(\modNamespace::class, ['name' => App::NAME])) {
            $namespace = new \modNamespace($modx);
            $namespace->fromArray(
                [
                    'name' => App::NAME,
                    'path' => '{core_path}components/' . App::NAMESPACE . '/',
                    'assets_path' => '{assets_path}components/' . App::NAMESPACE . '/',
                ],
                false,
                true,
            );
            $namespace->save();
            $output->writeln(\sprintf('<info>Created namespace `%s`</info>', App::NAME));
        }

        if (!$category = $modx->getObject(\modCategory::class, ['category' => App::NAMESPACE])) {
            $category = new \modCategory($modx);
            $category->fromArray(
                [
                    'category' => App::NAMESPACE,
                    'parent' => 0,
                ],
                false,
                true,
            );
            $category->save();
            $output->writeln(\sprintf('<info>Created category `%s`</info>', App::NAMESPACE));
        }

        $categoryId = $category->get('id');

        // SETTINGS
        $key = App::NAMESPACE . '.development_mode';

        if (!$modx->getObject(\modSystemSetting::class, $key)) {
            $setting = new \modSystemSetting($modx);
            $setting->fromArray(
                [
                    'key' => $key,
                    'namespace' => App::NAME,
                    'xtype' => 'combo-boolean',
                    'value' => false,
                ],
                false,
                true,
            );
            $setting->save();
            $output->writeln(\sprintf('<info>Created system setting `%s`</info>', $key));
        }

        $key = App::NAMESPACE . '.api_endpoint_id';

        if (!$modx->getObject(\modSystemSetting::class, $key)) {
            $setting = new \modSystemSetting($modx);
            $setting->fromArray(
                [
                    'key' => $key,
                    'namespace' => App::NAME,
                    'xtype' => 'textfield',
                    'value' => 'test.Website',
                ],
                false,
                true,
            );
            $setting->save();
            $output->writeln(\sprintf('<info>Created system setting `%s`</info>', $key));
        }

        $key = App::NAMESPACE . '.api_secret_key';

        if (!$modx->getObject(\modSystemSetting::class, $key)) {
            $setting = new \modSystemSetting($modx);
            $setting->fromArray(
                [
                    'key' => $key,
                    'namespace' => App::NAME,
                    'xtype' => 'textfield',
                    'value' => '',
                ],
                false,
                true,
            );
            $setting->save();
            $output->writeln(\sprintf('<info>Created system setting `%s`</info>', $key));
        }

        $key = App::NAMESPACE . '.webhook_secret_key';

        if (!$modx->getObject(\modSystemSetting::class, $key)) {
            $setting = new \modSystemSetting($modx);
            $setting->fromArray(
                [
                    'key' => $key,
                    'namespace' => App::NAME,
                    'xtype' => 'textfield',
                    'value' => '',
                ],
                false,
                true,
            );
            $setting->save();
            $output->writeln(\sprintf('<info>Created system setting `%s`</info>', $key));
        }

        $key = App::NAMESPACE . '.bot_patterns';

        if (!$modx->getObject(\modSystemSetting::class, $key)) {
            $setting = new \modSystemSetting($modx);
            $setting->fromArray(
                [
                    'key' => $key,
                    'namespace' => App::NAME,
                    'xtype' => 'textfield',
                    'value' => 'Yandex|Google|Yahoo|Rambler|Mail|Bot|Spider|Snoopy|Crawler|Finder|curl|Wget|Go-http-client|Postman',
                ],
                false,
                true,
            );
            $setting->save();
            $output->writeln(\sprintf('<info>Created system setting `%s`</info>', $key));
        }

        $key = App::NAMESPACE . '.nomenclature_website_key';

        if (!$modx->getObject(\modSystemSetting::class, $key)) {
            $setting = new \modSystemSetting($modx);
            $setting->fromArray(
                [
                    'key' => $key,
                    'namespace' => App::NAME,
                    'xtype' => 'textfield',
                    'value' => 'id',
                ],
                false,
                true,
            );
            $setting->save();
            $output->writeln(\sprintf('<info>Created system setting `%s`</info>', $key));
        }

        $key = App::NAMESPACE . '.nomenclature_modification_website_key';

        if (!$modx->getObject(\modSystemSetting::class, $key)) {
            $setting = new \modSystemSetting($modx);
            $setting->fromArray(
                [
                    'key' => $key,
                    'namespace' => App::NAME,
                    'xtype' => 'textfield',
                    'value' => 'id',
                ],
                false,
                true,
            );
            $setting->save();
            $output->writeln(\sprintf('<info>Created system setting `%s`</info>', $key));
        }

        $key = App::NAMESPACE . '.nomenclature_united_website_key';

        if (!$modx->getObject(\modSystemSetting::class, $key)) {
            $setting = new \modSystemSetting($modx);
            $setting->fromArray(
                [
                    'key' => $key,
                    'namespace' => App::NAME,
                    'xtype' => 'combo-boolean',
                    'value' => true,
                ],
                false,
                true,
            );
            $setting->save();
            $output->writeln(\sprintf('<info>Created system setting `%s`</info>', $key));
        }

        $key = App::NAMESPACE . '.nomenclature_category_templates';

        if (!$modx->getObject(\modSystemSetting::class, $key)) {
            $setting = new \modSystemSetting($modx);
            $setting->fromArray(
                [
                    'key' => $key,
                    'namespace' => App::NAME,
                    'xtype' => 'textfield',
                    'value' => '',
                ],
                false,
                true,
            );
            $setting->save();
            $output->writeln(\sprintf('<info>Created system setting `%s`</info>', $key));
        }

        $key = App::NAMESPACE . '.nomenclature_product_templates';

        if (!$modx->getObject(\modSystemSetting::class, $key)) {
            $setting = new \modSystemSetting($modx);
            $setting->fromArray(
                [
                    'key' => $key,
                    'namespace' => App::NAME,
                    'xtype' => 'textfield',
                    'value' => '',
                ],
                false,
                true,
            );
            $setting->save();
            $output->writeln(\sprintf('<info>Created system setting `%s`</info>', $key));
        }

        $schemaFile = $corePath . '/schema/' . App::NAMESPACE . '.mysql.schema.xml';

        if (\file_get_contents($schemaFile)) {
            $modx->addPackage(
                App::NAME,
                MODX_CORE_PATH . 'components/' . App::NAMESPACE . '/src/Models/' . App::NAME . '/',
            );

            if ($cache = $modx->getCacheManager()) {
                $cache->deleteTree(
                    $corePath . '/src/Models/' . App::NAME . '/' . App::NAME . '/mysql',
                    ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []],
                );
            }

            $manager = $modx->getManager();
            $generator = $manager->getGenerator();

            if (!$generator->parseSchema($schemaFile, $corePath . '/src/Models/' . App::NAME . '/')) {
                $output->writeln(
                    \sprintf('<error>Model regeneration failed! Error parsing schema `%s`</error>', $schemaFile),
                );

                unset($manager);
            } else {
                $output->writeln(
                    \sprintf('<info>Regeneration of model files completed successfully `%s`</info>', $schemaFile),
                );
            }

            if (isset($manager)) {
                $this->updateTables($schemaFile, $output);
            }
        }

        $config = $corePath . '/config.php';

        if (!\file_exists($config)) {
            \copy($config . '.inc', $config);
            \chmod($config, 0o644);
        }

        if (!$plugin = $modx->getObject(\modPlugin::class, ['name' => App::NAME])) {
            $plugin = new \modPlugin($modx);
            $plugin->fromArray(
                [
                    'name' => App::NAME,
                    'description' => '',
                    'source' => 1,
                    'static' => true,
                    'static_file' => \str_replace(MODX_BASE_PATH, '', $corePath . '/elements/plugins/plugin.php'),
                    'category' => $categoryId,
                    'propertiers' => [],
                ],
                false,
                true,
            );

            $plugin->save();
            $output->writeln(\sprintf('<info>Created plugin `%s`</info>', $plugin->get('name')));
        }

        foreach (
            [
                // '-----------------',
                'OnUserSave',
                'OnUserFormSave',
                'OnWebLogin',
                // '-----------------',
                'OnWebPagePrerender',
                'msopOnViewModification',
                'msOnAddToCart',
                'msOnChangeInCart',
                'msOnRemoveFromCart',
                'msOnEmptyCart',
                // '-----------------',
                'msOnSaveOrder',
                'msOnChangeOrderStatus',
            ] as $eventName
        ) {
            if (!$modx->getObject(\modPluginEvent::class, [
                'pluginid' => $plugin->get('id'),
                'event' => $eventName,
            ])) {
                $event = $modx->newObject(\modPluginEvent::class);
                $event->fromArray(
                    [
                        'event' => $eventName,
                        'pluginid' => $plugin->get('id'),
                        'priority' => 9999,
                        'propertyset' => 0,
                    ],
                    '',
                    true,
                    true,
                );
                $event->save();
                $output->writeln(
                    \sprintf('<info>Added event `%s` to plugin `%s`</info>', $eventName, $plugin->get('name')),
                );
            }
        }

        if (!$snippet = $modx->getObject(\modSnippet::class, ['name' => App::NAME . '.nomenclature.feed'])) {
            $snippet = $modx->newObject(\modSnippet::class);
            $snippet->fromArray(
                [
                    'name' => App::NAME . '.Nomenclature.feed',
                    'description' => '',
                    'source' => 1,
                    'static' => true,
                    'static_file' => \str_replace(MODX_BASE_PATH, '', $corePath . '/elements/snippets/nomenclature.feed.php'),
                    'category' => $categoryId,
                    'propertiers' => [],
                    'new' => true,
                ],
                '',
                true,
                true,
            );
        }

        $snippet->set('properties', [
            'showDeleted' => [
                'name' => 'showDeleted',
                'desc' => '',
                'type' => 'combo-boolean',
                'value' => false,
                'lexicon' => '',
            ],
            'showUnpublished' => [
                'name' => 'showUnpublished',
                'desc' => '',
                'type' => 'combo-boolean',
                'value' => false,
                'lexicon' => '',
            ],
            'includeContent' => [
                'name' => 'includeContent',
                'desc' => '',
                'type' => 'combo-boolean',
                'value' => false,
                'lexicon' => '',
            ],
            'useBasicAuth' => [
                'name' => 'useBasicAuth',
                'desc' => '',
                'type' => 'combo-boolean',
                'value' => false,
                'lexicon' => '',
            ],
            'authUsername' => [
                'name' => 'authUsername',
                'desc' => '',
                'type' => 'textfield',
                'value' => '',
                'lexicon' => '',
            ],
            'authPassword' => [
                'name' => 'authPassword',
                'desc' => '',
                'type' => 'textfield',
                'value' => '',
                'lexicon' => '',
            ],
        ]);

        $snippet->save();
        $output->writeln(\sprintf('<info>%s snippet `%s`</info>', $snippet->get('new') ? 'Created' : 'Updated', $snippet->get('name')));

        if (!$snippet = $modx->getObject(\modSnippet::class, ['name' => App::NAME . '.customers.feed'])) {
            $snippet = $modx->newObject(\modSnippet::class);
            $snippet->fromArray(
                [
                    'name' => App::NAME . '.Customers.feed',
                    'description' => '',
                    'source' => 1,
                    'static' => true,
                    'static_file' => \str_replace(MODX_BASE_PATH, '', $corePath . '/elements/snippets/customers.feed.php'),
                    'category' => $categoryId,
                    'propertiers' => [],
                    'new' => true,
                ],
                '',
                true,
                true,
            );
        }

        $snippet->set('properties', [
            'showInactive' => [
                'name' => 'showInactive',
                'desc' => '',
                'type' => 'combo-boolean',
                'value' => false,
                'lexicon' => '',
            ],
            'showBlocked' => [
                'name' => 'showBlocked',
                'desc' => '',
                'type' => 'combo-boolean',
                'value' => false,
                'lexicon' => '',
            ],
            'useBasicAuth' => [
                'name' => 'useBasicAuth',
                'desc' => '',
                'type' => 'combo-boolean',
                'value' => true,
                'lexicon' => '',
            ],
            'authUsername' => [
                'name' => 'authUsername',
                'desc' => '',
                'type' => 'textfield',
                'value' => '',
                'lexicon' => '',
            ],
            'authPassword' => [
                'name' => 'authPassword',
                'desc' => '',
                'type' => 'textfield',
                'value' => '',
                'lexicon' => '',
            ],
            'enclosure' => [
                'name' => 'enclosure',
                'desc' => '',
                'type' => 'textfield',
                'value' => '"',
                'lexicon' => '',
            ],
            'separator' => [
                'name' => 'separator',
                'desc' => '',
                'type' => 'textfield',
                'value' => ';',
                'lexicon' => '',
            ],
        ]);

        $snippet->save();
        $output->writeln(\sprintf('<info>%s snippet `%s`</info>', $snippet->get('new') ? 'Created' : 'Updated', $snippet->get('name')));

        $modx->getCacheManager()->refresh();
        $output->writeln('<info>Cleared MODX cache</info>');

        return Command::SUCCESS;
    }

    public function updateTables($schemaFile, $output): void
    {
        $modx = $this->modx;
        $manager = $modx->getManager();
        $schema = new \SimpleXMLElement($schemaFile, 0, true);
        $objects = [];

        if (isset($schema->object)) {
            foreach ($schema->object as $obj) {
                $objects[] = (string) $obj['class'];
            }
        }

        foreach ($objects as $class) {
            if (!$table = $modx->getTableName($class)) {
                $output->writeln(\sprintf('<error>I can\'t get a table for the class `%s`</error>', $class));

                continue;
            }

            $sql = "SHOW TABLES LIKE '" . \trim($table, '`') . "'";
            $stmt = $modx->prepare($sql);
            $newTable = true;

            if ($stmt->execute() && $stmt->fetchAll()) {
                $newTable = false;
            }

            // If the table is just created
            if ($newTable) {
                $manager->createObjectContainer($class);
                $output->writeln(\sprintf('<info>Create table `%s`</info>', $class));
            } else {
                // If the table exists
                // 1. Operate with tables
                $tableFields = [];
                $c = $modx->prepare("SHOW COLUMNS IN {$modx->getTableName($class)}");
                $c->execute();

                while ($cl = $c->fetch(\PDO::FETCH_ASSOC)) {
                    $tableFields[$cl['Field']] = $cl['Field'];
                }

                foreach ($modx->getFields($class) as $field => $v) {
                    if (\in_array($field, $tableFields, true)) {
                        unset($tableFields[$field]);
                        $manager->alterField($class, $field);
                    } else {
                        $manager->addField($class, $field);
                    }
                }

                foreach ($tableFields as $field) {
                    $manager->removeField($class, $field);
                }

                // 2. Operate with indexes
                $indexes = [];
                $c = $modx->prepare("SHOW INDEX FROM {$modx->getTableName($class)}");
                $c->execute();

                while ($row = $c->fetch(\PDO::FETCH_ASSOC)) {
                    $name = $row['Key_name'];

                    if (!isset($indexes[$name])) {
                        $indexes[$name] = [$row['Column_name']];
                    } else {
                        $indexes[$name][] = $row['Column_name'];
                    }
                }

                foreach ($indexes as $name => $values) {
                    \sort($values);
                    $indexes[$name] = \implode(':', $values);
                }

                $map = $modx->getIndexMeta($class);

                // Remove old indexes
                foreach ($indexes as $key => $index) {
                    if (!isset($map[$key])) {
                        if ($manager->removeIndex($class, $key)) {
                            $output->writeln(
                                \sprintf('<info>Removed index `%s` of the table `%s`</info>', $key, $class),
                            );
                        }
                    }
                }

                // Add or alter existing
                foreach ($map as $key => $index) {
                    \ksort($index['columns']);
                    $index = \implode(':', \array_keys($index['columns']));

                    if (!isset($indexes[$key])) {
                        if ($manager->addIndex($class, $key)) {
                            $output->writeln(\sprintf('<info>Added index `%s` in the table `%s`</info>', $key, $class));
                        }
                    } else {
                        if ($index !== $indexes[$key]) {
                            if ($manager->removeIndex($class, $key) && $manager->addIndex($class, $key)) {
                                $output->writeln(
                                    \sprintf('<info>Updated index `%s` of the table `%s`</info>', $key, $class),
                                );
                            }
                        }
                    }
                }
            }
            // END FOREACH
        }
        // END FUNC
    }
}
