<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateArticleMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (Schema::hasTable('admin_article_media')){
            Schema::table('admin_article_media', function (Blueprint $table) {
                $table->string('type', 50)->after('url')->default('wx')->comment('文章类型,wx=微信公众号文章,origin=原创');
                $table->integer('category_id')->after('id')->default(1)->comment('文章分类id');
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
        if (Schema::hasTable('admin_article_media')){
            Schema::table('admin_article_media', function (Blueprint $table){
                $table->dropColumn('type');
                $table->dropColumn('category_id');
            });
        }
    }
}

