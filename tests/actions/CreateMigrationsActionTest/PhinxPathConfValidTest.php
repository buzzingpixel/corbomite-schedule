<?php

declare(strict_types=1);

namespace corbomite\tests\actions\CreateMigrationsActionTest;

use ArrayIterator;
use corbomite\schedule\actions\CreateMigrationsAction;
use corbomite\schedule\PhpCalls;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

class PhinxPathConfValidTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $srcDir = '/test/src/dir';

        $appBasePath = '/test/app/base/path';

        $output = self::createMock(OutputInterface::class);

        $output->expects(self::at(0))
            ->method('writeln')
            ->with(
                self::equalTo(
                    '<fg=green>MigFileOne.php already exists.</>'
                )
            );

        $output->expects(self::at(1))
            ->method('writeln')
            ->with(
                self::equalTo(
                    '<fg=green>MigFileTwo.php created.</>'
                )
            );

        $fileSystem = self::createMock(Filesystem::class);

        $fileSystem->expects(self::at(0))
            ->method('exists')
            ->with(self::equalTo($appBasePath . '/phinx.php'))
            ->willReturn(true);

        $fileSystem->expects(self::at(1))
            ->method('exists')
            ->with(self::equalTo('/testRealPathReturn/MigFileOne.php'))
            ->willReturn(true);

        $fileSystem->expects(self::at(2))
            ->method('exists')
            ->with(self::equalTo('/testRealPathReturn/MigFileTwo.php'))
            ->willReturn(false);

        $fileSystem->expects(self::at(3))
            ->method('copy')
            ->with(
                self::equalTo($srcDir . '/MigFileTwo.php'),
                self::equalTo('/testRealPathReturn/MigFileTwo.php')
            );

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
            ->willReturn('/testRealPathReturn');

        $iterator = new ArrayIterator([
            ['MigFileOne.php'],
            ['MigFileTwo.php'],
        ]);

        $phpCalls->expects(self::once())
            ->method('getRegexIterator')
            ->with(self::equalTo($srcDir), self::equalTo('/^.+\.php$/i'))
            ->willReturn($iterator);

        /** @noinspection PhpParamsInspection */
        $createMigrationsAction = new CreateMigrationsAction(
            $srcDir,
            $output,
            $appBasePath,
            $fileSystem,
            $phpCalls
        );

        $createMigrationsAction();
    }
}
