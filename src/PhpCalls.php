<?php

declare(strict_types=1);

namespace corbomite\schedule;

use DirectoryIterator;
use RegexIterator;
use function realpath;

class PhpCalls
{
    /**
     * @return mixed
     */
    public function include(string $path)
    {
        return include $path;
    }

    /**
     * @return string|bool
     */
    public function realpath(string $path)
    {
        return realpath($path);
    }

    public function getRegexIterator(
        string $dirIteratorPath,
        string $regex,
        ?int $mode = RegexIterator::GET_MATCH
    ) : iterable {
        return new RegexIterator(
            new DirectoryIterator($dirIteratorPath),
            $regex,
            $mode
        );
    }
}
