<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('issues', function (Blueprint $table) {
            // Убираем after('cover_image') - просто добавляем колонки
            if (!Schema::hasColumn('issues', 'cover_image_path')) {
                $table->string('cover_image_path', 500)->nullable()->comment('Путь к загруженной обложке');
            }
            if (!Schema::hasColumn('issues', 'cover_original_name')) {
                $table->string('cover_original_name', 255)->nullable()->comment('Оригинальное имя файла обложки');
            }
        });
    }

    public function down()
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->dropColumn(['cover_image_path', 'cover_original_name']);
        });
    }
};