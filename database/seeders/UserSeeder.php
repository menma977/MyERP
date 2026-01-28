<?php

namespace Database\Seeders;

use App\Models\Companies\Company;
use App\Models\Companies\CompanyHasUser;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $collector = collect();

        $collector->push([
            'name' => 'developer',
            'username' => 'dev',
            'email' => 'dev@mail.com',
            'password' => bcrypt('1'),
        ]);

        $collector->push([
            'name' => 'owner',
            'username' => 'owner',
            'email' => 'owner@mail.com',
            'password' => bcrypt('1'),
        ]);

        $collector->push([
            'name' => 'user',
            'username' => 'user',
            'email' => 'user@mail.com',
            'password' => bcrypt('1'),
        ]);

        $company = new Company;
        $company->name = 'DEFAULT COMPANY';
        $company->code = 'DEFAULT';
        $company->save();

        foreach ($collector as $item) {
            $user = User::where('username', $item['username'])->first();
            if ($user) {
                continue;
            }

            $user = new User;
            $user->name = $item['name'];
            $user->username = $item['username'];
            $user->email = $item['email'];
            $user->password = $item['password'];
            $user->save();

            $companyHasUser = new CompanyHasUser;
            $companyHasUser->company_id = $company->id;
            $companyHasUser->user_id = $user->id;
            $companyHasUser->save();
        }
    }
}
