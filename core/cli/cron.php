<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

use Go\Job;
use GO\Scheduler;

require \dirname(__DIR__) . '/bootstrap.php';

$scheduler = new Scheduler();

$scheduler->php(__DIR__ . '/send-events.php', null, [], 'send_events')
    ->everyMinute(3)
    ->inForeground()
    ->onlyOne();

$scheduler->php(__DIR__ . '/remove-events.php', null, [], 'remove_events')
    ->hourly()
    ->inForeground()
    ->onlyOne();

$executed = $scheduler->run();

/** @var Job $job */
foreach ($executed as $job) {
    if ($output = $job->getOutput()) {
        if (\is_array($output)) {
            $output = \implode("\n", $output);
        }

        echo $output;
    }
}
