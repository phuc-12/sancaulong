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
        Schema::create('court_prices', function (Blueprint $table) {
        $table->id('court_price_id');
        $table->unsignedBigInteger('facility_id');
        $table->decimal('default_price', 14, 2)->nullable();
        $table->decimal('special_price', 14, 2)->nullable();
        $table->date('effective_date');
        $table->timestamps();
        
        $table->foreign('facility_id')->references('facility_id')->on('facilities')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('court_prices');
    }
};
