<?php

declare(strict_types=1);

namespace corbomite\tests\actions\RunScheduleAction;

class DoesNotThrowError
{
    /** @var int */
    private $runCounter = 0;

    public function runCounter() : int
    {
        return $this->runCounter;
    }

    public function customMethod() : void
    {
        $this->runCounter++;
    }
}
