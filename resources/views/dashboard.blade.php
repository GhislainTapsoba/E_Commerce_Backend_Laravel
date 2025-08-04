{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-primary border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-cart-check text-primary" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Commandes totales</div>
                        <div class="h5 mb-0 font-weight-bold">{{ number_format($stats['total_orders']) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-clock text-warning" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase mb-1">En attente</div>
                        <div class="h5 mb-0 font-weight-bold">{{ number_format($stats['pending_orders']) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Livrées</div>
                        <div class="h5 mb-0 font-weight-bold">{{ number_format($stats['delivered_orders']) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-info border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-people text-info" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Clients</div>
                        <div class="h5 mb-0 font-weight-bold">{{ number_format($stats['total_customers']) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Orders -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Commandes récentes</h5>
                @can('orders.view')
                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
                @endcan
            </div>
            <div class="card-body">
                @if($stats['recent_orders']->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>N° Commande</th>
                                <th>Client</th>
                                <th>Zone</th>
                                <th>Total</th>
                                <th>Statut</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['recent_orders'] as $order)
                            <tr>
                                <td><strong>{{ $order->order_number }}</strong></td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-cart-x text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">Aucune commande récente</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Actions rapides</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @can('orders.view')
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-cart-check me-2"></i>
                        Gérer les commandes
                    </a>
                    @endcan
                    
                    @can('customers.create')
                    <a href="{{ route('customers.create') }}" class="btn btn-outline-success">
                        <i class="bi bi-person-plus me-2"></i>
                        Ajouter un client
                    </a>
                    @endcan
                    
                    @can('delivery-zones.view')
                    <a href="{{ route('delivery-zones.index') }}" class="btn btn-outline-info">
                        <i class="bi bi-geo-alt me-2"></i>
                        Zones de livraison
                    </a>
                    @endcan
                    
                    @can('statistics.view')
                    <a href="{{ route('statistics.index') }}" class="btn btn-outline-warning">
                        <i class="bi bi-graph-up me-2"></i>
                        Voir les statistiques
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Active Zones -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Zones actives</h5>
            </div>
            <div class="card-body">
                <div class="small">
                    <strong>{{ $stats['active_zones'] }}</strong> zones de livraison actives
                </div>
                @can('delivery-zones.view')
                <a href="{{ route('delivery-zones.index') }}" class="btn btn-sm btn-outline-secondary mt-2">
                    Gérer les zones
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
