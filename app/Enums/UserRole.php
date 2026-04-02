<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case SCHOOL_ADMIN = 'school_admin';
    case PRINCIPAL = 'principal';
    case VICE_PRINCIPAL = 'vice_principal';
    case TEACHER = 'teacher';
    case ACCOUNTANT = 'accountant';
    case LIBRARIAN = 'librarian';
    case PARENT = 'parent';
    case STUDENT = 'student';
    case STAFF = 'staff';
    case DRIVER = 'driver';
    case NURSE = 'nurse';

    public function label(): string
    {
        return match($this) {
            self::SUPER_ADMIN => 'Super Administrator',
            self::SCHOOL_ADMIN => 'School Administrator',
            self::PRINCIPAL => 'Principal',
            self::VICE_PRINCIPAL => 'Vice Principal',
            self::TEACHER => 'Teacher',
            self::ACCOUNTANT => 'Accountant',
            self::LIBRARIAN => 'Librarian',
            self::PARENT => 'Parent/Guardian',
            self::STUDENT => 'Student',
            self::STAFF => 'Staff',
            self::DRIVER => 'Driver',
            self::NURSE => 'School Nurse',
        };
    }
}
