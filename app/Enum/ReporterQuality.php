<?php

namespace App\Enum;

enum ReporterQuality: int
{
    case policyholder = 1;
    case insured = 2;
    case beneficiary = 3;
    case injured = 4;
    case third_part = 5;
}
