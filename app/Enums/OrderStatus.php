<?php

namespace App\Enums;

enum OrderStatus: int
{
    case Pending = 1;
    case Approve = 2;
    case Deny = 3;
}
