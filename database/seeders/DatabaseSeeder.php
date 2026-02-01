<?php

namespace Database\Seeders;

use App\Models\Companies\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public static function createCompany(): Company
    {
        $company = Company::where('code', 'DEFAULT')->first();
        if (! $company) {
            $company = new Company;
            $company->name = 'DEFAULT COMPANY';
            $company->code = 'DEFAULT';
            $company->save();
        }

        return $company;
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        self::createCompany();
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            RoleHasUserSeeder::class,
        ]);
    }
}
