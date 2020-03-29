<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\ClassicFields\Exceptions;

use pointybeard\Helpers\Exceptions\ReadableTrace;

class SymlinkCreationFailedException extends ReadableTrace\ReadableTraceException
{
    public function __construct(string $name, string $message, int $code = 0, \Exception $previous = null)
    {
        parent::__construct("Symbolic link '{$name}' could not be created. Returned: {$message}", $code, $previous);
    }
}
