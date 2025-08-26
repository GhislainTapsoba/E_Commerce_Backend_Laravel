{{-- resources/views/home.blade.php --}}
@extends('layouts.app')

@php $hideSidebar = true; @endphp

@section('title', 'Accueil')
@section('page-title', 'Accueil')

@section('content')
<div class="row justify-content-center g-4 mt-5">
    <div class="col-md-8">
        <div class="card shadow-sm border-start border-primary border-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-house-door me-2"></i>Bienvenue sur votre espace administrateur</h5>
                <span class="badge bg-primary">Connecté</span>
            </div>
            <div class="card-body">
                @if (session('status'))
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <div>{{ session('status') }}</div>
                </div>
                @endif

                <div class="text-center py-4">
                    <i class="bi bi-person-circle text-primary" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">Vous êtes connecté !</h4>
                    <p class="text-muted">Bienvenue dans votre espace administrateur. Gérez vos utilisateurs, commandes et statistiques facilement.</p>
                </div>

                <div class="d-grid gap-3 mt-4">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-people me-2"></i> Gérer les utilisateurs
                    </a>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-success">
                        <i class="bi bi-cart-check me-2"></i> Gérer les commandes
                    </a>
                    <a href="{{ route('statistics.index') }}" class="btn btn-outline-warning">
                        <i class="bi bi-graph-up me-2"></i> Voir les statistiques
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border-radius: 0.75rem;
    }
    .card-header {
        font-weight: 600;
    }
    .btn-outline-primary, .btn-outline-success, .btn-outline-warning {
        font-weight: 500;
        border-width: 2px;
    }
</style>
@endpush
@endsection
