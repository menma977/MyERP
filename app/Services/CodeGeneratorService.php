<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class CodeGeneratorService
{
    private string $code;

    private int $number;

    /**
     * Create a new CodeGeneratorService instance with the specified code.
     *
     * @param string $code The code string to include in the generated code
     * @return self The CodeGeneratorService instance
     */
    public static function code(string $code): self
    {
        $self = new self;
        $self->code = $code;

        return $self;
    }

    /**
     * Set the number for the code generation.
     *
     * @param int $number The number to include in the generated code
     * @return $this The CodeGeneratorService instance for method chaining
     */
    public function number(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Generate a code with format: Y/STRING/Roman/number
     * Example: 2025/PR/XII/0001
     *
     * The generated code follows the pattern:
     * - Current year (4 digits)
     * - Forward slash separator
     * - The specified code string
     * - Forward slash separator
     * - Current month in Roman numerals
     * - Forward slash separator
     * - The specified number padded to 4 digits with leading zeros
     *
     * @return string The generated code in the format Y/CODE/ROMAN_MONTH/NUMBER
     */
    public function generate(): string
    {
        $carbonDate = Carbon::now();
        $year = $carbonDate->year;
        $month = $carbonDate->month;

        $romanMonth = $this->toRoman($month);

        return sprintf('%s/%s/%s/%04d', $year, $this->code, $romanMonth, $this->number);
    }

    /**
     * Convert a number to the Roman numeral representation.
     *
     * This method converts an integer to its Roman numeral equivalent using
     * a standard conversion algorithm with predefined numeral-value pairs.
     * It supports numbers from 1 to 3999.
     *
     * @param int $number The number to convert (must be between 1 and 3999)
     * @return string The Roman numeral representation of the number
     */
    private function toRoman(int $number): string
    {
        $romanNumerals = [
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1,
        ];

        $result = '';

        foreach ($romanNumerals as $numeral => $value) {
            while ($number >= $value) {
                $result .= $numeral;
                $number -= $value;
            }
        }

        return $result;
    }
}
