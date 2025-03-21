<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2;

interface WebHookInterface
{
    public function run(bool $debug = false): WebHookResult;

    public function process(): WebHookResult;

    public function error(array $data = []): WebHookResult;

    public function success(array $data = []): WebHookResult;

    public function log(array $data = []): void;
}
