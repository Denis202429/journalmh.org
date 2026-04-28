<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            // Добавляем поле для хранения пути к загруженному PDF
            $table->string('pdf_file_path', 500)->nullable()->after('pdf_url')->comment('Путь к загруженному PDF файлу');
            $table->string('pdf_original_name', 255)->nullable()->after('pdf_file_path')->comment('Оригинальное имя PDF файла');
            $table->bigInteger('pdf_file_size')->nullable()->after('pdf_original_name')->comment('Размер PDF файла в байтах');
        });
    }

    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['pdf_file_path', 'pdf_original_name', 'pdf_file_size']);
        });
    }
};