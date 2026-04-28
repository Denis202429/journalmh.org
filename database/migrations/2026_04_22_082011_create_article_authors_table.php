<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('article_authors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('author_num')->comment('Порядковый номер автора');
            $table->string('author_id')->nullable()->comment('ID автора в eLibrary');
            
            // Роль автора (role)
            $table->string('role', 2)->nullable()->comment('0-редактор, 23-рецензент и т.д.');
            
            // Автор-корреспондент (correspondent)
            $table->boolean('is_correspondent')->default(false);
            
            // Идентификаторы автора (authorCodes)
            $table->string('researcherid')->nullable();
            $table->string('spin', 9)->nullable()->comment('SPIN-код: XXXX-XXXX');
            $table->string('scopusid')->nullable();
            $table->string('orcid', 19)->nullable()->comment('ORCID: 0000-0000-0000-0000');
            
            // Индивидуальные сведения на разных языках (individInfo)
            $table->string('surname_ru')->nullable();
            $table->string('surname_en')->nullable();
            $table->string('initials_ru')->nullable();
            $table->string('initials_en')->nullable();
            $table->text('address_ru')->nullable();
            $table->text('address_en')->nullable();
            $table->string('town_ru')->nullable();
            $table->string('town_en')->nullable();
            $table->string('country_ru')->nullable();
            $table->string('country_en')->nullable();
            $table->text('other_info_ru')->nullable();
            $table->text('other_info_en')->nullable();
            $table->text('comment')->nullable()->comment('Текст рецензии');
            $table->date('comment_date')->nullable();
            $table->string('org_name_ru')->nullable();
            $table->string('org_name_en')->nullable();
            $table->boolean('org_not_authentic')->default(false);
            $table->string('email')->nullable();
            $table->boolean('email_not_authentic')->default(false);
            
            $table->timestamps();
            
            $table->index(['article_id', 'author_num']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('article_authors');
    }
};