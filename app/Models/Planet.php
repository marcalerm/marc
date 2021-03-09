<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Planet extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'x',
        'y',
        'obstacles'
    ];

    protected $casts = [
        'obstacles' => 'array'
    ];

    public function getFullNameAttribute()
    {
        return $this->name . ': (' . $this->x.'x' . $this->y.')';
    }
}
