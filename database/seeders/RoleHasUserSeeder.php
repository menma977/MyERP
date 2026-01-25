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

        if (! $developer instanceof User) {
            return;
        }

        $developerRole = Role::where('name', 'developer')
            ->where('guard_name', 'web')
            ->first();

        if (! $developerRole instanceof Role) {
            return;
        }

        if ($developer->hasRole($developerRole)) {
            return;
        }

        $developer->assignRole($developerRole);
    }
}
