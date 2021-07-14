<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (!Schema::hasTable('admin_article_media')){
            Schema::create('admin_article_media', function (Blueprint $table) {
                $table->increments('id');
                $table->string('url')->comment('文章原地址');
                $table->string('title')->comment('文章标题');
                $table->text('content')->comment('文章内容');
                $table->text('admin_id')->comment('后台操作人id');
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
        Schema::dropIfExists('admin_article_media');
    }
}
