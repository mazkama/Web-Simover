<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorHistory extends Model
{
    use HasFactory;

    /**
     * Table associated with the model.
     */
    protected $table = 'sensor_histories';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'device_id',
        'temperature',
        'humidity',
        'smoke',
        'motion',
        'recorded_at',
    ];

    /**
     * Relationships
     */

    // A sensor history belongs to a device
    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
