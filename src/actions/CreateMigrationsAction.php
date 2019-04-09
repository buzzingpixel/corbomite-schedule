<?php

declare(strict_types=1);

namespace corbomite\schedule\actions;

use DirectoryIterator;
use LogicException;
use RegexIterator;
use Symfony\Component\Console\Output\OutputInterface;
use function copy;
use function defined;
use function file_exists;
use function realpath;
use function str_replace;

class CreateMigrationsAction
{
    /** @var string */
    private $srcDir;
    /** @var OutputInterface */
    private $output;

    public function __construct(
        string $srcDir,
        OutputInterface $output
    ) {
        $this->srcDir = $srcDir;
        $this->output = $output;
    }

    public function __invoke() : void
    {
        if (! defined('APP_BASE_PATH')) {
            throw new LogicException('APP_BASE_PATH must be defined');
        }

        if (! file_exists(APP_BASE_PATH . '/phinx.php')) {
            throw new LogicException('phinx.php must be present in your project');
        }

        $phinxConf = include APP_BASE_PATH . '/phinx.php';
        $dest      = $phinxConf['paths']['migrations'] ?? null;

        if (! $dest) {
            throw new LogicException('Migrations path must be defined in phinx conf');
        }

        $dest = realpath(
            str_replace('%%PHINX_CONFIG_DIR%%', APP_BASE_PATH, $dest)
        );

        if (! $dest) {
            throw new LogicException('Migrations path could not be resolved');
        }

        $iterator = new RegexIterator(
            new DirectoryIterator($this->srcDir),
            '/^.+\.php$/i',
            RegexIterator::GET_MATCH
        );

        foreach ($iterator as $files) {
            foreach ($files as $file) {
                $path     = $this->srcDir . '/' . $file;
                $destPath = $dest . '/' . $file;

                if (file_exists($destPath)) {
                    $this->output->writeln(
                        '<fg=green>' . $file . ' already exists.</>'
                    );

                    continue;
                }

                copy($path, $destPath);

                $this->output->writeln(
                    '<fg=green>' . $file . ' created.</>'
                );
            }
        }
    }
}
