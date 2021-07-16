<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExtendConfigOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (!Schema::hasTable('admin_extend_config_options')){
            Schema::create('admin_extend_config_options', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('config_id')->comment('配置id');
                $table->string('options_key')->comment('选项的key');
                $table->text('options_value')->comment('选项的值');
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
        Schema::dropIfExists('admin_extend_config_options');
    }
}
