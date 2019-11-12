<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'parent_id'
    ];

    public function parent() {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function childrens() {
        return $this->hasMany(self::class, 'parent_id','id');
    }
}
