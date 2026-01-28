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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 10)->unique();

            $table->string('customer_name', 120);
            $table->string('customer_phone', 20);

            // tạm thời dùng string id theo frontend (cut-basic, stylist-1...) để đi nhanh
            // sau này làm chức năng #4 sẽ chuyển sang foreign key service_id/stylist_id
            $table->string('service_id', 50);
            $table->string('stylist_id', 50);

            $table->date('booking_date');
            $table->string('booking_time', 10);

            $table->integer('total_price');
            $table->string('status', 20)->default('pending'); // pending/confirmed/...
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['stylist_id', 'booking_date', 'booking_time']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
