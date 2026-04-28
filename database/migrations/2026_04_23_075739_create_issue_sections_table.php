<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Таблица разделов выпуска
        Schema::create('issue_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_id')->constrained()->onDelete('cascade');
            $table->string('title_ru', 255)->nullable();
            $table->string('title_en', 255)->nullable();
            $table->string('title_cv', 255)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['issue_id', 'sort_order']);
        });
        
        // Добавляем поле section_id в таблицу articles
        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('section_id')->nullable()->after('issue_id')
                  ->constrained('issue_sections')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropColumn('section_id');
        });
        
        Schema::dropIfExists('issue_sections');
    }
};