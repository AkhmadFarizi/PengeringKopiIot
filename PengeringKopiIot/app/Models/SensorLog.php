<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorLog extends Model
{
    /** @use HasFactory<\Database\Factories\SensorLogFactory> */
    use HasFactory;
     protected $fillable = [
        'suhu',
        'rpm',
        'arus',
        'relayFan',
        'relayHeater',
    ];
}
