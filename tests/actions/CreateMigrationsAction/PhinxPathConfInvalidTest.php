<?php

declare(strict_types=1);

namespace corbomite\tests\actions\CreateMigrationsAction;

use corbomite\schedule\actions\CreateMigrationsAction;
use corbomite\schedule\PhpCalls;
use LogicException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

class PhinxPathConfInvalidTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $srcDir = '/test/src/dir';

        $output = self::createMock(OutputInterface::class);

        $appBasePath = '/test/base/path';

        $fileSystem = self::createMock(Filesystem::class);

        $fileSystem->expects(self::once())
            ->method('exists')
            ->with(self::equalTo($appBasePath . '/phinx.php'))
            ->willReturn(true);

        $phpCalls = self::createMock(PhpCalls::class);

        $phpCalls->expects(self::once())
            ->method('include')
            ->with(self::equalTo($appBasePath . '/phinx.php'))
            ->willReturn([
                'paths' => ['migrations' => '%%PHINX_CONFIG_DIR%%/migrations'],
            ]);

        $phpCalls->expects(self::once())
            ->method('realpath')
            ->with(self::equalTo($appBasePath . '/migrations'))
            ->willReturn(false);

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

        self::assertEquals('Migrations path could not be resolved', $exception->getMessage());
    }
}
