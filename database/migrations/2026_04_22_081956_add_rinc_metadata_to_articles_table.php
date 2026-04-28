<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            // === Основные элементы XSD ===
            // $table->string('pages', 50)->nullable()->comment('Диапазон страниц');
            // $table->string('art_type', 10)->default('RAR')->comment('Тип статьи: RAR, REV, SCO, BRV, CNF и др.');
            // $table->string('lang_publ', 3)->default('RUS')->comment('Язык статьи: RUS, ENG и др.');
            
            // === Названия на разных языках (artTitles) ===
            // $table->string('title_ru', 500)->nullable()->comment('Название на русском');
            // $table->string('title_en', 500)->nullable()->comment('Название на английском');
            
            // === Аннотации (abstracts) ===
            // $table->text('abstract_ru')->nullable()->comment('Аннотация на русском');
            // $table->text('abstract_en')->nullable()->comment('Аннотация на английском');
            
            // === Ключевые слова (keywords) ===
            // $table->text('keywords_ru')->nullable()->comment('Ключевые слова на русском');
            // $table->text('keywords_en')->nullable()->comment('Ключевые слова на английском');
            
            // === Коды статьи (codes) ===
            // $table->string('doi', 100)->nullable()->comment('DOI статьи');
            // $table->string('edn', 6)->nullable()->comment('EDN (eLIBRARY Document Number) - 6 латинских символов');
            // $table->json('udk')->nullable()->comment('УДК (может быть несколько)');
            // $table->json('bbk')->nullable()->comment('ББК (может быть несколько)');
            // $table->string('vak', 50)->nullable()->comment('Код ВАК (старая номенклатура)');
            // $table->string('vak21', 50)->nullable()->comment('Код ВАК (новая номенклатура 2021)');
            // $table->json('jel')->nullable()->comment('Коды JEL');
            // $table->json('msc')->nullable()->comment('Коды MSC');
            // $table->json('pacs')->nullable()->comment('Коды PACS');
            // $table->json('anycode')->nullable()->comment('Другие коды');
            
            // === Рубрики ГРНТИ ===
            // $table->json('rubrics')->nullable()->comment('Рубрики ГРНТИ');
            
            // === Даты (dates) ===
            // $table->date('date_received')->nullable()->comment('Дата поступления в редакцию');
            // $table->date('date_accepted')->nullable()->comment('Дата принятия в печать');
            // $table->date('date_publication')->nullable()->comment('Дата публикации');
            
            // === Финансирование (fundings) ===
            // $table->json('fundings')->nullable()->comment('Финансовая поддержка');
            
            // === Ссылки (references) ===
            // $table->json('references')->nullable()->comment('Библиографические ссылки');
            
            // === Файлы (files) ===
            // $table->string('pdf_url')->nullable()->comment('Ссылка на PDF');
            // $table->json('additional_files')->nullable()->comment('Дополнительные файлы');
            
            // === Статусы ===
            // $table->boolean('is_published')->default(true);
            // $table->integer('sort_order')->nullable();
        });
    }

    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn([
                'pages', 'art_type', 'lang_publ', 'title_ru', 'title_en',
                'abstract_ru', 'abstract_en', 'keywords_ru', 'keywords_en',
                'doi', 'edn', 'udk', 'bbk', 'vak', 'vak21', 'jel', 'msc',
                'pacs', 'anycode', 'rubrics', 'date_received', 'date_accepted',
                'date_publication', 'fundings', 'references', 'additional_files'
            ]);
        });
    }
};