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
       Schema::create('news', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('slug')->unique(); // SEO ke liye
    $table->text('content');
    $table->string('image')->nullable();
    $table->string('country'); // India, US, etc.
    $table->text('keywords'); // Google Trends wale keywords
    $table->integer('view_count')->default(0); // Visitors track karne ke liye
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
