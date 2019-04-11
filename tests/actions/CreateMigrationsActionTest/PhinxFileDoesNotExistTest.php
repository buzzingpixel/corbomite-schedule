<?php

declare(strict_types=1);

namespace corbomite\tests\actions\CreateMigrationsActionTest;

use corbomite\schedule\actions\CreateMigrationsAction;
use corbomite\schedule\PhpCalls;
use LogicException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

class PhinxFileDoesNotExistTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $srcDir = '/test/src/dir';

        $output = self::createMock(OutputInterface::class);

        $appBasePath = '/test/app/base/path';

        $fileSystem = self::createMock(Filesystem::class);

        $fileSystem->expects(self::once())
            ->method('exists')
            ->with(self::equalTo($appBasePath . '/phinx.php'))
            ->willReturn(false);

        $phpCalls = self::createMock(PhpCalls::class);

        /** @noinspection PhpParamsInspection */
        $createMigrationsAction = new CreateMigrationsAction(
            $srcDir,
            $output,
            $appBasePath,
            $fileSystem,
            $phpCalls
        );

        $exception = null;

        try {
            $createMigrationsAction();
        } catch (LogicException $e) {
            $exception = $e;
        }

        self::assertInstanceOf(LogicException::class, $exception);

        self::assertEquals('phinx.php must be present in your project', $exception->getMessage());
    }
}
