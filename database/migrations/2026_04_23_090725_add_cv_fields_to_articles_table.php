<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            // Чувашские версии
            $table->string('title_cv', 500)->nullable()->after('title_en')->comment('Название статьи (чуваш)');
            $table->text('abstract_cv')->nullable()->after('abstract_en')->comment('Аннотация (чуваш)');
            $table->string('keywords_cv', 500)->nullable()->after('keywords_en')->comment('Ключевые слова (чуваш)');
        });
    }

    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['title_cv', 'abstract_cv', 'keywords_cv']);
        });
    }
};