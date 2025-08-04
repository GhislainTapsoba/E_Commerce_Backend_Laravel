<?php

// app/Mail/OrderStatusUpdate.php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        $this->order->load(['customer', 'deliveryZone', 'items']);
        
        $statusLabels = [
            'nouvelle' => 'Nouvelle',
            'en_cours_livraison' => 'En cours de livraison',
            'livree' => 'Livrée',
            'annulee' => 'Annulée',
            'payee' => 'Payée',
        ];

        $statusLabel = $statusLabels[$this->order->status] ?? $this->order->status;

        return $this->subject('Mise à jour de votre commande #' . $this->order->order_number . ' - ' . $statusLabel)
                    ->view('emails.orders.status-update');
    }
}
