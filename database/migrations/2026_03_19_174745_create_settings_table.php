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
    Schema::create('settings', function (Blueprint $table) {
        $table->id();
        $table->string('site_name')->default('Newsentric');
        $table->string('logo')->nullable(); // Logo image ke liye
        $table->text('footer_about')->nullable(); // Footer ke text ke liye
        $table->string('facebook')->nullable();
        $table->string('twitter')->nullable();
        $table->string('instagram')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
