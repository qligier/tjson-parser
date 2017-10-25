<?php

namespace Kentin\TJSON\Types;

interface ScalarType
{
    public function transform(string $bytes);
}
