<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('issues', function (Blueprint $table) {
            // Добавляем колонку cover_image, если её нет
            if (!Schema::hasColumn('issues', 'cover_image')) {
                $table->string('cover_image', 500)->nullable()->comment('Ссылка на обложку выпуска');
            }
        });
    }

    public function down()
    {
        Schema::table('issues', function (Blueprint $table) {
            if (Schema::hasColumn('issues', 'cover_image')) {
                $table->dropColumn('cover_image');
            }
        });
    }
};