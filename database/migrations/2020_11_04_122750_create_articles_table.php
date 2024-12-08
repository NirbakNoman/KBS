<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->uuid('category_id');
            $table->string('en_title')->index();
            $table->string('bn_title')->index()->nullable();
            $table->text('tag')->nullable();
            $table->string('slug')->unique();
            $table->text('en_short_summary')->nullable();
            $table->text('bn_short_summary')->nullable();
            $table->binary('en_body')->nullable();
            $table->binary('bn_body')->nullable();
            $table->enum('status', ['draft', 'hide', 'private', 'public']);
            $table->timestamp('publish_date')->nullable();
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
        Schema::dropIfExists('articles');
    }
}
