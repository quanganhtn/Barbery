<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Đảm bảo cột tồn tại + nullable
        Schema::table('bookings', function (Blueprint $table) {

            // Nếu cột đã tồn tại -> đổi thành nullable
            if (Schema::hasColumn('bookings', 'service_id')) {
                $table->unsignedBigInteger('service_id')->nullable()->change();
            } else {
                $table->unsignedBigInteger('service_id')->nullable()->after('customer_phone');
            }

            if (Schema::hasColumn('bookings', 'stylist_id')) {
                $table->unsignedBigInteger('stylist_id')->nullable()->change();
            } else {
                $table->unsignedBigInteger('stylist_id')->nullable()->after('service_id');
            }
        });

        // 2) Add foreign keys (chỉ add nếu chưa có)
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'service_id') || !Schema::hasColumn('bookings', 'stylist_id')) {
                return;
            }

            // FK service
            try {
                $table->foreign('service_id')
                    ->references('id')->on('services')
                    ->nullOnDelete();
            } catch (Throwable $e) {
            }

            // FK stylist
            try {
                $table->foreign('stylist_id')
                    ->references('id')->on('stylists')
                    ->nullOnDelete();
            } catch (Throwable $e) {
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // drop FK nếu có
            try {
                $table->dropForeign(['service_id']);
            } catch (Throwable $e) {
            }
            try {
                $table->dropForeign(['stylist_id']);
            } catch (Throwable $e) {
            }
        });

        // Nếu chị muốn rollback mà giữ cột thì thôi.
        // Nếu muốn rollback xóa cột thì mở comment:
        /*
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'stylist_id')) $table->dropColumn('stylist_id');
            if (Schema::hasColumn('bookings', 'service_id')) $table->dropColumn('service_id');
        });
        */
    }
};
