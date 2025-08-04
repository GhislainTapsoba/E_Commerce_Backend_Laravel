@extends('layouts.app')

@section('title', 'Détail zone de livraison')
@section('page-title', 'Détail zone de livraison')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Zone : {{ $deliveryZone->name }}</h5>
                <div>
                    @can('delivery-zones.edit')
                    <a href="{{ route('delivery-zones.edit', $deliveryZone) }}" class="btn btn-warning btn-sm me-1">
                        <i class="bi bi-pencil"></i> Modifier
                    </a>
                    @endcan
                    @can('delivery-zones.delete')
                    @if($deliveryZone->orders_count == 0)
                    <form action="{{ route('delivery-zones.destroy', $deliveryZone) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette zone ?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i> Supprimer
                        </button>
                    </form>
                    @endif
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Nom</dt>
                    <dd class="col-sm-8">{{ $deliveryZone->name }}</dd>

                    <dt class="col-sm-4">Description</dt>
                    <dd class="col-sm-8">{{ $deliveryZone->description }}</dd>

                    <dt class="col-sm-4">Frais de livraison</dt>
                    <dd class="col-sm-8"><strong>{{ number_format($deliveryZone->delivery_fee) }} F</strong></dd>

                    <dt class="col-sm-4">Temps de livraison</dt>
                    <dd class="col-sm-8">
                        @if($deliveryZone->delivery_time_min && $deliveryZone->delivery_time_max)
                            {{ $deliveryZone->delivery_time_min }}-{{ $deliveryZone->delivery_time_max }} min
                        @else
                            Non défini
                        @endif
                    </dd>

                    <dt class="col-sm-4">Nombre de clients</dt>
                    <dd class="col-sm-8"><span class="badge bg-info">{{ $deliveryZone->customers_count }}</span></dd>

                    <dt class="col-sm-4">Nombre de commandes</dt>
                    <dd class="col-sm-8"><span class="badge bg-primary">{{ $deliveryZone->orders_count }}</span></dd>

                    <dt class="col-sm-4">Statut</dt>
                    <dd class="col-sm-8">
                        <span class="badge {{ $deliveryZone->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $deliveryZone->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </dd>
                </dl>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('delivery-zones.index') }}" class="btn btn-secondary">Retour à la liste</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection