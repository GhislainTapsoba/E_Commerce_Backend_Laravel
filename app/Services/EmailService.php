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
    /**
     * Envoi des emails pour une nouvelle commande
     */
    public function sendNewOrderNotification(Order $order)
    {
        $adminEmail = config('mail.admin_email', 'arseneghislaintaps@gmail.com');

        // Tableau des destinataires
        $recipients = [
            ['email' => $adminEmail, 'isCustomer' => false],
        ];

        if ($order->customer?->email) {
            $recipients[] = ['email' => $order->customer->email, 'isCustomer' => true];
        }

        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient['email'])->send(new NewOrderNotification($order, $recipient['isCustomer']));

                // Enregistrement notification réussie
                Notification::create([
                    'type' => 'email',
                    'category' => 'order',
                    'recipient' => $recipient['email'],
                    'subject' => $recipient['isCustomer']
                        ? 'Confirmation de commande #' . $order->order_number
                        : 'Nouvelle commande #' . $order->order_number,
                    'message' => $recipient['isCustomer']
                        ? 'Votre commande a été confirmée.'
                        : 'Une nouvelle commande a été passée.',
                    'status' => 'sent',
                    'order_id' => $order->id,
                    'sent_at' => now(),
                ]);

            } catch (\Exception $e) {
                Log::error('Erreur envoi email nouvelle commande à ' . $recipient['email'] . ': ' . $e->getMessage());

                Notification::create([
                    'type' => 'email',
                    'category' => 'order',
                    'recipient' => $recipient['email'],
                    'subject' => $recipient['isCustomer']
                        ? 'Confirmation de commande #' . $order->order_number
                        : 'Nouvelle commande #' . $order->order_number,
                    'message' => $recipient['isCustomer']
                        ? 'Votre commande a été confirmée.'
                        : 'Une nouvelle commande a été passée.',
                    'status' => 'failed',
                    'order_id' => $order->id,
                    'metadata' => ['error' => $e->getMessage()],
                ]);
            }
        }
    }

    /**
     * Envoi des emails pour mise à jour du statut de commande
     */
    public function sendOrderStatusUpdate(Order $order)
    {
        if (!$order->customer?->email) return;

        try {
            Mail::to($order->customer->email)->send(new OrderStatusUpdate($order));

            Notification::create([
                'type' => 'email',
                'category' => 'order',
                'recipient' => $order->customer->email,
                'subject' => 'Mise à jour commande #' . $order->order_number,
                'message' => 'Le statut de votre commande a été mis à jour: ' . $order->status,
                'status' => 'sent',
                'order_id' => $order->id,
                'sent_at' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur envoi email mise à jour commande à ' . $order->customer->email . ': ' . $e->getMessage());

            Notification::create([
                'type' => 'email',
                'category' => 'order',
                'recipient' => $order->customer->email,
                'subject' => 'Mise à jour commande #' . $order->order_number,
                'message' => 'Le statut de votre commande a été mis à jour: ' . $order->status,
                'status' => 'failed',
                'order_id' => $order->id,
                'metadata' => ['error' => $e->getMessage()],
            ]);
        }
    }
}
