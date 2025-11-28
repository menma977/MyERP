<?php

namespace Database\Seeders;

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
            'name' => 'admin',
            'username' => 'admin',
            'email' => 'admin@mail.com',
            'password' => bcrypt('1'),
        ]);

        $collector->push([
            'name' => 'user',
            'username' => 'user',
            'email' => 'user@mail.com',
            'password' => bcrypt('1'),
        ]);

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
        }
    }
}
