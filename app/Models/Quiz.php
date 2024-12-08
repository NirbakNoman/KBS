<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $table = 'quizzes';

    protected $fillable = ["article_id", "quiz_form_id", "name", "slug", "duration", "total_marks", "number_of_questions", "status"];

    public function quizForm(){

        return $this->belongsTo(QuizForm::class);

    }
}
