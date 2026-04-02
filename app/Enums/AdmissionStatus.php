<?php

namespace App\Enums;

enum AdmissionStatus: string
{
    case PENDING = 'pending';
    case UNDER_REVIEW = 'under_review';
    case SCREENING = 'screening';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case ACCEPTED = 'accepted';
    case ENROLLED = 'enrolled';

    public function label(): string
    {
        return str_replace('_', ' ', ucfirst($this->value));
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::UNDER_REVIEW => 'blue',
            self::SCREENING => 'indigo',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
            self::ACCEPTED => 'emerald',
            self::ENROLLED => 'teal',
        };
    }
}
