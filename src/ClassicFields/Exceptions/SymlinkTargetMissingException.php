<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\ClassicFields\Exceptions;

use pointybeard\Helpers\Exceptions\ReadableTrace;

class SymlinkTargetMissingException extends ReadableTrace\ReadableTraceException
{
    public function __construct(string $target, int $code = 0, \Exception $previous = null)
    {
        parent::__construct("Symbolic link target {$target} does not exist.", $code, $previous);
    }
}
