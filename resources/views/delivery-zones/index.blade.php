{{-- resources/views/delivery-zones/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Zones de livraison')
@section('page-title', 'Zones de livraison')

@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <!-- Stats -->
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h4>{{ $zones->where('is_active', true)->count() }}</h4>
                        <small>Zones actives</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 text-end">
        @can('delivery-zones.create')
        <a href="{{ route('delivery-zones.create') }}" class="btn btn-primary">
            <i class="bi bi-geo-alt-fill me-2"></i>
            Nouvelle zone
        </a>
        @endcan
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($zones->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Frais de livraison</th>
                        <th>Temps de livraison</th>
                        <th>Clients</th>
                        <th>Commandes</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($zones as $zone)
                    <tr>
                        <td><strong>{{ $zone->name }}</strong></td>
                        <td>{{ Str::limit($zone->description, 50) }}</td>
                        <td><strong>{{ number_format($zone->delivery_fee) }} F</strong></td>
                        <td>
                            @if($zone->delivery_time_min && $zone->delivery_time_max)
                            {{ $zone->delivery_time_min }}-{{ $zone->delivery_time_max }} min
                            @else
                            Non défini
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $zone->customers_count }}</span>
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $zone->orders_count }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $zone->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $zone->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('delivery-zones.show', $zone) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @can('delivery-zones.edit')
                                <a href="{{ route('delivery-zones.edit', $zone) }}" class="btn btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endcan
                                @can('delivery-zones.delete')
                                @if($zone->orders_count == 0)
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete('{{ $zone->id }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $zones->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-geo-alt text-muted" style="font-size: 4rem;"></i>
            <h4 class="text-muted mt-3">Aucune zone de livraison</h4>
            <p class="text-muted">Commencez par créer votre première zone de livraison.</p>
            @can('delivery-zones.create')
            <a href="{{ route('delivery-zones.create') }}" class="btn btn-primary">
                <i class="bi bi-geo-alt-fill me-2"></i>
                Créer une zone
            </a>
            @endcan
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(zoneId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette zone de livraison ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/delivery-zones/${zoneId}`;
        
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