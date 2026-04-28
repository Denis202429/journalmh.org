<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('issues', function (Blueprint $table) {
            // Проверяем и добавляем колонку publisher
            if (!Schema::hasColumn('issues', 'publisher')) {
                $table->string('publisher', 255)->nullable()->comment('Издатель');
            }
            
            // Проверяем и добавляем колонку description
            if (!Schema::hasColumn('issues', 'description')) {
                $table->text('description')->nullable()->comment('Описание выпуска (рус)');
            }
            
            // Проверяем и добавляем колонку description_en
            if (!Schema::hasColumn('issues', 'description_en')) {
                $table->text('description_en')->nullable()->comment('Описание выпуска (англ)');
            }
            
            // Проверяем и добавляем колонку issue_files
            if (!Schema::hasColumn('issues', 'issue_files')) {
                $table->json('issue_files')->nullable()->comment('Файлы выпуска (обложка и др.)');
            }
            
            // Проверяем и добавляем колонку alt_number
            if (!Schema::hasColumn('issues', 'alt_number')) {
                $table->string('alt_number', 50)->nullable()->comment('Сквозной номер выпуска');
            }
            
            // Проверяем и добавляем колонку part
            if (!Schema::hasColumn('issues', 'part')) {
                $table->unsignedSmallInteger('part')->nullable()->comment('Часть выпуска');
            }
        });
    }

    public function down()
    {
        Schema::table('issues', function (Blueprint $table) {
            $columns = ['publisher', 'description', 'description_en', 'issue_files', 'alt_number', 'part'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('issues', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};