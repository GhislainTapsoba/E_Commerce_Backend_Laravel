<?php

// app/Services/EmailService.php

namespace App\Services;

use App\Models\Order;
use App\Models\Notification;
use App\Mail\NewOrderNotification;
use App\Mail\OrderStatusUpdate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    public function sendNewOrderNotification(Order $order)
    {
        try {
            // Email à l'administrateur
            $adminEmail = config('mail.admin_email', 'admin@ecms.com');
            
            Mail::to($adminEmail)->send(new NewOrderNotification($order));
            
            // Enregistrer la notification
            Notification::create([
                'type' => 'email',
                'recipient' => $adminEmail,
                'subject' => 'Nouvelle commande #' . $order->order_number,
                'message' => 'Une nouvelle commande a été passée.',
                'status' => 'sent',
                'order_id' => $order->id,
                'sent_at' => now(),
            ]);

            // Email au client (si email fourni)
            if ($order->customer->email) {
                Mail::to($order->customer->email)->send(new NewOrderNotification($order, true));
                
                Notification::create([
                    'type' => 'email',
                    'recipient' => $order->customer->email,
                    'subject' => 'Confirmation de commande #' . $order->order_number,
                    'message' => 'Votre commande a été confirmée.',
                    'status' => 'sent',
                    'order_id' => $order->id,
                    'sent_at' => now(),
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Erreur envoi email nouvelle commande: ' . $e->getMessage());
            
            Notification::create([
                'type' => 'email',
                'recipient' => $adminEmail ?? 'admin@ecms.com',
                'subject' => 'Nouvelle commande #' . $order->order_number,
                'message' => 'Une nouvelle commande a été passée.',
                'status' => 'failed',
                'order_id' => $order->id,
                'metadata' => ['error' => $e->getMessage()],
            ]);
        }
    }

    public function sendOrderStatusUpdate(Order $order)
    {
        try {
            if ($order->customer->email) {
                Mail::to($order->customer->email)->send(new OrderStatusUpdate($order));
                
                Notification::create([
                    'type' => 'email',
                    'recipient' => $order->customer->email,
                    'subject' => 'Mise à jour commande #' . $order->order_number,
                    'message' => 'Le statut de votre commande a été mis à jour: ' . $order->status,
                    'status' => 'sent',
                    'order_id' => $order->id,
                    'sent_at' => now(),
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Erreur envoi email mise à jour commande: ' . $e->getMessage());
            
            Notification::create([
                'type' => 'email',
                'recipient' => $order->customer->email ?? 'unknown',
                'subject' => 'Mise à jour commande #' . $order->order_number,
                'message' => 'Le statut de votre commande a été mis à jour: ' . $order->status,
                'status' => 'failed',
                'order_id' => $order->id,
                'metadata' => ['error' => $e->getMessage()],
            ]);
        }
    }
}