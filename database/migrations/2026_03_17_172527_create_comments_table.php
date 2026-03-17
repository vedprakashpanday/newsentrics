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
    Schema::create('comments', function (Blueprint $table) {
        $table->id();
        // foreignId us news se jodne ke liye (Agar news delete hui toh comment bhi delete ho jayega)
        $table->foreignId('news_id')->constrained()->onDelete('cascade'); 
        $table->string('name');
        $table->string('email');
        $table->text('comment');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
