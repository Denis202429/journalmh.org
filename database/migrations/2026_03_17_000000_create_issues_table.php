<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('volume')->nullable();
            $table->unsignedInteger('number')->nullable();
            $table->unsignedSmallInteger('year')->index();
            $table->string('month')->nullable();

            $table->string('title')->nullable();
            $table->date('published_at')->nullable()->index();
            $table->string('pdf_url')->nullable();

            $table->boolean('is_published')->default(true)->index();
            $table->integer('sort_order')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};

