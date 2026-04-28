<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('issues', function (Blueprint $table) {
            // Добавляем колонку doi, если её нет
            if (!Schema::hasColumn('issues', 'doi')) {
                $table->string('doi', 100)->nullable()->comment('DOI журнала');
            }
            
            // Добавляем issue_doi (убираем after('doi'))
            if (!Schema::hasColumn('issues', 'issue_doi')) {
                $table->string('issue_doi', 100)->nullable()->comment('DOI выпуска');
            }
            
            if (!Schema::hasColumn('issues', 'edn')) {
                $table->string('edn', 6)->nullable()->comment('EDN (eLIBRARY Document Number)');
            }
            
            if (!Schema::hasColumn('issues', 'pdf_file_path')) {
                $table->string('pdf_file_path', 500)->nullable()->comment('Путь к загруженному PDF файлу выпуска');
            }
            
            if (!Schema::hasColumn('issues', 'pdf_original_name')) {
                $table->string('pdf_original_name', 255)->nullable()->comment('Оригинальное имя PDF файла выпуска');
            }
            
            if (!Schema::hasColumn('issues', 'pdf_file_size')) {
                $table->bigInteger('pdf_file_size')->nullable()->comment('Размер PDF файла выпуска в байтах');
            }
            
            if (!Schema::hasColumn('issues', 'description')) {
                $table->text('description')->nullable()->comment('Описание выпуска (рус)');
            }
            
            if (!Schema::hasColumn('issues', 'description_en')) {
                $table->text('description_en')->nullable()->comment('Описание выпуска (англ)');
            }
        });
    }

    public function down()
    {
        Schema::table('issues', function (Blueprint $table) {
            $columns = ['doi', 'issue_doi', 'edn', 'pdf_file_path', 'pdf_original_name', 'pdf_file_size', 'description', 'description_en'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('issues', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};