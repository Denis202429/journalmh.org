<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('article_authors', function (Blueprint $table) {
            if (!Schema::hasColumn('article_authors', 'org_name_cv')) {
                $table->string('org_name_cv', 500)->nullable()->after('org_name_en')->comment('Организация (чуваш)');
            }
            if (!Schema::hasColumn('article_authors', 'town_cv')) {
                $table->string('town_cv', 255)->nullable()->after('town_en')->comment('Город (чуваш)');
            }
            if (!Schema::hasColumn('article_authors', 'country_cv')) {
                $table->string('country_cv', 255)->nullable()->after('country_en')->comment('Страна (чуваш)');
            }
            if (!Schema::hasColumn('article_authors', 'position_cv')) {
                $table->string('position_cv', 255)->nullable()->after('position_en')->comment('Должность (чуваш)');
            }
        });
    }

    public function down()
    {
        Schema::table('article_authors', function (Blueprint $table) {
            $table->dropColumn(['org_name_cv', 'town_cv', 'country_cv', 'position_cv']);
        });
    }
};