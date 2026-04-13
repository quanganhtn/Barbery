<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            UPDATE services
            SET duration_min = CASE
                WHEN duration_min IS NULL OR duration_min = 0 THEN COALESCE(duration, 30)
                ELSE duration_min
            END
        ");
    }

    public function down(): void
    {
        //
    }
};
