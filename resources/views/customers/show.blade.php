@extends('layouts.app')

@section('title', 'Client #' . $customer->id)
@section('page-title', 'Client #' . $customer->id)

@section('content')
@include('components.notifications')

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Clients</a></li>
                    <li class="breadcrumb-item active">#{{ $customer->id }}</li>
                </ol>
            </nav>
            <div class="btn-group">
                @can('customers.edit')
                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-warning">
                    <i class="bi bi-pencil me-2"></i>
                    Modifier
                </a>
                @endcan
                @can('customers.delete')
                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete('{{ $customer->id }}')">
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
        <!-- Carte informations client -->
        <div class="card mb-4">
            <div class="card-body">
                <h3 class="mb-0">{{ $customer->name }}</h3>
                <p class="text-muted mb-0">
                    Inscrit le {{ $customer->created_at->format('d/m/Y à H:i') }}
                </p>
            </div>
        </div>

        <!-- Coordonnées -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person-lines-fill me-2"></i>
                    Coordonnées
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <strong>Email:</strong>
                    <a href="mailto:{{ $customer->email }}" class="text-decoration-none">{{ $customer->email }}</a>
                </div>
                <div>
                    <strong>Téléphone:</strong>
                    <a href="tel:{{ $customer->phone }}" class="text-decoration-none">{{ $customer->phone }}</a>
                </div>
            </div>
        </div>

        <!-- Commandes du client -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-cart me-2"></i>
                    Commandes ({{ $customer->orders->count() }})
                </h5>
            </div>
            <div class="card-body">
                @if($customer->orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Statut</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customer->orders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                <td>{{ number_format($order->total) }} F</td>
                                <td>
                                    <span class="badge status-badge {{ $order->status_badge }}">
                                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                        Voir
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <p class="text-muted mb-0"><i class="bi bi-info-circle"></i> Aucune commande trouvée</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Récapitulatif -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-calculator me-2"></i>
                    Récapitulatif
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col">Total commandes:</div>
                    <div class="col text-end"><strong>{{ $customer->orders->count() }}</strong></div>
                </div>
                <div class="row mb-2">
                    <div class="col">Montant total dépensé:</div>
                    <div class="col text-end"><strong>{{ number_format($customer->orders->sum('total')) }} F</strong></div>
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
                            <h6 class="mb-1">Client créé</h6>
                            <small class="text-muted">{{ $customer->created_at->format('d/m/Y à H:i') }}</small>
                        </div>
                    </div>
                    
                    @if($customer->updated_at != $customer->created_at)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Dernière modification</h6>
                            <small class="text-muted">{{ $customer->updated_at->format('d/m/Y à H:i') }}</small>
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
function confirmDelete(customerId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce client ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/customers/${customerId}`;
        
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
</script>
@endpush

@push('styles')
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
</style>
@endpush
