<?php
// app/Services/OrderService.php

namespace App\Services;

use App\Models\Order;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Services\EmailService;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function createOrderFromFrontend(array $orderData)
    {
        return DB::transaction(function () use ($orderData) {
            // Créer ou récupérer le client
            $customer = Customer::firstOrCreate([
                'phone' => $orderData['customer']['phone'],
            ], [
                'name' => $orderData['customer']['name'],
                'email' => $orderData['customer']['email'] ?? null,
                'address' => $orderData['customer']['address'],
                'delivery_zone_id' => $orderData['delivery_zone_id'],
            ]);

            // Créer la commande
            $order = Order::create([
                'customer_id' => $customer->id,
                'delivery_zone_id' => $orderData['delivery_zone_id'],
                'subtotal' => $orderData['subtotal'],
                'delivery_fee' => $orderData['delivery_fee'],
                'total' => $orderData['total'],
                'remarks' => $orderData['remarks'] ?? null,
                'status' => 'nouvelle',
            ]);

            // Créer les items de commande
            foreach ($orderData['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_sku' => $item['product_sku'] ?? null,
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'total_price' => $item['total_price'],
                    'product_variants' => $item['variants'] ?? null,
                ]);
            }

            // Envoyer notification email
            $this->emailService->sendNewOrderNotification($order);

            return $order;
        });
    }

    public function updateOrderStatus(Order $order, string $status)
    {
        $oldStatus = $order->status;
        
        $order->update([
            'status' => $status,
            'delivered_at' => $status === 'livree' ? now() : null,
        ]);

        if ($oldStatus !== $status) {
            $this->sendStatusUpdateNotification($order);
        }

        return $order;
    }

    public function sendStatusUpdateNotification(Order $order)
    {
        $this->emailService->sendOrderStatusUpdate($order);
    }

    public function calculateOrderTotals(array $items, float $deliveryFee)
    {
        $subtotal = collect($items)->sum(function ($item) {
            return $item['unit_price'] * $item['quantity'];
        });

        return [
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total' => $subtotal + $deliveryFee,
        ];
    }
}
