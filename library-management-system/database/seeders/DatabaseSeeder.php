<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (['Admin', 'Librarian', 'Member'] as $role) {
            DB::table('roles')->updateOrInsert(
                ['role_name' => $role],
                ['role_name' => $role],
            );
        }

        DB::table('users')->updateOrInsert(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'role_id' => DB::table('roles')->where('role_name', 'Member')->value('id'),
                'status' => 'active',
            ],
        );
    }
}
