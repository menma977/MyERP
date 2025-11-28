<?php

namespace Database\Seeders;

use App\Models\Role as RoleModel;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $collector = collect();

        $collector->push([
            'name' => 'developer',
            'guard_name' => 'sanctum',
        ]);

        $collector->push([
            'name' => 'admin',
            'guard_name' => 'sanctum',
        ]);

        $collector->push([
            'name' => 'user',
            'guard_name' => 'sanctum',
        ]);

        foreach ($collector as $item) {
            RoleModel::updateOrCreate([
                'name' => $item['name'],
                'guard_name' => $item['guard_name'],
            ]);
        }
    }
}
