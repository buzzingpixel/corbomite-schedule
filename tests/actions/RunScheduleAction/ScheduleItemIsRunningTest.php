<?php

declare(strict_types=1);

namespace corbomite\tests\actions\RunScheduleAction;

use corbomite\schedule\actions\RunScheduleAction;
use corbomite\schedule\models\ScheduleItemModel;
use corbomite\schedule\ScheduleApi;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class ScheduleItemIsRunningTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $scheduleItemModel = self::createMock(ScheduleItemModel::class);

        $scheduleItemModel->expects(self::once())
            ->method('isRunning')
            ->willReturn(true);

        $scheduleItemModel->expects(self::once())
            ->method('shouldRun')
            ->willReturn(false);

        $scheduleItemModel->expects(self::once())
            ->method('class')
            ->willReturn('TestClass');

        $scheduleItemModel->expects(self::once())
            ->method('method')
            ->willReturn('TestMethod');

        $scheduleApi = self::createMock(ScheduleApi::class);

        $scheduleApi->expects(self::once())
            ->method('getSchedule')
            ->willReturn([$scheduleItemModel]);

        $di = self::createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(ScheduleApi::class)
            ->willReturn($scheduleApi);

        $consoleOutput = self::createMock(OutputInterface::class);

        $consoleOutput->expects(self::once())
            ->method('writeln')
            ->with(self::equalTo('<fg=yellow>TestClass::TestMethod is currently running</>'));

        /** @noinspection PhpParamsInspection */
        $action = new RunScheduleAction($di, $consoleOutput);

        $action();
    }
}
