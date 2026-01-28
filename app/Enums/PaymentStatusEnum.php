<?php

namespace App\Enums;

enum PaymentStatusEnum: string
{
    case PAID = 'paid';
    case PARTIAL = 'partial';
}
