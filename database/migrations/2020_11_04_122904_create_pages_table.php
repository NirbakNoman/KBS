<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('parent_id')->nullable()->index();
            $table->integer('position')->nullable();
            $table->integer('left')->nullable()->index();
            $table->integer('right')->nullable()->index();
            $table->integer('depth')->nullable();
            $table->string('name')->index();
            $table->string('slug');
            $table->string('outline')->longText()->nullable();
            $table->binary('description')->nullable();
            $table->integer('user_id')->unsigned();
            $table->integer('article_id')->unsigned();
            $table->integer('team_id')->unsigned();
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
        Schema::dropIfExists('pages');
    }
}
