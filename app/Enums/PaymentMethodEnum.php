<?php

namespace App\Enums;

enum PaymentMethodEnum: string
{
	case CASH = 'CASH';
	case BANK_TRANSFER = 'BANK_TRANSFER';
}
