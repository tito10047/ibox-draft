<?php

namespace App\Enum;

enum ComplaintType: string
{
    case ABSURD = 'Absurd';
    case CRITICAL = 'Critical';
    case URGENT = 'Urgent';
    case PARANORMAL = 'Paranormal';
    case PERSONAL = 'Personal';
}
