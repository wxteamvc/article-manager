<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (!Schema::hasTable('admin_article_category')) {
            Schema::create('admin_article_category', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title')->comment('文章分类名');
                $table->string('remark')->nullable(true)->comment('文章分类备注');
                $table->integer('admin_id')->comment('管理员id');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('admin_article_category');
    }
}
