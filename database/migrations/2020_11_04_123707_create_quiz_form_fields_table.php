<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizFormFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_form_fields', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quiz_form_id');
            $table->foreign('quiz_form_id')->references('id')->on('quiz_forms')->onDelete('cascade');
            $table->string('f_label');
            $table->string('f_name');
            $table->string('f_id');
            $table->string('f_class');
            $table->enum('f_type', ['Text','Email','Password','Number','Textarea','Radio','Checkbox', 'Select/Dropdown']);
            $table->text('f_option_value')->nullable();
            $table->string('f_default_value')->nullable();
            $table->integer('f_max_value')->nullable();
            $table->integer('f_sort_order')->nullable();
            $table->enum('f_required', [1,0])->nullable();
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
        Schema::dropIfExists('quiz_form_fields');
    }
}
