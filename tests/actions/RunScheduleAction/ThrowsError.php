<?php

declare(strict_types=1);

namespace corbomite\tests\actions\RunScheduleAction;

use Exception;

class ThrowsError
{
    public function __invoke() : void
    {
        throw new Exception('Test Message');
    }
}
