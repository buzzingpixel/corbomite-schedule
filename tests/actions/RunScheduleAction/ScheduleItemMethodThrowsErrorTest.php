<?php

declare(strict_types=1);

namespace corbomite\tests\actions\RunScheduleAction;

use corbomite\schedule\actions\RunScheduleAction;
use corbomite\schedule\models\ScheduleItemModel;
use corbomite\schedule\ScheduleApi;
use DateTime;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class ScheduleItemMethodThrowsErrorTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $scheduleItemModel = self::createMock(ScheduleItemModel::class);

        $scheduleItemModel->expects(self::at(0))
            ->method('isRunning')
            ->willReturn(false);

        $scheduleItemModel->expects(self::at(1))
            ->method('shouldRun')
            ->willReturn(true);

        $scheduleItemModel->expects(self::at(2))
            ->method('isRunning')
            ->with(self::equalTo(true))
            ->willReturn(true);

        $scheduleItemModel->expects(self::at(3))
            ->method('lastRunStartAt')
            ->willReturnCallback(static function (DateTimeInterface $incomingDateTime) {
                 self::assertEquals((new DateTime())->getTimestamp(), $incomingDateTime->getTimestamp());

                return $incomingDateTime;
            });

        $scheduleItemModel->expects(self::at(4))
            ->method('class')
            ->willReturn(ThrowsError::class);

        $scheduleItemModel->expects(self::at(5))
            ->method('class')
            ->willReturn(ThrowsError::class);

        $scheduleItemModel->expects(self::at(6))
            ->method('method')
            ->willReturn('__invoke');

        $scheduleItemModel->expects(self::at(7))
            ->method('isRunning')
            ->with(self::equalTo(false))
            ->willReturn(false);

        $scheduleItemModel->expects(self::at(8))
            ->method('class')
            ->willReturn(ThrowsError::class);

        $scheduleItemModel->expects(self::at(9))
            ->method('method')
            ->willReturn('__invoke');

        $scheduleApi = self::createMock(ScheduleApi::class);

        $scheduleApi->expects(self::at(0))
            ->method('getSchedule')
            ->willReturn([$scheduleItemModel]);

        $scheduleApi->expects(self::at(1))
            ->method('saveSchedule')
            ->with(self::equalTo($scheduleItemModel));

        $scheduleApi->expects(self::at(2))
            ->method('saveSchedule')
            ->with(self::equalTo($scheduleItemModel));

        $di = self::createMock(ContainerInterface::class);

        $di->expects(self::at(0))
            ->method('get')
            ->with(ScheduleApi::class)
            ->willReturn($scheduleApi);

        $di->expects(self::at(1))
            ->method('has')
            ->with(self::equalTo(ThrowsError::class))
            ->willReturn(false);

        $consoleOutput = self::createMock(OutputInterface::class);

        $consoleOutput->expects(self::at(0))
            ->method('writeln')
            ->with(self::equalTo('<fg=red>There was a problem running a scheduled command.</>'));

        $consoleOutput->expects(self::at(1))
            ->method('writeln')
            ->with(self::equalTo('<fg=red>' . ThrowsError::class . '::__invoke</>'));

        $consoleOutput->expects(self::at(2))
            ->method('writeln')
            ->with(self::equalTo('<fg=red>Message: Test Message</>'));

        /** @noinspection PhpParamsInspection */
        $action = new RunScheduleAction($di, $consoleOutput);

        $action();
    }
}
