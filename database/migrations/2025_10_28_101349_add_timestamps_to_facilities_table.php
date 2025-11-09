<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->timestamps(); // Tự động thêm created_at và updated_at (kiểu TIMESTAMP, nullable)
            $table->decimal('default_price', 10, 2)->nullable();
            $table->decimal('special_price', 10, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropTimestamps(); // Để rollback
        });
    }
};
