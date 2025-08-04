@extends('layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Gestion des notifications')

@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <select name="type" class="form-select">
                    <option value="">Tous les types</option>
                    <option value="order" {{ request('type') == 'order' ? 'selected' : '' }}>Commande</option>
                    <option value="customer" {{ request('type') == 'customer' ? 'selected' : '' }}>Client</option>
                    <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>Système</option>
                </select>
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Non lue</option>
                    <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Lue</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($notifications->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Type</th>
                        <th>Message</th>
                        <th>Commande liée</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notifications as $notification)
                    <tr>
                        <td>{{ $loop->iteration + ($notifications->currentPage()-1)*$notifications->perPage() }}</td>
                        <td>
                            @if($notification->type == 'order')
                                <span class="badge bg-primary">Commande</span>
                            @elseif($notification->type == 'customer')
                                <span class="badge bg-info">Client</span>
                            @else
                                <span class="badge bg-secondary">Système</span>
                            @endif
                        </td>
                        <td>{{ Str::limit($notification->message, 60) }}</td>
                        <td>
                            @if($notification->order)
                                <a href="{{ route('orders.show', $notification->order) }}">
                                    #{{ $notification->order->order_number }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $notification->status == 'unread' ? 'bg-warning' : 'bg-success' }}">
                                {{ $notification->status == 'unread' ? 'Non lue' : 'Lue' }}
                            </span>
                        </td>
                        <td>{{ $notification->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @can('notifications.delete')
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDelete('{{ $notification->id }}')">
                                <i class="bi bi-trash"></i>
                            </button>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">
            {{ $notifications->appends(request()->query())->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-bell-slash text-muted" style="font-size: 4rem;"></i>
            <h4 class="text-muted mt-3">Aucune notification trouvée</h4>
            <p class="text-muted">Aucune notification ne correspond à vos critères de recherche.</p>
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
function confirmDelete(notificationId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')) {
        const form = document.getElementById('delete-form');
        form.action = `/notifications/${notificationId}`;
        form.submit();
    }
}
</script>
@endpush