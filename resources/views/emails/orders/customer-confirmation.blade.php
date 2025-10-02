{{-- resources/views/emails/orders/customer-confirmation.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Confirmation de commande - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0; padding: 20px;
            background-color: #f4f6f9;
        }
        .container {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: #fff;
            text-align: center;
            padding: 25px;
        }
        .header h1 { margin: 0; font-size: 28px; }
        .header p { margin: 5px 0 0; font-size: 16px; }

        .content { padding: 30px; }

        .section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .section h3 { margin-top: 0; color: #333; }

        .status-box {
            background-color: #d1ecf1;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
            font-size: 16px;
        }

        .items-table {
            width: 100%; border-collapse: collapse; margin-top: 15px;
        }
        .items-table th, .items-table td {
            padding: 12px; text-align: left; border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
        }
        .items-table th {
            background-color: #f1f3f5; font-weight: 600; color: #555;
        }
        .total-row {
            font-weight: bold; background-color: #eef1f5;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            color: #fff;
        }
        .badge-subtotal { background-color: #6c757d; }
        .badge-delivery { background-color: #17a2b8; }
        .badge-total { background-color: #28a745; }

        .btn {
            display: inline-block;
            padding: 12px 28px;
            background-color: #28a745;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 15px;
            transition: background-color 0.3s;
        }
        .btn:hover { background-color: #218838; }

        .footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 20px;
            color: #6c757d;
            font-size: 13px;
        }

        @media only screen and (max-width: 600px) {
            .container { padding: 10px; }
            .content { padding: 20px; }
            .btn { width: 100%; text-align: center; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Commande Confirmée</h1>
            <p>Merci pour votre commande, {{ $order->customer->name }} !</p>
        </div>

        <div class="content">
            <div class="section">
                <h3>📋 Récapitulatif de commande</h3>
                <p><strong>Numéro :</strong> {{ $order->order_number }}</p>
                <p><strong>Date :</strong> {{ $order->created_at->format('d/m/Y à H:i') }}</p>
                <p><strong>Zone de livraison :</strong> {{ $order->deliveryZone->name }}</p>
                <p><strong>Adresse :</strong> {{ $order->customer->address }}</p>
            </div>

            <div class="status-box">
                📦 <strong>Statut actuel :</strong> {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                <p>Nous vous tiendrons informé de l'évolution de votre commande.</p>
            </div>

            <div class="section">
                <h3>🛒 Articles commandés</h3>
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
                            <td><span class="badge badge-subtotal">{{ number_format($order->subtotal) }} F</span></td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="3">Frais de livraison</td>
                            <td><span class="badge badge-delivery">{{ number_format($order->delivery_fee) }} F</span></td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="3"><strong>TOTAL À PAYER</strong></td>
                            <td><span class="badge badge-total">{{ number_format($order->total) }} F</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($order->remarks)
            <div class="section" style="background-color: #fff3cd;">
                <h4>💬 Vos remarques</h4>
                <p>{{ $order->remarks }}</p>
            </div>
            @endif

            <div class="section" style="background-color: #e8f5e8;">
                <h4>🚚 Informations de livraison</h4>
                <p><strong>Zone :</strong> {{ $order->deliveryZone->name }}</p>
                <p><strong>Frais :</strong> {{ number_format($order->delivery_fee) }} F</p>
                @if($order->deliveryZone->delivery_time_min && $order->deliveryZone->delivery_time_max)
                <p><strong>Délai estimé :</strong> {{ $order->deliveryZone->delivery_time_min }}-{{ $order->deliveryZone->delivery_time_max }} minutes</p>
                @endif
            </div>

            <div class="section" style="background-color: #f0f8ff; text-align: center;">
                <h4>📞 Besoin d'aide ?</h4>
                <p>Contactez-nous si nécessaire :</p>
                <p><strong>Téléphone :</strong> +226 05 92 98 83</p>
                <p><strong>WhatsApp :</strong> +226 05 92 98 83</p>
            </div>
        </div>

        <div class="footer">
            Merci de faire confiance à <strong>{{ config('app.name') }}</strong> !<br>
            {{ now()->format('d/m/Y à H:i') }}
        </div>
    </div>
</body>
</html>
