<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'duration_min')) {
                $table->unsignedInteger('duration_min')->default(30)->after('duration');
            }

            if (!Schema::hasColumn('services', 'icon')) {
                $table->string('icon')->nullable()->after('duration_min');
            }

            if (!Schema::hasColumn('services', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('icon');
            }

            if (!Schema::hasColumn('services', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('sort_order');
            }

            if (!Schema::hasColumn('services', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_active');
            }

            if (!Schema::hasColumn('services', 'is_hot')) {
                $table->boolean('is_hot')->default(false)->after('is_featured');
            }

            if (!Schema::hasColumn('services', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('is_hot')->constrained('service_categories')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'category_id')) {
                $table->dropConstrainedForeignId('category_id');
            }

            if (Schema::hasColumn('services', 'is_hot')) {
                $table->dropColumn('is_hot');
            }

            if (Schema::hasColumn('services', 'is_featured')) {
                $table->dropColumn('is_featured');
            }

            if (Schema::hasColumn('services', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if (Schema::hasColumn('services', 'sort_order')) {
                $table->dropColumn('sort_order');
            }

            if (Schema::hasColumn('services', 'icon')) {
                $table->dropColumn('icon');
            }

            if (Schema::hasColumn('services', 'duration_min')) {
                $table->dropColumn('duration_min');
            }
        });
    }
};
