<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /**
     * @var string[]
     */
    public $fillable = ['title','parent_id', 'slug'];


    /**
     * @return HasMany
     */
    public function children()
    {
        return $this->hasMany('App\Models\Category', 'parent_id');
    }

    /**
     * @return HasMany
     */
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');

    }

    /**
     * @return BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo('App\Models\Category','parent_id');
    }


    /**
     * @return BelongsTo
     */
    public function parentRecursive()
    {
        return $this->parent()->with('parentRecursive');
    }
}
