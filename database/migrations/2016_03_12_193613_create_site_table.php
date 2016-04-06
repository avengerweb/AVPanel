<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string("url")->unique();
            $table->tinyInteger("status")->default(\App\Models\Site::STATUS_DISABLED);
            $table->string("directory");
            $table->string("slug");
            $table->unsignedInteger("user_id");
            $table->foreign('user_id')->references('id')->on('users');

            $table->boolean("access_log");
            $table->boolean("error_log");
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sites');
    }
}
