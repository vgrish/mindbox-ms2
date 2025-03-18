<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

use Vgrish\MindBox\MS2\App;
use Vgrish\MindBox\MS2\WorkerResult;
use Vgrish\MindBox\MS2\Workers\Customers\EditCustomer;

/** @var modX $modx */
if ($user = getUser()) {
    $app = new App($modx);
    $worker = new EditCustomer($app, [
        'user' => $user,
    ]);

    return $worker->run();
}

return new WorkerResult(false);
