<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->longText('text_ru')->nullable()->after('keywords_cv')->comment('Полный текст статьи (рус)');
            $table->longText('text_en')->nullable()->after('text_ru')->comment('Полный текст статьи (англ)');
            $table->longText('text_cv')->nullable()->after('text_en')->comment('Полный текст статьи (чуваш)');
        });
    }

    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['text_ru', 'text_en', 'text_cv']);
        });
    }
};