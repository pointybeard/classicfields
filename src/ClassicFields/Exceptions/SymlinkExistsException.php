<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\ClassicFields\Exceptions;

use pointybeard\Helpers\Exceptions\ReadableTrace;

class SymlinkExistsException extends ReadableTrace\ReadableTraceException
{
    public function __construct(string $name, int $code = 0, \Exception $previous = null)
    {
        parent::__construct("Symbolic link {$name} already exists.", $code, $previous);
    }
}
