<?php

namespace App\Enums;

enum ExpenseCategoryEnum: string
{
    case OPERATIONAL = 'operational';
    case SALARY = 'salary';
    case REIMBURSEMENT = 'reimbursement';
}
