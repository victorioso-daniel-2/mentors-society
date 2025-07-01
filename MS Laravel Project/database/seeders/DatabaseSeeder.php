<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            CsvStudentImportSeeder::class,
        ]);

        // Seed transaction types
        DB::table('transaction_type')->insertOrIgnore([
            ['type_name' => 'FRA Payment', 'direction' => 'income'],
            ['type_name' => 'Event Fee', 'direction' => 'income'],
            ['type_name' => 'Purchase', 'direction' => 'outcome'],
            ['type_name' => 'Refund', 'direction' => 'outcome'],
            ['type_name' => 'Penalty', 'direction' => 'income'],
        ]);
    }
}