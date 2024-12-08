<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = ['url'];


    /**
     * @return MorphTo
     */
    public function Mediable()
    {
        return $this->morphTo();
    }

}
