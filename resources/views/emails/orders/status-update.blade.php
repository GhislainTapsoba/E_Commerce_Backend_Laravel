{{-- resources/views/emails/orders/status-update.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Mise à jour de commande - {{ $order->order_number }}</title>
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
            background: linear-gradient(135deg, #ffc107 0%, #ff8f00 100%);
            color: #fff;
            text-align: center;
            padding: 25px;
        }
        .header h1 { margin: 0; font-size: 28px; }
        .header p { margin: 5px 0 0; font-size: 16px; }

        .content { padding: 30px; }

        .status-box {
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            margin: 20px 0;
            color: #333;
            font-size: 16px;
            font-weight: 600;
        }
        .status-nouvelle { background-color: #cce5ff; }
        .status-en_cours_livraison { background-color: #fff3cd; }
        .status-livree { background-color: #d1f2eb; }
        .status-annulee { background-color: #f8d7da; }
        .status-payee { background-color: #d4edda; }

        .order-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .section {
            background-color: #f0f8ff;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

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
            <h1>📦 Mise à jour de votre commande</h1>
            <p>Commande #{{ $order->order_number }}</p>
        </div>

        <div class="content">
            <p>Bonjour <strong>{{ $order->customer->name }}</strong>,</p>
            <p>Le statut de votre commande a été mis à jour :</p>

            <div class="status-box status-{{ $order->status }}">
                @switch($order->status)
                    @case('nouvelle')
                        🆕 Nouvelle commande<br>
                        <small>Votre commande est confirmée et en cours de traitement.</small>
                        @break
                    @case('en_cours_livraison')
                        🚚 En cours de livraison<br>
                        <small>Notre livreur vous contactera bientôt.</small>
                        @break
                    @case('livree')
                        ✅ Livrée<br>
                        <small>Votre commande a été livrée avec succès ! Merci pour votre confiance.</small>
                        @if($order->delivered_at)
                        <br><small>Livrée le {{ $order->delivered_at->format('d/m/Y à H:i') }}</small>
                        @endif
                        @break
                    @case('annulee')
                        ❌ Annulée<br>
                        <small>Votre commande a été annulée. Contactez-nous pour plus d’informations.</small>
                        @break
                    @case('payee')
                        💰 Payée<br>
                        <small>Le paiement de votre commande a été confirmé.</small>
                        @break
                    @default
                        📦 {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                @endswitch
            </div>

            <div class="order-info">
                <h4>📋 Informations de commande</h4>
                <p><strong>Numéro :</strong> {{ $order->order_number }}</p>
                <p><strong>Total :</strong> {{ number_format($order->total) }} F</p>
                <p><strong>Zone de livraison :</strong> {{ $order->deliveryZone->name }}</p>
                <p><strong>Adresse :</strong> {{ $order->customer->address }}</p>
            </div>

            @if($order->status == 'en_cours_livraison')
            <div class="section">
                <h4>📱 Information importante</h4>
                <p>Notre livreur vous contactera au <strong>{{ $order->customer->phone }}</strong> pour coordonner la livraison.</p>
                <p>Veuillez rester disponible pour recevoir votre commande.</p>
            </div>
            @endif

            @if($order->status == 'livree')
            <div class="section">
                <h4>⭐ Votre avis compte !</h4>
                <p>Nous espérons que vous êtes satisfait de votre commande. Partagez vos commentaires pour nous aider à nous améliorer.</p>
            </div>
            @endif

            <div class="section" style="text-align: center;">
                <h4>📞 Besoin d'aide ?</h4>
                <p>Pour toute question concernant votre commande :</p>
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
