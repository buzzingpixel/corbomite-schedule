<?php

declare(strict_types=1);

namespace corbomite\tests;

use corbomite\schedule\models\ScheduleItemModel;
use corbomite\schedule\ScheduleApi;
use corbomite\schedule\services\GetScheduleService;
use corbomite\schedule\services\SaveScheduleService;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Throwable;

class ScheduleApiTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testGetSchedule() : void
    {
        $returnArray = [
            'testItemOne',
            'testItemTwo',
        ];

        $getScheduleService = self::createMock(GetScheduleService::class);

        $getScheduleService->expects(self::once())
            ->method('get')
            ->willReturn($returnArray);

        $di = self::createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(self::equalTo(GetScheduleService::class))
            ->willReturn($getScheduleService);

        /** @noinspection PhpParamsInspection */
        $scheduleApi = new ScheduleApi($di);

        self::assertEquals($returnArray, $scheduleApi->getSchedule());
    }

    /**
     * @throws Throwable
     */
    public function testSaveSchedule() : void
    {
        $model = self::createMock(ScheduleItemModel::class);

        $saveScheduleService = self::createMock(SaveScheduleService::class);

        $saveScheduleService->expects(self::once())
            ->method('save')
            ->with(self::equalTo($model));

        $di = self::createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(self::equalTo(SaveScheduleService::class))
            ->willReturn($saveScheduleService);

        /** @noinspection PhpParamsInspection */
        $scheduleApi = new ScheduleApi($di);

        /** @noinspection PhpParamsInspection */
        $scheduleApi->saveSchedule($model);
    }
}
