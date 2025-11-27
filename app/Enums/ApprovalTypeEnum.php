<?php

namespace App\Enums;

enum ApprovalTypeEnum: int
{
	case PARALLEL = 0;
	case SEQUENTIAL = 1;
}
