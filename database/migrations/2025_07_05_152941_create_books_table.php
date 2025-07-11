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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->string('isbn')->nullable()->unique();
            $table->year('published_year')->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('ebook_file');
            $table->unsignedBigInteger('category_id');
            $table->string('publisher')->nullable();
            $table->string('language')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
