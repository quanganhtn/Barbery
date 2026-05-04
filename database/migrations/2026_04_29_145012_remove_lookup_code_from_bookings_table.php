<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'lookup_code',
                'lookup_code_sent_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('lookup_code', 6)->nullable()->after('booking_code');
            $table->timestamp('lookup_code_sent_at')->nullable()->after('lookup_code');
        });
    }
};
