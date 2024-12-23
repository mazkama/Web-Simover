<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    /**
     * Table associated with the model.
     */
    protected $table = 'devices';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'device_name',
        'location',
        'status',
    ];

    /**
     * Relationships
     */

    // A device has many sensor histories
    public function sensorHistories()
    {
        return $this->hasMany(SensorHistory::class);
    }
}
