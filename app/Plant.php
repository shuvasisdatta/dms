<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    public function documents() {
		  return $this->hasMany(Document::class);
    }

    public function equipments() {
		  return $this->hasMany(Equipment::class);
    }
}
