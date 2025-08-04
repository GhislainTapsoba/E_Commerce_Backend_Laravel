@extends('layouts.app')

@section('title', 'Gestion des clients')
@section('page-title', 'Gestion des clients')

@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Rechercher un client..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
    <div class="col-md-4 text-end">
        @can('customers.create')
        <a href="{{ route('customers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Nouveau client
        </a>
        @endcan
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($customers->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Date d'inscription</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                    <tr>
                        <td>{{ $loop->iteration + ($customers->currentPage()-1)*$customers->perPage() }}</td>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ $customer->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                @can('customers.view')
                                <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-primary" title="Voir">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @endcan
                                @can('customers.edit')
                                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-warning" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endcan
                                @can('customers.delete')
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete('{{ $customer->id }}')">
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
        <div class="d-flex justify-content-center">
            {{ $customers->appends(request()->query())->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
            <h4 class="text-muted mt-3">Aucun client trouvé</h4>
            <p class="text-muted">Aucun client ne correspond à vos critères de recherche.</p>
        </div>
        @endif
    </div>
</div>

<form id="delete-form" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
function confirmDelete(customerId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce client ?')) {
        const form = document.getElementById('delete-form');
        form.action = `/customers/${customerId}`;
        form.submit();
    }
}
</script>
@endpush