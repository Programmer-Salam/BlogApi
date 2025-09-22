<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('title');
            $table->index('author_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
