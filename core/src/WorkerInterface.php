<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2;

interface WorkerInterface
{
    public function run(bool $debug = false): WorkerResult;

    public function process(): WorkerResult;

    public function error(array $data = []): WorkerResult;

    public function success(array $data = []): WorkerResult;

    public function log(array $data = []): void;

    public function operation(): string;

    public function isAsyncOperation(): bool;

    public function isClientRequired(): bool;
}
