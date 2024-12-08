<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizForm extends Model
{
    protected $fillable = ['id', 'name', 'slug'];

    public function quizFormField(){
        return $this->hasMany(QuizFormField::class);
    }
}
