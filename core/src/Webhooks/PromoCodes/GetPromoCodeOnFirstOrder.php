<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Webhooks\PromoCodes;

use Vgrish\MindBox\MS2\WebHook;
use Vgrish\MindBox\MS2\WebHookResult;

class GetPromoCodeOnFirstOrder extends WebHook
{
    public function process(): WebHookResult
    {
        return $this->success();
    }
}
