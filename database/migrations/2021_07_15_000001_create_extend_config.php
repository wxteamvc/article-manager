<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExtendConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (!Schema::hasTable('admin_extend_config')){
            Schema::create('admin_extend_config', function (Blueprint $table) {
                $table->increments('id');
                $table->string('group', 50)->comment('配置分组');
                $table->string('title',50)->comment('配置名称');
                $table->string('key')->comment('配置的键');
                $table->text('value')->nullable(true)->comment('配置的值');
                $table->text('help')->nullable(true)->comment('字段提示信息');
                $table->string('field_type')->comment('字段类型,参考laravel-admin表单文档');
                $table->integer('admin_id')->default(1)->comment('后台操作人id');
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
        Schema::dropIfExists('admin_extend_config');
    }
}