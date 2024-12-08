<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizFormField extends Model
{
    protected $table = 'quiz_form_fields';

    protected $fillable = ["quiz_form_id", "f_label", "f_name", "f_id", "f_class", "f_type", "f_option_value", "f_default_value",
        "f_max_value", "f_sort_order", "f_required",];

    public function quizForm(){

        return $this->belongsToMany(QuizForm::class);

    }
}
