<?php
// app/Models/DeliveryZone.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'delivery_fee',
        'is_active',
        'delivery_time_min',
        'delivery_time_max',
        'coordinates'
    ];

    protected $casts = [
        'coordinates' => 'array',
        'is_active' => 'boolean',
        'delivery_fee' => 'decimal:2'
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
