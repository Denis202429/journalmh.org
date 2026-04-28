<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Проверяем, существует ли уже таблица
        if (!Schema::hasTable('issue_sections')) {
            Schema::create('issue_sections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('issue_id')->constrained()->onDelete('cascade');
                $table->string('title_ru', 255)->nullable()->comment('Название раздела (рус)');
                $table->string('title_en', 255)->nullable()->comment('Название раздела (англ)');
                $table->string('title_cv', 255)->nullable()->comment('Название раздела (чуваш)');
                $table->unsignedInteger('sort_order')->default(0)->comment('Порядок сортировки');
                $table->timestamps();
                
                $table->index(['issue_id', 'sort_order']);
            });
        }
        
        // Проверяем, существует ли поле section_id в таблице articles
        if (!Schema::hasColumn('articles', 'section_id')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->foreignId('section_id')->nullable()->after('issue_id')
                      ->constrained('issue_sections')
                      ->onDelete('set null');
            });
        }
    }

    public function down()
    {
        // Проверяем существование поля section_id перед удалением
        if (Schema::hasColumn('articles', 'section_id')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->dropForeign(['section_id']);
                $table->dropColumn('section_id');
            });
        }
        
        Schema::dropIfExists('issue_sections');
    }
};