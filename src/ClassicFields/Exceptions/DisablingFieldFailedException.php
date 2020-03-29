<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\ClassicFields\Exceptions;

use pointybeard\Helpers\Exceptions\ReadableTrace;

class DisablingFieldFailedException extends ReadableTrace\ReadableTraceException
{
    public function __construct(string $name, string $message, int $code = 0, \Exception $previous = null)
    {
        parent::__construct("Faild to disable field '{$name}'. Returned: {$message}", $code, $previous);
    }
}
