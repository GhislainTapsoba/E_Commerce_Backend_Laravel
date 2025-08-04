{{-- resources/views/emails/orders/admin-notification.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nouvelle commande - {{ $order->order_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f8f9fa; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; }
        .content { padding: 30px; }
        .order-details { background-color: #f8f9fa; padding: 20px; border-radius: 6px; margin: 20px 0; }
        .customer-info { background-color: #e3f2fd; padding: 15px; border-radius: 6px; margin: 15px 0; }
        .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .items-table th, .items-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .items-table th { background-color: #f8f9fa; font-weight: 600; }
        .total-row { font-weight: bold; background-color: #f0f0f0; }
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 14px; }
        .btn { display: inline-block; padding: 12px 24px; background-color: #007bff; color: white; text-decoration: none; border-radius: 6px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛒 Nouvelle Commande</h1>
            <p>Commande #{{ $order->order_number }}</p>
        </div>
        
        <div class="content">
            <h2>Détails de la commande</h2>
            
            <div class="order-details">
                <p><strong>Numéro de commande :</strong> {{ $order->order_number }}</p>
                <p><strong>Date :</strong> {{ $order->created_at->format('d/m/Y à H:i') }}</p>
                <p><strong>Statut :</strong> {{ ucfirst(str_replace('_', ' ', $order->status)) }}</p>
                <p><strong>Zone de livraison :</strong> {{ $order->deliveryZone->name }}</p>
            </div>
            
            <div class="customer-info">
                <h3>👤 Informations client</h3>
                <p><strong>Nom :</strong> {{ $order->customer->name }}</p>
                <p><strong>Téléphone :</strong> {{ $order->customer->phone }}</p>
                @if($order->customer->email)
                <p><strong>Email :</strong> {{ $order->customer->email }}</p>
                @endif
                <p><strong>Adresse :</strong> {{ $order->customer->address }}</p>
            </div>
            
            <h3>📦 Articles commandés</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Article</th>
                        <th>Prix unitaire</th>
                        <th>Quantité</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product_name }}</strong>
                            @if($item->product_sku)
                            <br><small>SKU: {{ $item->product_sku }}</small>
                            @endif
                        </td>
                        <td>{{ number_format($item->unit_price) }} F</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->total_price) }} F</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="3">Sous-total</td>
                        <td>{{ number_format($order->subtotal) }} F</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="3">Frais de livraison</td>
                        <td>{{ number_format($order->delivery_fee) }} F</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="3"><strong>TOTAL</strong></td>
                        <td><strong>{{ number_format($order->total) }} F</strong></td>
                    </tr>
                </tbody>
            </table>
            
            @if($order->remarks)
            <div style="background-color: #fff3cd; padding: 15px; border-radius: 6px; margin: 15px 0;">
                <h4>💬 Remarques du client</h4>
                <p>{{ $order->remarks }}</p>
            </div>
            @endif
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ config('app.url') }}/orders/{{ $order->id }}" class="btn">
                    Voir la commande dans l'admin
                </a>
            </div>
        </div>
        
        <div class="footer">
            <p>Cette notification a été envoyée automatiquement par le système {{ config('app.name') }}</p>
            <p>{{ now()->format('d/m/Y à H:i') }}</p>
        </div>
    </div>
</body>
</html>