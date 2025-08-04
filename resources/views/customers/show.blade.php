@extends('layouts.app')

@section('title', 'Détail client')
@section('page-title', 'Détail du client')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Informations du client</h5>
                <div>
                    @can('customers.edit')
                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning btn-sm me-1">
                        <i class="bi bi-pencil"></i> Modifier
                    </a>
                    @endcan
                    @can('customers.delete')
                    <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce client ?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i> Supprimer
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Nom</dt>
                    <dd class="col-sm-8">{{ $customer->name }}</dd>

                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">{{ $customer->email }}</dd>

                    <dt class="col-sm-4">Téléphone</dt>
                    <dd class="col-sm-8">{{ $customer->phone }}</dd>

                    <dt class="col-sm-4">Date d'inscription</dt>
                    <dd class="col-sm-8">{{ $customer->created_at->format('d/m/Y H:i') }}</dd>
                </dl>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary">Retour à la liste</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection