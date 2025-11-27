<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidationWithoutTrashed implements ValidationRule
{
    protected string $model;

    protected ?string $column;

    protected ?string $ignoreId;

    public function __construct(string $model, ?string $column = null, ?string $ignoreId = null)
    {
        $this->model = $model;
        $this->column = $column;
        $this->ignoreId = $ignoreId;
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = app($this->model)->where($this->column ?? $attribute, $value)->whereNull('deleted_at');

        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        if ($query->exists()) {
            $fail(trans('validation.unique', ['attribute' => $attribute]));
        }
    }
}
