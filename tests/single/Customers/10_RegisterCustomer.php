<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

echo '<pre>';

use Vgrish\MindBox\MS2\App;
use Vgrish\MindBox\MS2\WorkerResult;
use Vgrish\MindBox\MS2\Workers\Customers\RegisterCustomer;

/** @var modX $modx */
if ($user = getUser()) {
    $app = new App($modx);
    $worker = new RegisterCustomer($app, [
        'user' => $user,
        'mode' => modSystemEvent::MODE_NEW,
    ]);

    return $worker->run();
}

return new WorkerResult(false);
