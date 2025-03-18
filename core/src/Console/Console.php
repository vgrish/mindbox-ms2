<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\ListCommand;
use Vgrish\MindBox\MS2\App;
use Vgrish\MindBox\MS2\Console\Command\InstallCommand;
use Vgrish\MindBox\MS2\Console\Command\RemoveCommand;

class Console extends Application
{
    protected \modX $modx;

    public function __construct(\modX $modx)
    {
        parent::__construct(App::NAMESPACE);
        $this->modx = $modx;
    }

    protected function getDefaultCommands(): array
    {
        return [
            new ListCommand(),
            new InstallCommand($this->modx),
            new RemoveCommand($this->modx),
        ];
    }
}
