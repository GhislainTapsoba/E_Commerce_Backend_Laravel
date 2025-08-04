{{-- resources/views/emails/orders/status-update.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mise à jour de commande - {{ $order->order_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f8f9fa; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #ffc107 0%, #ff8f00 100%); color: white; padding: 20px; text-align: center; }
        .content { padding: 30px; }
        .status-update { padding: 20px; border-radius: 6px; margin: 20px 0; text-align: center; }
        .status-nouvelle { background-color: #cce5ff; }
        .status-en_cours_livraison { background-color: #fff3cd; }
        .status-livree { background-color: #d1f2eb; }
        .status-annulee { background-color: #f8d7da; }
        .status-payee { background-color: #d4edda; }
        .order-info { background-color: #f8f9fa; padding: 15px; border-radius: 6px; margin: 15px 0; }
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📦 Mise à jour de commande</h1>
            <p>Commande #{{ $order->order_number }}</p>
        </div>
        
        <div class="content">
            <p>Bonjour <strong>{{ $order->customer->name }}</strong>,</p>
            
            <p>Le statut de votre commande a été mis à jour :</p>
            
            <div class="status-update status-{{ $order->status }}">
                <h2>
                    @switch($order->status)
                        @case('nouvelle')
                            🆕 Nouvelle commande
                            @break
                        @case('en_cours_livraison')
                            🚚 En cours de livraison
                            @break
                        @case('livree')
                            ✅ Livrée
                            @break
                        @case('annulee')
                            ❌ Annulée
                            @break
                        @case('payee')
                            💰 Payée
                            @break
                        @default
                            📦 {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    @endswitch
                </h2>
                
                @switch($order->status)
                    @case('nouvelle')
                        <p>Votre commande est confirmée et en cours de traitement.</p>
                        @break
                    @case('en_cours_livraison')
                        <p>Votre commande est en route ! Notre livreur va vous contacter bientôt.</p>
                        @break
                    @case('livree')
                        <p>Votre commande a été livrée avec succès ! Merci pour votre confiance.</p>
                        @if($order->delivered_at)
                        <p><small>Livrée le {{ $order->delivered_at->format('d/m/Y à H:i') }}</small></p>
                        @endif
                        @break
                    @case('annulee')
                        <p>Votre commande a été annulée. Si vous avez des questions, contactez-nous.</p>
                        @break
                    @case('payee')
                        <p>Le paiement de votre commande a été confirmé.</p>
                        @break
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
            <div style="background-color: #e8f5e8; padding: 15px; border-radius: 6px; margin: 15px 0;">
                <h4>📱 Information importante</h4>
                <p>Notre livreur vous contactera au <strong>{{ $order->customer->phone }}</strong> pour coordonner la livraison.</p>
                <p>Assurez-vous que votre téléphone soit disponible.</p>
            </div>
            @endif
            
            @if($order->status == 'livree')
            <div style="background-color: #e8f5e8; padding: 15px; border-radius: 6px; margin: 15px 0;">
                <h4>⭐ Votre avis nous intéresse</h4>
                <p>Nous espérons que vous êtes satisfait de votre commande. N'hésitez pas à nous faire part de vos commentaires !</p>
            </div>
            @endif
            
            <div style="background-color: #f0f8ff; padding: 20px; border-radius: 6px; margin: 20px 0;">
                <h4>📞 Besoin d'aide ?</h4>
                <p>Pour toute question concernant votre commande :</p>
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