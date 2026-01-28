<?php

namespace Database\Factories\Customer;

use App\Models\Companies\Company;
use App\Models\Customer\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'code' => 'CST-'.$this->faker->unique()->numerify('######'),
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'is_default' => false,
        ];
    }
}
