{-- resources/views/emails/orders/customer-confirmation.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Confirmation de commande - {{ $order->order_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f8f9fa; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 20px; text-align: center; }
        .content { padding: 30px; }
        .order-summary { background-color: #f8f9fa; padding: 20px; border-radius: 6px; margin: 20px 0; }
        .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .items-table th, .items-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .items-table th { background-color: #f8f9fa; font-weight: 600; }
        .total-row { font-weight: bold; background-color: #f0f0f0; }
        .delivery-info { background-color: #e8f5e8; padding: 15px; border-radius: 6px; margin: 15px 0; }
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 14px; }
        .status-box { background-color: #d1ecf1; padding: 15px; border-radius: 6px; text-align: center; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Commande Confirmée</h1>
            <p>Merci pour votre commande !</p>
        </div>
        
        <div class="content">
            <p>Bonjour <strong>{{ $order->customer->name }}</strong>,</p>
            
            <p>Nous avons bien reçu votre commande et nous vous remercions pour votre confiance. Voici un récapitulatif de votre commande :</p>
            
            <div class="order-summary">
                <h3>📋 Récapitulatif de commande</h3>
                <p><strong>Numéro de commande :</strong> {{ $order->order_number }}</p>
                <p><strong>Date :</strong> {{ $order->created_at->format('d/m/Y à H:i') }}</p>
                <p><strong>Zone de livraison :</strong> {{ $order->deliveryZone->name }}</p>
                <p><strong>Adresse de livraison :</strong> {{ $order->customer->address }}</p>
            </div>
            
            <div class="status-box">
                <h4>📦 Statut actuel : {{ ucfirst(str_replace('_', ' ', $order->status)) }}</h4>
                <p>Nous vous tiendrons informé de l'évolution de votre commande.</p>
            </div>
            
            <h3>🛒 Vos articles</h3>
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
                        <td><strong>{{ $item->product_name }}</strong></td>
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
                        <td colspan="3"><strong>TOTAL À PAYER</strong></td>
                        <td><strong>{{ number_format($order->total) }} F</strong></td>
                    </tr>
                </tbody>
            </table>
            
            <div class="delivery-info">
                <h4>🚚 Informations de livraison</h4>
                <p><strong>Zone :</strong> {{ $order->deliveryZone->name }}</p>
                <p><strong>Frais de livraison :</strong> {{ number_format($order->delivery_fee) }} F</p>
                @if($order->deliveryZone->delivery_time_min && $order->deliveryZone->delivery_time_max)
                <p><strong>Délai estimé :</strong> {{ $order->deliveryZone->delivery_time_min }}-{{ $order->deliveryZone->delivery_time_max }} minutes</p>
                @endif
            </div>
            
            @if($order->remarks)
            <div style="background-color: #fff3cd; padding: 15px; border-radius: 6px; margin: 15px 0;">
                <h4>💬 Vos remarques</h4>
                <p>{{ $order->remarks }}</p>
            </div>
            @endif
            
            <div style="background-color: #f0f8ff; padding: 20px; border-radius: 6px; margin: 20px 0;">
                <h4>📞 Besoin d'aide ?</h4>
                <p>Si vous avez des questions concernant votre commande, n'hésitez pas à nous contacter :</p>
                <p><strong>Téléphone :</strong> +226 XX XX XX XX</p>
                <p><strong>WhatsApp :</strong> +226 XX XX XX XX</p>
            </div>
        </div>
        
        <div class="footer">
            <p>Merci de faire confiance à {{ config('app.name') }} !</p>
            <p>{{ now()->format('d/m/Y à H:i') }}</p>
        </div>
    </div>
</body>
</html>
