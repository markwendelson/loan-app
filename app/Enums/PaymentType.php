<?php

namespace App\Enums;

enum PaymentType: string
{
    case RELOAN_DEDUCTION = 'reloan_deducation';
    case REGULAR_PAYMENT = 'regular_payment';
}
