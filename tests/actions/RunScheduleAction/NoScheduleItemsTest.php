<?php

declare(strict_types=1);

namespace corbomite\tests\actions\RunScheduleAction;

use corbomite\schedule\actions\RunScheduleAction;
use corbomite\schedule\ScheduleApi;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class NoScheduleItemsTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $scheduleApi = self::createMock(ScheduleApi::class);

        $scheduleApi->expects(self::once())
            ->method('getSchedule')
            ->willReturn([]);

        $di = self::createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(ScheduleApi::class)
            ->willReturn($scheduleApi);

        $consoleOutput = self::createMock(OutputInterface::class);

        $consoleOutput->expects(self::once())
            ->method('writeln')
            ->with(self::equalTo('<fg=yellow>There are no scheduled commands set up</>'));

        /** @noinspection PhpParamsInspection */
        $action = new RunScheduleAction($di, $consoleOutput);

        $action();
    }
}
