<?php

declare(strict_types=1);

namespace corbomite\tests\services;

use corbomite\schedule\services\GetCurrentDateTimeService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class GetCurrentDateTimeServiceTest extends TestCase
{
    public function test() : void
    {
        $service = new GetCurrentDateTimeService();

        self::assertInstanceOf(DateTimeImmutable::class, $service());
    }
}
