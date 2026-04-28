<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('article_authors', function (Blueprint $table) {
            // Чувашская версия фамилии
            $table->string('surname_cv', 255)->nullable()->after('surname_en')->comment('Фамилия (чуваш)');
            
            // Имя (рус, англ, чув)
            $table->string('name_ru', 255)->nullable()->after('surname_cv')->comment('Имя (рус)');
            $table->string('name_en', 255)->nullable()->after('name_ru')->comment('Имя (англ)');
            $table->string('name_cv', 255)->nullable()->after('name_en')->comment('Имя (чуваш)');
            
            // Отчество (рус, англ, чув)
            $table->string('patronymic_ru', 255)->nullable()->after('name_cv')->comment('Отчество (рус)');
            $table->string('patronymic_en', 255)->nullable()->after('patronymic_ru')->comment('Отчество (англ)');
            $table->string('patronymic_cv', 255)->nullable()->after('patronymic_en')->comment('Отчество (чуваш)');
            
            // Чувашская версия инициалов
            $table->string('initials_cv', 50)->nullable()->after('initials_en')->comment('Инициалы (чуваш)');
            
            // Чувашская версия организации
            $table->string('org_name_cv', 500)->nullable()->after('org_name_en')->comment('Организация (чуваш)');
            
            // Чувашская версия города
            $table->string('town_cv', 255)->nullable()->after('town_en')->comment('Город (чуваш)');
            
            // Чувашская версия страны
            $table->string('country_cv', 255)->nullable()->after('country_en')->comment('Страна (чуваш)');
            
            // Должность (рус, англ, чув)
            $table->string('position_ru', 255)->nullable()->after('country_cv')->comment('Должность (рус)');
            $table->string('position_en', 255)->nullable()->after('position_ru')->comment('Должность (англ)');
            $table->string('position_cv', 255)->nullable()->after('position_en')->comment('Должность (чуваш)');
            
            // Ученая степень и звание
            $table->string('degree', 255)->nullable()->after('position_cv')->comment('Ученая степень');
            $table->string('rank', 255)->nullable()->after('degree')->comment('Звание');
        });
    }

    public function down()
    {
        Schema::table('article_authors', function (Blueprint $table) {
            $table->dropColumn([
                'surname_cv',
                'name_ru', 'name_en', 'name_cv',
                'patronymic_ru', 'patronymic_en', 'patronymic_cv',
                'initials_cv',
                'org_name_cv',
                'town_cv',
                'country_cv',
                'position_ru', 'position_en', 'position_cv',
                'degree', 'rank'
            ]);
        });
    }
};