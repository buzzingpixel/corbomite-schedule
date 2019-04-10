<?php

declare(strict_types=1);

namespace corbomite\tests\services\GetScheduleServiceTest;

use corbomite\configcollector\Collector;
use corbomite\db\Factory;
use corbomite\schedule\services\GetScheduleService;
use PHPUnit\Framework\TestCase;
use Throwable;

class DbDoesNotHaveGuidsTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $ormFactory = self::createMock(Factory::class);

        $ormFactory->expects(self::never())
            ->method('makeOrm');

        $configCollector = self::createMock(Collector::class);

        $configCollector->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo('scheduleConfigFilePath'))
            ->willReturn([]);

        /** @noinspection PhpParamsInspection */
        $service = new GetScheduleService($ormFactory, $configCollector);

        $result = $service();

        self::assertCount(0, $result);
    }
}
