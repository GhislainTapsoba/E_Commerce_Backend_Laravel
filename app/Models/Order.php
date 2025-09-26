<?php

// app/Models/Order.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'delivery_zone_id',
        'status',
        'subtotal',
        'delivery_fee',
        'total',
        'remarks',
        'delivered_at',
        'created_by'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'delivered_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(8));
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function deliveryZone()
    {
        return $this->belongsTo(DeliveryZone::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'nouvelle' => 'bg-primary',
            'en_cours_livraison' => 'bg-warning',
            'livree' => 'bg-success',
            'annulee' => 'bg-danger',
            'payee' => 'bg-info'
        ];

        return $badges[$this->status] ?? 'bg-secondary';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function zone()
    {
        return $this->belongsTo(DeliveryZone::class);
    }
}
