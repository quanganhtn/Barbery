<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm email + mã tra cứu vào bảng bookings
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Email để gửi thông tin lịch hẹn + mã tra cứu
            $table->string('customer_email', 150)->nullable()->after('customer_phone');

            // Mã tra cứu 6 số gửi qua email
            $table->string('lookup_code', 6)->nullable()->after('booking_code');

            // Thời điểm đã gửi mã tra cứu
            $table->timestamp('lookup_code_sent_at')->nullable()->after('lookup_code');
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'customer_email',
                'lookup_code',
                'lookup_code_sent_at',
            ]);
        });
    }
};
