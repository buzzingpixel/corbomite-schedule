<?php

declare(strict_types=1);

namespace corbomite\schedule\actions;

use corbomite\schedule\PhpCalls;
use LogicException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use function str_replace;

class CreateMigrationsAction
{
    /** @var string */
    private $srcDir;
    /** @var OutputInterface */
    private $output;
    /** @var string $appBasePath */
    private $appBasePath;
    /** @var Filesystem */
    private $filesystem;
    /** @var PhpCalls */
    private $phpCalls;

    public function __construct(
        string $srcDir,
        OutputInterface $output,
        string $appBasePath,
        Filesystem $filesystem,
        PhpCalls $phpCalls
    ) {
        $this->srcDir      = $srcDir;
        $this->output      = $output;
        $this->appBasePath = $appBasePath;
        $this->filesystem  = $filesystem;
        $this->phpCalls    = $phpCalls;
    }

    public function __invoke() : void
    {
        $phinxPhpFile = $this->appBasePath . '/phinx.php';

        if (! $this->filesystem->exists($phinxPhpFile)) {
            throw new LogicException('phinx.php must be present in your project');
        }

        $phinxConf = include $this->appBasePath . '/phinx.php';

        $dest = $phinxConf['paths']['migrations'] ?? null;

        if (! $dest) {
            throw new LogicException('Migrations path must be defined in phinx conf');
        }

        $dest = $this->phpCalls->realpath(
            str_replace('%%PHINX_CONFIG_DIR%%', $this->appBasePath, $dest)
        );

        if (! $dest) {
            throw new LogicException('Migrations path could not be resolved');
        }

        $iterator = $this->phpCalls->getRegexIterator($this->srcDir, '/^.+\.php$/i');

        foreach ($iterator as $files) {
            foreach ($files as $file) {
                $path = $this->srcDir . '/' . $file;

                $destPath = $dest . '/' . $file;

                if ($this->filesystem->exists($destPath)) {
                    $this->output->writeln(
                        '<fg=green>' . $file . ' already exists.</>'
                    );

                    continue;
                }

                $this->filesystem->copy($path, $destPath);

                $this->output->writeln(
                    '<fg=green>' . $file . ' created.</>'
                );
            }
        }
    }
}
