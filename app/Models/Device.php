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
    protected $primaryKey = 'id'; // Pastikan kolom 'id' sebagai primary key
    public $incrementing = false; // Nonaktifkan auto-increment
    protected $keyType = 'string'; // Jika `id` berupa string, gunakan 'string', jika integer tetap 'int'

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'device_name', 
    ];

    /**
     * Relationships
     */

    // A device has many sensor histories
    public function sensorHistories()
    {
        return $this->hasMany(SensorHistory::class);
    }

    // Relasi ke sensor terakhir
    public function latestSensorHistory()
    {
        return $this->hasOne(SensorHistory::class, 'device_id')->latest('recorded_at');
    }
}
