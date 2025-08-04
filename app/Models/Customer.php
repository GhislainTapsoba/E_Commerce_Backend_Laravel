<?php

// app/Models/Customer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'delivery_zone_id'
    ];

    public function deliveryZone()
    {
        return $this->belongsTo(DeliveryZone::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
