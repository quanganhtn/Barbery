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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stylist_id')
                ->constrained('stylists')
                ->cascadeOnDelete();

            $table->date('work_date');

            $table->string('status')->default('present');
            $table->decimal('work_value', 3, 1)->default(1);

            $table->text('note')->nullable();

            $table->timestamps();

            $table->unique(['stylist_id', 'work_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
