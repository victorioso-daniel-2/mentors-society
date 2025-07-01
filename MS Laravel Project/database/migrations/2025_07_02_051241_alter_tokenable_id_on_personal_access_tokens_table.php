<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterTokenableIdOnPersonalAccessTokensTable extends Migration
{
    public function up()
    {
        // For MySQL
        DB::statement('ALTER TABLE personal_access_tokens MODIFY tokenable_id VARCHAR(255)');
    }

    public function down()
    {
        // Revert to bigint if needed
        DB::statement('ALTER TABLE personal_access_tokens MODIFY tokenable_id BIGINT UNSIGNED');
    }
}