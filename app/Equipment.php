<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'plant_id'
    ];

    public function documents() {
		  return $this->hasMany(Document::class);
    }

    public function plant() {
		  return $this->belongsTo(Plant::class);
    }
}
