<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('article_authors', function (Blueprint $table) {
            // Увеличиваем длину ORCID до 19 символов (стандарт: 0000-0000-0000-0000)
            $table->string('orcid', 19)->nullable()->change();
            
            // Увеличиваем длину SPIN (формат: 1234-5678 = 9 символов)
            $table->string('spin', 9)->nullable()->change();
            
            // Увеличиваем длину email
            $table->string('email', 255)->nullable()->change();
            
            // Увеличиваем длину других строковых полей при необходимости
            $table->string('surname_ru', 255)->nullable()->change();
            $table->string('surname_en', 255)->nullable()->change();
            $table->string('surname_cv', 255)->nullable()->change();
            $table->string('name_ru', 255)->nullable()->change();
            $table->string('name_en', 255)->nullable()->change();
            $table->string('name_cv', 255)->nullable()->change();
            $table->string('patronymic_ru', 255)->nullable()->change();
            $table->string('patronymic_en', 255)->nullable()->change();
            $table->string('patronymic_cv', 255)->nullable()->change();
            $table->string('position_ru', 255)->nullable()->change();
            $table->string('position_en', 255)->nullable()->change();
            $table->string('position_cv', 255)->nullable()->change();
            $table->string('degree', 255)->nullable()->change();
            $table->string('rank', 255)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('article_authors', function (Blueprint $table) {
            $table->string('orcid', 255)->nullable()->change();
            $table->string('spin', 255)->nullable()->change();
            // Остальные поля можно не откатывать
        });
    }
};