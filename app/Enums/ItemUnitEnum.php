<?php

namespace App\Enums;

enum ItemUnitEnum: string
{
	case PCS = 'PCS';
	case KG = 'KG';
	case GRAM = 'GRAM';
	case LITER = 'LITER';
	case MILLILITER = 'MILLILITER';
	case METER = 'METER';
	case CENTIMETER = 'CENTIMETER';
	case SQUARE_METER = 'SQUARE_METER';
	case BOX = 'BOX';
	case ROLL = 'ROLL';
	case TABLE_SPOON = 'TABLE_SPOON';
	case TEA_SPOON = 'TEA_SPOON';
}
