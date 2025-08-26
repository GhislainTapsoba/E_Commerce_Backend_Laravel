{{-- resources/views/deliveryZones/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Zone de livraison - ' . $deliveryZone->name)
@section('page-title', 'Zone de livraison - ' . $deliveryZone->name)

@section('content')
@include('components.notifications')

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('delivery-zones.index') }}">Zones de livraison</a></li>
                    <li class="breadcrumb-item active">{{ $deliveryZone->name }}</li>
                </ol>
            </nav>
            <div class="btn-group">
                @can('deliveryZones.edit')
                <a href="{{ route('delivery-zones.edit', $deliveryZone) }}" class="btn btn-outline-warning">
                    <i class="bi bi-pencil me-2"></i>
                    Modifier
                </a>
                @endcan
                @can('delivery-zones.delete')
                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete('{{ $deliveryZone->id }}')">
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
        <div class="card mb-4">
            <div class="card-body">
                <h3 class="mb-0">{{ $deliveryZone->name }}</h3>
                <p class="text-muted mb-0">
                    Créée le {{ $deliveryZone->created_at->format('d/m/Y à H:i') }}
                    @if($deliveryZone->updated_at != $deliveryZone->created_at)
                        | Dernière modification le {{ $deliveryZone->updated_at->format('d/m/Y à H:i') }}
                    @endif
                </p>
            </div>
        </div>

        <!-- Détails de la zone -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-geo-alt me-2"></i>
                    Détails
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Nom:</strong> {{ $deliveryZone->name }}
                </div>
                <div class="mb-3">
                    <strong>Frais de livraison:</strong> {{ number_format($deliveryZone->delivery_fee) }} F
                </div>
                @if($deliveryZone->description)
                <div class="mb-3">
                    <strong>Description:</strong><br>
                    <span class="text-muted">{{ $deliveryZone->description }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Liste des commandes associées -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-list-check me-2"></i>
                    Commandes associées ({{ $deliveryZone->orders->count() }})
                </h5>
            </div>
            <div class="card-body">
                @if($deliveryZone->orders->count() > 0)
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
                                @foreach($deliveryZone->orders as $order)
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
                    <p class="text-muted mb-0">
                        <i class="bi bi-info-circle"></i> Aucune commande associée
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Récapitulatif
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col">Nombre de commandes:</div>
                    <div class="col text-end"><strong>{{ $deliveryZone->orders->count() }}</strong></div>
                </div>
                <div class="row mb-2">
                    <div class="col">Frais de livraison:</div>
                    <div class="col text-end">{{ number_format($deliveryZone->delivery_fee) }} F</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(zoneId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette zone de livraison ? Cette action est irréversible.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/deliveryZones/${zoneId}`;
        
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
