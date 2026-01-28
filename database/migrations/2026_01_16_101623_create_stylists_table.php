<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stylists', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();      // stylist-1 (tạm để map từ dữ liệu cũ)
            $table->string('name', 120);
            $table->string('role', 30)->default('Junior'); // Master/Senior/Junior
            $table->unsignedTinyInteger('exp')->default(1);
            $table->decimal('rating', 2, 1)->default(4.5); // 4.9
            $table->string('specialty', 255)->nullable();  // "Fade,Korean"
            $table->string('status', 20)->default('available'); // available/busy
            $table->string('avatar', 5)->nullable(); // M/H/D...
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stylists');
    }
};
