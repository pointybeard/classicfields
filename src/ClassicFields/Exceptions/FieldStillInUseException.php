<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\ClassicFields\Exceptions;

use pointybeard\Helpers\Exceptions\ReadableTrace;

class FieldStillInUseException extends ReadableTrace\ReadableTraceException
{
    public function __construct(string $name, array $sections, int $code = 0, \Exception $previous = null)
    {
        parent::__construct("Unable to disable or uninstall field '{$name}'. It is currently in use by sections: ".implode(', ', $sections), $code, $previous);
    }
}
