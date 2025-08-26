{{-- resources/views/orders/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des commandes')
@section('page-title', 'Gestion des commandes')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <!-- Filtres -->
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Statut</label>
                <select name="status" class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="nouvelle" {{ request('status') == 'nouvelle' ? 'selected' : '' }}>Nouvelle</option>
                    <option value="en_cours_livraison" {{ request('status') == 'en_cours_livraison' ? 'selected' : '' }}>En cours</option>
                    <option value="livree" {{ request('status') == 'livree' ? 'selected' : '' }}>Livrée</option>
                    <option value="annulee" {{ request('status') == 'annulee' ? 'selected' : '' }}>Annulée</option>
                    <option value="payee" {{ request('status') == 'payee' ? 'selected' : '' }}>Payée</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Zone</label>
                <select name="zone" class="form-select">
                    <option value="">Toutes les zones</option>
                    @foreach($deliveryZones as $zone)
                    <option value="{{ $zone->id }}" {{ request('zone') == $zone->id ? 'selected' : '' }}>
                        {{ $zone->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Rechercher</label>
                <input type="text" name="search" class="form-control" placeholder="Nom client, numéro de commande..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Rechercher
                </button>
            </div>
        </form>
    </div>
    <div class="col-md-4 text-end">
        @can('statistics.export')
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
            <i class="bi bi-download me-2"></i> Exporter
        </button>
        @endcan
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        @if($orders->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>N° Commande</th>
                        <th>Client</th>
                        <th>Zone</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr class="{{ $order->status == 'nouvelle' ? 'table-primary' : ($order->status == 'en_cours_livraison' ? 'table-warning' : ($order->status == 'livree' ? 'table-success' : ($order->status == 'annulee' ? 'table-danger' : ''))) }}">
                        <td><strong>{{ $order->order_number }}</strong></td>
                        <td>
                            <div>{{ $order->customer->name }}</div>
                            <small class="text-muted">{{ $order->customer->phone }}</small>
                        </td>
                        <td>{{ $order->deliveryZone->name }}</td>
                        <td><strong>{{ number_format($order->total) }} F</strong></td>
                        <td>
                            @php
                                $badgeColor = match($order->status) {
                                    'nouvelle' => 'primary',
                                    'en_cours_livraison' => 'warning text-dark',
                                    'livree' => 'success',
                                    'annulee' => 'danger',
                                    'payee' => 'info text-dark',
                                    default => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeColor }}">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary" title="Voir">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @can('orders.edit')
                                <a href="{{ route('orders.edit', $order) }}" class="btn btn-outline-warning" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endcan
                                @can('orders.delete')
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete('{{ $order->id }}')" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center p-3">
            {{ $orders->appends(request()->query())->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-cart-x text-muted" style="font-size: 4rem;"></i>
            <h4 class="text-muted mt-3">Aucune commande trouvée</h4>
            <p class="text-muted">Aucune commande ne correspond à vos critères de recherche.</p>
        </div>
        @endif
    </div>
</div>

<!-- Export Modal -->
@can('statistics.export')
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('statistics.export') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Exporter les commandes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Date de début</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date de fin</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Statut (optionnel)</label>
                        <select name="status" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="nouvelle">Nouvelle</option>
                            <option value="en_cours_livraison">En cours</option>
                            <option value="livree">Livrée</option>
                            <option value="annulee">Annulée</option>
                            <option value="payee">Payée</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-download me-2"></i> Exporter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection

@push('scripts')
<script>
function confirmDelete(orderId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette commande ?')) {
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

document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    const thirtyDaysAgo = new Date(Date.now() - 30*24*60*60*1000).toISOString().split('T')[0];

    const startDateInput = document.querySelector('input[name="start_date"]');
    const endDateInput = document.querySelector('input[name="end_date"]');

    if(startDateInput && !startDateInput.value) startDateInput.value = thirtyDaysAgo;
    if(endDateInput && !endDateInput.value) endDateInput.value = today;
});
</script>
@endpush
