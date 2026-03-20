<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('ads', function (Blueprint $table) {
        $table->id();
        $table->string('slug')->unique(); // e.g., 'header_top', 'sidebar_right'
        $table->string('location_name');  // Pehchan ke liye: "Header Banner"
        $table->text('ad_code')->nullable(); // AdSense ka script ya HTML
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
