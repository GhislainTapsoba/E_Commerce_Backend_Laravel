<?php

// app/Exports/OrdersExport.php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;
    protected $status;

    public function __construct($startDate, $endDate, $status = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }

    public function collection()
    {
        $query = Order::with(['customer', 'deliveryZone'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate]);

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Numéro de commande',
            'Date',
            'Client',
            'Téléphone',
            'Zone de livraison',
            'Statut',
            'Sous-total',
            'Frais de livraison',
            'Total',
            'Remarques',
        ];
    }

    public function map($order): array
    {
        return [
            $order->order_number,
            $order->created_at->format('d/m/Y H:i'),
            $order->customer->name,
            $order->customer->phone,
            $order->deliveryZone->name,
            $order->status,
            $order->subtotal,
            $order->delivery_fee,
            $order->total,
            $order->remarks,
        ];
    }
}