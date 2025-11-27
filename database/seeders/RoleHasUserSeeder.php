<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleHasUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $developer = User::where('username', 'dev')->first();
        if (!$developer) {
            return;
        }

        $developerRole = Role::where('name', 'developer')->first();
        if (!$developerRole) {
            return;
        }

        if ($developer->hasRole('developer')) {
            return;
        }

        $developer->assignRole('developer');
    }
}
