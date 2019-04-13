<?php

declare(strict_types=1);

namespace corbomite\schedule\services;

use DateTimeImmutable;
use DateTimeInterface;

class GetCurrentDateTimeService
{
    public function __invoke() : DateTimeInterface
    {
        return $this->get();
    }

    public function get() : DateTimeInterface
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return new DateTimeImmutable();
    }
}
