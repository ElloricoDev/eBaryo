<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('book_reviews', function (Blueprint $table) {
            $table->unique(['user_id', 'book_id']);
        });
    }

    public function down()
    {
        Schema::table('book_reviews', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'book_id']);
        });
    }
};
