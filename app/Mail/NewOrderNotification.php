<?php
// app/Mail/NewOrderNotification.php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewOrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $isCustomer;

    public function __construct(Order $order, bool $isCustomer = false)
    {
        $this->order = $order;
        $this->isCustomer = $isCustomer;
    }

    public function build()
    {
        $this->order->load(['customer', 'deliveryZone', 'items']);
        
        if ($this->isCustomer) {
            return $this->subject('Confirmation de votre commande #' . $this->order->order_number)
                        ->view('emails.orders.customer-confirmation');
        }

        return $this->subject('Nouvelle commande #' . $this->order->order_number)
                    ->view('emails.orders.admin-notification');
    }
}
