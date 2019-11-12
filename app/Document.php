<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'type', 'slug', 'department_id', 'plant_id', 'equipment_id', 'category_id', 'locker_id', 'user_id'
    ];

    public function department() {
		  return $this->belongsTo(Department::class);
    }

    public function user() {
		  return $this->belongsTo(User::class);
    }
    
    public function category() {
		  return $this->belongsTo(Category::class);
	}
    
    public function locker() {
		  return $this->belongsTo(Locker::class);
	}
    
    public function plant() {
		  return $this->belongsTo(Plant::class);
	}
    
    public function equipment() {
		  return $this->belongsTo(Equipment::class);
	}
}
