<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class FakeIdTranslationService
{
    private Model $model;

    private ?string $key;

    private function __construct(Model $model)
    {
        $this->model = $model;
    }

    public static function model(Model $model): self
    {
        return new self($model);
    }

    public function key(?string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function translateUlid(): int
    {
        if (! $this->key) {
            return 0;
        }

        $model = $this->model->newQuery()->select('id')->where('ulid', $this->key)->first();

        return (int) $model?->getKey();
    }
}
