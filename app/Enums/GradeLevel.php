<?php

namespace App\Enums;

enum GradeLevel: string
{
    // Kindergarten
    case KG1 = 'kg1';
    case KG2 = 'kg2';
    case KG3 = 'kg3';

    // Primary
    case PRIMARY_1 = 'primary_1';
    case PRIMARY_2 = 'primary_2';
    case PRIMARY_3 = 'primary_3';
    case PRIMARY_4 = 'primary_4';
    case PRIMARY_5 = 'primary_5';
    case PRIMARY_6 = 'primary_6';

    // Junior Secondary School
    case JSS_1 = 'jss_1';
    case JSS_2 = 'jss_2';
    case JSS_3 = 'jss_3';

    // Senior Secondary School
    case SSS_1 = 'sss_1';
    case SSS_2 = 'sss_2';
    case SSS_3 = 'sss_3';

    public function label(): string
    {
        return match($this) {
            self::KG1 => 'KG 1',
            self::KG2 => 'KG 2',
            self::KG3 => 'KG 3',
            self::PRIMARY_1 => 'Primary 1',
            self::PRIMARY_2 => 'Primary 2',
            self::PRIMARY_3 => 'Primary 3',
            self::PRIMARY_4 => 'Primary 4',
            self::PRIMARY_5 => 'Primary 5',
            self::PRIMARY_6 => 'Primary 6',
            self::JSS_1 => 'JSS 1',
            self::JSS_2 => 'JSS 2',
            self::JSS_3 => 'JSS 3',
            self::SSS_1 => 'SS 1',
            self::SSS_2 => 'SS 2',
            self::SSS_3 => 'SS 3',
        };
    }

    public function section(): string
    {
        return match(true) {
            in_array($this, [self::KG1, self::KG2, self::KG3]) => 'KG',
            in_array($this, [self::PRIMARY_1, self::PRIMARY_2, self::PRIMARY_3, self::PRIMARY_4, self::PRIMARY_5, self::PRIMARY_6]) => 'Primary',
            in_array($this, [self::JSS_1, self::JSS_2, self::JSS_3, self::SSS_1, self::SSS_2, self::SSS_3]) => 'Secondary',
        };
    }
}
