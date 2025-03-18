<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Console\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;

abstract class Command extends SymfonyCommand
{
    public const SUCCESS = SymfonyCommand::SUCCESS;
    public const FAILURE = SymfonyCommand::FAILURE;
    public const INVALID = SymfonyCommand::INVALID;
    protected \modX $modx;

    public function __construct(\modX $modx, ?string $name = null)
    {
        parent::__construct($name);
        $this->modx = $modx;
    }
}
