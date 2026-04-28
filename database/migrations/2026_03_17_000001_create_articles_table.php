<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('issue_id')->constrained('issues')->cascadeOnDelete();

            $table->string('title');
            $table->string('authors')->nullable();
            $table->text('abstract')->nullable();
            $table->string('pages')->nullable(); // например: 12–25
            $table->string('doi')->nullable();

            $table->date('published_at')->nullable()->index();
            $table->string('pdf_url')->nullable();

            $table->boolean('is_published')->default(true)->index();
            $table->integer('sort_order')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};

