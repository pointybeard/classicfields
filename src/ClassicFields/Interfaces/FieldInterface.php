<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\ClassicFields\Interfaces;

interface FieldInterface
{
    public function getCreateFieldSQL(): string;

    public function getFropFieldSQL(): string;
}
