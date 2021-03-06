<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomContentsTable extends Migration
{
    /**
     * Run the migrations.min
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('website_id');
            $table->integer('language_id');
            $table->string('key');
            $table->string('value')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_contents');
    }
}
