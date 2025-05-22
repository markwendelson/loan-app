<?php

namespace App\Enums;

enum LoanStatus: int
{
    case PENDING = 0;
    case APPROVE = 1;
    case DECLINE = 2;
    case WITHDRAWN = 3;
    case REQUEST_APPROVE = 4;
}
