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
        Schema::create('salary_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stylist_id')
                ->constrained('stylists')
                ->cascadeOnDelete();

            $table->integer('month');
            $table->integer('year');

            $table->decimal('standard_work_days', 4, 1)->default(26);
            $table->decimal('actual_work_days', 4, 1)->default(0);

            $table->integer('base_salary')->default(3000000);
            $table->integer('earned_base_salary')->default(0);

            $table->integer('total_bookings')->default(0);
            $table->integer('total_commission')->default(0);
            $table->integer('total_salary')->default(0);

            $table->string('payment_status')->default('unpaid');
            $table->timestamp('paid_at')->nullable();

            $table->text('note')->nullable();

            $table->timestamps();

            $table->unique(['stylist_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_reports');
    }
};
