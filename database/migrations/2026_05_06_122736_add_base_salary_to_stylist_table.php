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
        Schema::table('stylists', function (Blueprint $table) {
            $table->integer('base_salary')->default(3000000)->after('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stylist', function (Blueprint $table) {
            $table->dropColumn('base_salary');
        });
    }
};
