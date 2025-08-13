{{-- resources/views/orders/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Commande #' . $order->order_number)
@section('page-title', 'Commande #' . $order->order_number)

@section('content')
<!-- Notifications -->
@include('components.notifications')

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Commandes</a></li>
                    <li class="breadcrumb-item active">#{{ $order->order_number }}</li>
                </ol>
            </nav>
            <div class="btn-group">
                @can('orders.edit')
                <a href="{{ route('orders.edit', $order) }}" class="btn btn-outline-warning">
                    <i class="bi bi-pencil me-2"></i>
                    Modifier
                </a>
                @endcan
                <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                    <i class="bi bi-printer me-2"></i>
                    Imprimer
                </button>
                @can('orders.delete')
                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete('{{ $order->id }}')">
                    <i class="bi bi-trash me-2"></i>
                    Supprimer
                </button>
                @endcan
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Informations principales -->
    <div class="col-md-8">
        <!-- En-tête de commande -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3 class="mb-0">Commande #{{ $order->order_number }}</h3>
                        <p class="text-muted mb-0">Créée le {{ $order->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <span class="badge status-badge {{ $order->status_badge }} fs-6 px-3 py-2">
                            <i class="bi {{ $order->status_icon }} me-2"></i>
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations client et livraison -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-person me-2"></i>
                            Informations client
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong class="d-block">{{ $order->customer->name }}</strong>
                            <div class="text-muted">
                                <i class="bi bi-telephone me-1"></i>
                                <a href="tel:{{ $order->customer->phone }}" class="text-decoration-none">
                                    {{ $order->customer->phone }}
                                </a>
                            </div>
                            @if($order->customer->email)
                            <div class="text-muted">
                                <i class="bi bi-envelope me-1"></i>
                                <a href="mailto:{{ $order->customer->email }}" class="text-decoration-none">
                                    {{ $order->customer->email }}
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-truck me-2"></i>
                            Informations de livraison
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>Zone:</strong> {{ $order->deliveryZone->name }}
                        </div>
                        <div class="mb-2">
                            <strong>Frais:</strong> {{ number_format($order->delivery_fee) }} F
                        </div>
                        <div>
                            <strong>Adresse:</strong><br>
                            <span class="text-muted">{{ $order->delivery_address }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Articles de la commande -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-cart me-2"></i>
                    Articles commandés ({{ $order->orderItems->count() }} {{ $order->orderItems->count() > 1 ? 'articles' : 'article' }})
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Produit</th>
                                <th class="text-center" style="width: 100px;">Quantité</th>
                                <th class="text-end" style="width: 120px;">Prix unitaire</th>
                                <th class="text-end" style="width: 120px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->product_name }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark">{{ $item->quantity }}</span>
                                </td>
                                <td class="text-end">{{ number_format($item->unit_price) }} F</td>
                                <td class="text-end"><strong>{{ number_format($item->total_price) }} F</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end"><strong>Sous-total:</strong></td>
                                <td class="text-end"><strong>{{ number_format($order->subtotal) }} F</strong></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Frais de livraison:</strong></td>
                                <td class="text-end"><strong>{{ number_format($order->delivery_fee) }} F</strong></td>
                            </tr>
                            <tr class="table-primary">
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td class="text-end"><strong class="fs-5">{{ number_format($order->total) }} F</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($order->notes)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-sticky me-2"></i>
                    Notes
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $order->notes }}</p>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Actions rapides -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    Actions rapides
                </h5>
            </div>
            <div class="card-body">
                @if($order->status === 'nouvelle')
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success" onclick="updateStatus('en_cours_livraison')">
                        <i class="bi bi-truck me-2"></i>
                        Marquer en cours
                    </button>
                    <button type="button" class="btn btn-outline-danger" onclick="updateStatus('annulee')">
                        <i class="bi bi-x-circle me-2"></i>
                        Annuler la commande
                    </button>
                </div>
                @elseif($order->status === 'en_cours_livraison')
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success" onclick="updateStatus('livree')">
                        <i class="bi bi-check-circle me-2"></i>
                        Marquer comme livrée
                    </button>
                    <button type="button" class="btn btn-outline-warning" onclick="updateStatus('nouvelle')">
                        <i class="bi bi-arrow-left-circle me-2"></i>
                        Remettre en attente
                    </button>
                </div>
                @elseif($order->status === 'livree')
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary" onclick="updateStatus('payee')">
                        <i class="bi bi-credit-card me-2"></i>
                        Marquer comme payée
                    </button>
                    <button type="button" class="btn btn-outline-warning" onclick="updateStatus('en_cours_livraison')">
                        <i class="bi bi-arrow-left-circle me-2"></i>
                        Remettre en cours
                    </button>
                </div>
                @elseif($order->status === 'payee')
                <div class="text-center text-success">
                    <i class="bi bi-check-circle-fill" style="font-size: 2rem;"></i>
                    <p class="mb-0 mt-2">Commande terminée</p>
                </div>
                @else
                <div class="text-center text-danger">
                    <i class="bi bi-x-circle-fill" style="font-size: 2rem;"></i>
                    <p class="mb-0 mt-2">Commande annulée</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Récapitulatif financier -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-calculator me-2"></i>
                    Récapitulatif
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col">Nombre d'articles:</div>
                    <div class="col text-end"><strong>{{ $order->orderItems->sum('quantity') }}</strong></div>
                </div>
                <div class="row mb-2">
                    <div class="col">Sous-total:</div>
                    <div class="col text-end">{{ number_format($order->subtotal) }} F</div>
                </div>
                <div class="row mb-2">
                    <div class="col">Livraison:</div>
                    <div class="col text-end">{{ number_format($order->delivery_fee) }} F</div>
                </div>
                <hr>
                <div class="row">
                    <div class="col"><strong>Total:</strong></div>
                    <div class="col text-end"><strong class="text-primary fs-5">{{ number_format($order->total) }} F</strong></div>
                </div>
            </div>
        </div>

        <!-- Historique -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Chronologie
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Commande créée</h6>
                            <small class="text-muted">{{ $order->created_at->format('d/m/Y à H:i') }}</small>
                        </div>
                    </div>
                    
                    @if($order->updated_at != $order->created_at)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Dernière modification</h6>
                            <small class="text-muted">{{ $order->updated_at->format('d/m/Y à H:i') }}</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Fonction améliorée pour la mise à jour de statut
function updateStatus(newStatus) {
    const statusLabels = {
        'nouvelle': 'Nouvelle',
        'en_cours_livraison': 'En cours de livraison',
        'livree': 'Livrée',
        'annulee': 'Annulée',
        'payee': 'Payée'
    };
    
    const currentStatus = '{{ $order->status }}';
    
    // Vérifier si la transition est autorisée
    const allowedTransitions = {
        'nouvelle': ['en_cours_livraison', 'annulee'],
        'en_cours_livraison': ['livree', 'nouvelle', 'annulee'],
        'livree': ['payee', 'en_cours_livraison'],
        'payee': [],
        'annulee': ['nouvelle']
    };
    
    if (!allowedTransitions[currentStatus].includes(newStatus)) {
        alert('Cette transition de statut n\'est pas autorisée');
        return;
    }
    
    const message = `Êtes-vous sûr de vouloir changer le statut de cette commande vers "${statusLabels[newStatus]}" ?`;
    
    if (confirm(message)) {
        // Afficher un indicateur de chargement
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(btn => btn.disabled = true);
        
        // Ajouter un spinner sur le bouton cliqué
        event.target.innerHTML = '<i class="bi bi-hourglass-split"></i> Mise à jour...';
        
        fetch(`/orders/{{ $order->id }}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                status: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Créer une notification de succès
                showNotification('success', data.message);
                
                // Recharger après un délai pour voir la notification
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showNotification('error', data.message || 'Une erreur est survenue');
                // Restaurer les boutons
                buttons.forEach(btn => btn.disabled = false);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('error', 'Une erreur de réseau est survenue');
            buttons.forEach(btn => btn.disabled = false);
        });
    }
}

// Fonction pour afficher des notifications toast
function showNotification(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
    
    const notification = document.createElement('div');
    notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        <i class="bi ${icon} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Supprimer automatiquement après 5 secondes
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

function confirmDelete(orderId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette commande ? Cette action est irréversible.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/orders/${orderId}`;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        form.appendChild(csrfInput);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Ajouter des raccourcis clavier pour les actions rapides
document.addEventListener('keydown', function(e) {
    // Alt + S pour marquer comme livré
    if (e.altKey && e.key === 's' && '{{ $order->status }}' === 'en_cours_livraison') {
        e.preventDefault();
        updateStatus('livree');
    }
    
    // Alt + P pour marquer comme payé
    if (e.altKey && e.key === 'p' && '{{ $order->status }}' === 'livree') {
        e.preventDefault();
        updateStatus('payee');
    }
    
    // Alt + E pour éditer
    if (e.altKey && e.key === 'e') {
        e.preventDefault();
        @can('orders.edit')
        window.location.href = '{{ route('orders.edit', $order) }}';
        @endcan
    }
});

// Tooltip pour les raccourcis clavier
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter des tooltips Bootstrap si disponible
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});
</script>
@endpushstyles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 5px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -31px;
    top: 15px;
    width: 2px;
    height: calc(100% + 5px);
    background-color: #dee2e6;
}

.status-badge.bg-warning {
    color: #000 !important;
}

@media print {
    .btn-group,
    .breadcrumb,
    .card:last-child {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .card-header {
        background-color: transparent !important;
        border-bottom: 1px solid #dee2e6 !important;
    }
}
</style>
@endpush