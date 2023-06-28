<?php

namespace PinaCMS\Types;

use Pina\Types\StringType;

class SlugType extends StringType//UUIDType
{
    public function getSize(): int
    {
        return 40;//TODO: сократить до размера UUID 36
    }
}