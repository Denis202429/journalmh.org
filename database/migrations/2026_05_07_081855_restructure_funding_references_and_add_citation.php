<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Шаг 1: Переименовываем fundings -> fundings_ru
        Schema::table('articles', function (Blueprint $table) {
            if (Schema::hasColumn('articles', 'fundings')) {
                $table->renameColumn('fundings', 'fundings_ru');
            }
        });

        // Шаг 2: Добавляем fundings_en и fundings_cv
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'fundings_en')) {
                $table->json('fundings_en')->nullable()->after('fundings_ru');
            }
            if (!Schema::hasColumn('articles', 'fundings_cv')) {
                $table->json('fundings_cv')->nullable()->after('fundings_en');
            }
        });

        // Шаг 3: Переименовываем references -> references_ru
        Schema::table('articles', function (Blueprint $table) {
            if (Schema::hasColumn('articles', 'references')) {
                $table->renameColumn('references', 'references_ru');
            }
        });

        // Шаг 4: Добавляем references_en
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'references_en')) {
                $table->json('references_en')->nullable()->after('references_ru');
            }
        });

        // Шаг 5: Добавляем поля для цитирования
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'citation_ru')) {
                $table->text('citation_ru')->nullable()->after('references_en');
            }
            if (!Schema::hasColumn('articles', 'citation_en')) {
                $table->text('citation_en')->nullable()->after('citation_ru');
            }
            if (!Schema::hasColumn('articles', 'citation_cv')) {
                $table->text('citation_cv')->nullable()->after('citation_en');
            }
        });
    }

    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            // Удаляем добавленные поля
            $table->dropColumn([
                'fundings_en',
                'fundings_cv',
                'references_en',
                'citation_ru',
                'citation_en',
                'citation_cv'
            ]);
            
            // Возвращаем обратно имена
            $table->renameColumn('fundings_ru', 'fundings');
            $table->renameColumn('references_ru', 'references');
        });
    }
};