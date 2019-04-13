<?php

declare(strict_types=1);

namespace corbomite\tests\services\SaveScheduleService;

use corbomite\db\Factory as OrmFactory;
use corbomite\schedule\models\ScheduleItemModel;
use corbomite\schedule\services\SaveScheduleService;
use PHPUnit\Framework\TestCase;
use Throwable;

class NoGuidTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $model = self::createMock(ScheduleItemModel::class);

        $model->expects(self::once())
            ->method('guid')
            ->willReturn('');

        $ormFactory = self::createMock(OrmFactory::class);

        $ormFactory->expects(self::never())
            ->method(self::anything());

        /** @noinspection PhpParamsInspection */
        $service = new SaveScheduleService($ormFactory);

        /** @noinspection PhpParamsInspection */
        $service($model);
    }
}
