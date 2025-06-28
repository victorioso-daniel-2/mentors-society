<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Check if test user exists
        $testUser = DB::table('USER')->where('email', 'test@example.com')->first();
        
        if (!$testUser) {
            User::create([
                'first_name' => 'Test',
                'last_name' => 'User',
                'middle_initial' => 'A',
                'email' => 'test@example.com',
            ]);
        }

        $this->call([
            CsvStudentImportSeeder::class,
        ]);
    }
}
