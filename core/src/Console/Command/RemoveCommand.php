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

class RemoveCommand extends Command
{
    protected static $defaultName = 'remove';
    protected static $defaultDescription = 'Remove "' . App::NAMESPACE . '" extra from MODX 2';

    public function run(InputInterface $input, OutputInterface $output): int
    {
        $srcPath = MODX_CORE_PATH . 'vendor/' . App::AUTHOR . '/' . App::NAMESPACE;
        $corePath = MODX_CORE_PATH . 'components/' . App::NAMESPACE;
        $assetsPath = MODX_ASSETS_PATH . 'components/' . App::NAMESPACE;

        if (\is_dir($corePath)) {
            \unlink($corePath);
            $output->writeln('<info>Removed symlink for "core"</info>');
        }

        if (\is_dir($assetsPath)) {
            \unlink($assetsPath);
            $output->writeln('<info>Removed symlink for "assets"</info>');
        }

        $modx = $this->modx;

        if ($namespace = $modx->getObject(\modNamespace::class, ['name' => App::NAME])) {
            $namespace->remove();
            $output->writeln('<info>Removed namespace "' . App::NAME . '"</info>');
        }

        if ($category = $modx->getObject(\modCategory::class, ['category' => App::NAMESPACE])) {
            if ($plugins = $modx->getCollection(\modPlugin::class, ['category' => $category->get('id')])) {
                foreach ($plugins as $plugin) {
                    $plugin->remove();
                    $output->writeln('<info>Removed plugin "' . $plugin->get('name') . '"</info>');
                }
            }

            $category->remove();
            $output->writeln('<info>Removed category "' . App::NAME . '"</info>');
        }

        if ($menu = $modx->getObject(\modMenu::class, ['namespace' => App::NAME])) {
            $menu->remove();
            $output->writeln('<info>Removed menu "' . App::NAME . '"</info>');
        }

        $modx->getCacheManager()->refresh();
        $output->writeln('<info>Cleared MODX cache</info>');

        return Command::SUCCESS;
    }
}
