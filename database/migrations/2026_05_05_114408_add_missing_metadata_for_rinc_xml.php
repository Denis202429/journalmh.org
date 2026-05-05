<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Добавляем titleid в issues
        if (!Schema::hasColumn('issues', 'titleid')) {
            Schema::table('issues', function (Blueprint $table) {
                $table->unsignedInteger('titleid')->nullable()->after('id');
            });
        }
        
        // 2. Добавляем разделы в articles
        if (!Schema::hasColumn('articles', 'section_ru')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->string('section_ru', 255)->nullable()->after('section_id');
                $table->string('section_en', 255)->nullable()->after('section_ru');
                $table->string('section_cv', 255)->nullable()->after('section_en');
            });
        }
        
        // 3. Добавляем недостающие поля в article_authors
        if (!Schema::hasColumn('article_authors', 'address_cv')) {
            Schema::table('article_authors', function (Blueprint $table) {
                $table->text('address_cv')->nullable()->after('address_en');
            });
        }
        
        if (!Schema::hasColumn('article_authors', 'degree_ru')) {
            Schema::table('article_authors', function (Blueprint $table) {
                $table->string('degree_ru', 255)->nullable()->after('degree');
                $table->string('degree_en', 255)->nullable()->after('degree_ru');
                $table->string('degree_cv', 255)->nullable()->after('degree_en');
            });
        }
        
        if (!Schema::hasColumn('article_authors', 'rank_ru')) {
            Schema::table('article_authors', function (Blueprint $table) {
                $table->string('rank_ru', 255)->nullable()->after('rank');
                $table->string('rank_en', 255)->nullable()->after('rank_ru');
                $table->string('rank_cv', 255)->nullable()->after('rank_en');
            });
        }
        
        if (!Schema::hasColumn('article_authors', 'other_info_cv')) {
            Schema::table('article_authors', function (Blueprint $table) {
                $table->text('other_info_cv')->nullable()->after('other_info_en');
            });
        }
    }

    public function down()
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->dropColumn('titleid');
        });
        
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['section_ru', 'section_en', 'section_cv']);
        });
        
        Schema::table('article_authors', function (Blueprint $table) {
            $table->dropColumn([
                'address_cv',
                'degree_ru', 'degree_en', 'degree_cv',
                'rank_ru', 'rank_en', 'rank_cv',
                'other_info_cv'
            ]);
        });
    }
};