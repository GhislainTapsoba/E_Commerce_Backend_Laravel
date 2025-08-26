{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')
<div class="row g-4">
    <!-- Stats Cards -->
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-start border-primary border-4 hover-shadow">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-cart-check-fill text-primary" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <div class="text-uppercase text-muted fw-bold">Commandes totales</div>
                    <div class="h4 fw-bold text-primary">{{ number_format($stats['total_orders']) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-start border-warning border-4 hover-shadow">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-clock-history text-warning" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <div class="text-uppercase text-muted fw-bold">En attente</div>
                    <div class="h4 fw-bold text-warning">{{ number_format($stats['pending_orders']) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-start border-success border-4 hover-shadow">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <div class="text-uppercase text-muted fw-bold">Livrées</div>
                    <div class="h4 fw-bold text-success">{{ number_format($stats['delivered_orders']) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-start border-info border-4 hover-shadow">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-people-fill text-info" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <div class="text-uppercase text-muted fw-bold">Clients</div>
                    <div class="h4 fw-bold text-info">{{ number_format($stats['total_customers']) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- Recent Orders -->
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Commandes récentes</h5>
                @can('orders.view')
                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
                @endcan
            </div>
            <div class="card-body p-0">
                @if($stats['recent_orders']->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>N° Commande</th>
                                <th>Client</th>
                                <th>Zone</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['recent_orders'] as $order)
                            <tr>
                                <td><strong>{{ $order->order_number }}</strong></td>
                                <td>{{ $order->customer->name }}</td>
                                <td>{{ $order->deliveryZone->name }}</td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @php
                                        $statusClass = match($order->status) {
                                            'nouvelle' => 'bg-primary',
                                            'en_cours_livraison' => 'bg-warning text-dark',
                                            'livree' => 'bg-success',
                                            'annulee' => 'bg-danger',
                                            'payee' => 'bg-info text-dark',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-cart-x text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">Aucune commande récente</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions & Active Zones -->
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-lightning-fill me-2"></i>Actions rapides</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @can('orders.view')
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-cart-check me-2"></i> Gérer les commandes
                    </a>
                    @endcan
                    @can('customers.create')
                    <a href="{{ route('customers.create') }}" class="btn btn-outline-success">
                        <i class="bi bi-person-plus me-2"></i> Ajouter un client
                    </a>
                    @endcan
                    @can('delivery-zones.view')
                    <a href="{{ route('delivery-zones.index') }}" class="btn btn-outline-info">
                        <i class="bi bi-geo-alt me-2"></i> Zones de livraison
                    </a>
                    @endcan
                    @can('statistics.view')
                    <a href="{{ route('statistics.index') }}" class="btn btn-outline-warning">
                        <i class="bi bi-graph-up me-2"></i> Voir les statistiques
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-geo-alt-fill me-2"></i>Zones actives</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="fw-bold">{{ $stats['active_zones'] }} zones actives</div>
                    @can('delivery-zones.view')
                    <a href="{{ route('delivery-zones.index') }}" class="btn btn-sm btn-outline-secondary">
                        Gérer
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        transform: translateY(-2px);
        transition: all 0.3s ease-in-out;
    }
</style>
@endpush
@endsection
