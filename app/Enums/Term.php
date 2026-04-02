<?php

namespace App\Enums;

enum Term: string
{
    case FIRST = 'first';
    case SECOND = 'second';
    case THIRD = 'third';

    public function label(): string
    {
        return ucfirst($this->value) . ' Term';
    }
}
