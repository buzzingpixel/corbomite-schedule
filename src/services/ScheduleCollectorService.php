<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace corbomite\schedule\services;

use LogicException;
use DirectoryIterator;

class ScheduleCollectorService
{
    public function __invoke()
    {
        if (! defined('APP_BASE_PATH')) {
            throw new LogicException('APP_BASE_PATH must be defined');
        }

        $config = $this->collectFromPath(APP_BASE_PATH);

        $vendorIterator = new DirectoryIterator(
            APP_BASE_PATH . DIRECTORY_SEPARATOR . 'vendor'
        );

        foreach ($vendorIterator as $fileInfo) {
            if ($fileInfo->isDot() || ! $fileInfo->isDir()) {
                continue;
            }

            $providerIterator = new DirectoryIterator($fileInfo->getPathname());

            foreach ($providerIterator as $providerFileInfo) {
                if ($providerFileInfo->isDot() ||
                    ! $providerFileInfo->isDir()
                ) {
                    continue;
                }

                $config = array_merge($config, $this->collectFromPath(
                    $providerFileInfo->getPathname()
                ));
            }
        }

        return $config;
    }

    private function collectFromPath(string $path): array
    {
        $composerJsonPath = $path . DIRECTORY_SEPARATOR . 'composer.json';

        if (! file_exists($composerJsonPath)) {
            return [];
        }

        $json = json_decode(file_get_contents($composerJsonPath), true);

        $filePath = isset($json['extra']['scheduleConfigFilePath']) ?
            $json['extra']['scheduleConfigFilePath'] :
            'asdf';

        $configFilePath = $path . DIRECTORY_SEPARATOR . $filePath;

        if (! file_exists($configFilePath)) {
            return [];
        }

        $configInclude = include $configFilePath;

        return \is_array($configInclude) ? $configInclude : [];
    }
}
