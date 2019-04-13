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

class DiHasClassAndIsSuccessfulTest extends TestCase
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
            ->willReturn(DoesNotThrowError::class);

        $scheduleItemModel->expects(self::at(5))
            ->method('class')
            ->willReturn(DoesNotThrowError::class);

        $scheduleItemModel->expects(self::at(6))
            ->method('method')
            ->willReturn('customMethod');

        $scheduleItemModel->expects(self::at(7))
            ->method('isRunning')
            ->with(self::equalTo(false))
            ->willReturn(false);

        $scheduleItemModel->expects(self::at(8))
            ->method('lastRunEndAt')
            ->willReturnCallback(static function (DateTimeInterface $incomingDateTime) {
                self::assertEquals((new DateTime())->getTimestamp(), $incomingDateTime->getTimestamp());

                return $incomingDateTime;
            });

        $scheduleItemModel->expects(self::at(9))
            ->method('class')
            ->willReturn(DoesNotThrowError::class);

        $scheduleItemModel->expects(self::at(10))
            ->method('method')
            ->willReturn('customMethod');

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
            ->with(self::equalTo(DoesNotThrowError::class))
            ->willReturn(true);

        $doesNotThrowError = new DoesNotThrowError();

        $di->expects(self::at(2))
            ->method('get')
            ->with(self::equalTo(DoesNotThrowError::class))
            ->willReturn($doesNotThrowError);

        $consoleOutput = self::createMock(OutputInterface::class);

        $consoleOutput->expects(self::once())
            ->method('writeln')
            ->with(self::equalTo(
                '<fg=green>' . DoesNotThrowError::class . '::customMethod ran successfully</>'
            ));

        /** @noinspection PhpParamsInspection */
        $action = new RunScheduleAction($di, $consoleOutput);

        self::assertEquals(0, $doesNotThrowError->runCounter());

        $action();

        self::assertEquals(1, $doesNotThrowError->runCounter());
    }
}
