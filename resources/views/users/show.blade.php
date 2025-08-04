@extends('layouts.app')

@section('title', 'Détail utilisateur')
@section('page-title', 'Détail utilisateur')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Utilisateur : {{ $user->name }}</h5>
                <div>
                    @can('users.edit')
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm me-1">
                        <i class="bi bi-pencil"></i> Modifier
                    </a>
                    @endcan
                    @can('users.delete')
                    @if(!$user->hasRole('Super Administrateur') || \App\Models\User::role('Super Administrateur')->count() > 1)
                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cet utilisateur ?')">
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
                    <dd class="col-sm-8">{{ $user->name }}</dd>

                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">{{ $user->email }}</dd>

                    <dt class="col-sm-4">Rôle</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-info">{{ $user->getRoleNames()->first() }}</span>
                    </dd>

                    <dt class="col-sm-4">Statut</dt>
                    <dd class="col-sm-8">
                        <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $user->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </dd>

                    <dt class="col-sm-4">Créé le</dt>
                    <dd class="col-sm-8">{{ $user->created_at->format('d/m/Y H:i') }}</dd>
                </dl>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Retour à la liste</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection