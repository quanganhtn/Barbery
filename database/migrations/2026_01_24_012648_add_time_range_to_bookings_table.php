<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->dateTime('start_at')->nullable()->index();
            $table->dateTime('end_at')->nullable()->index();
            $table->unsignedInteger('total_duration_min')->default(30);
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->dropColumn(['start_at', 'end_at', 'total_duration_min']);
        });
    }
};
