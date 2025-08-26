{{-- resources/views/notifications/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Gestion des notifications')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Category</label>
                <select name="category" class="form-select">
                    <option value="">Toutes</option>
                    <option value="order" {{ request('category') == 'order' ? 'selected' : '' }}>Commande</option>
                    <option value="customer" {{ request('category') == 'customer' ? 'selected' : '' }}>Client</option>
                    <option value="system" {{ request('category') == 'system' ? 'selected' : '' }}>Système</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Statut</label>
                <select name="status" class="form-select">
                    <option value="">Tous</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Envoyé</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Échoué</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Canal</label>
                <select name="type" class="form-select">
                    <option value="">Tous</option>
                    <option value="email" {{ request('type') == 'email' ? 'selected' : '' }}>Email</option>
                    <option value="sms" {{ request('type') == 'sms' ? 'selected' : '' }}>SMS</option>
                    <option value="whatsapp" {{ request('type') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Rechercher
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        @if($notifications->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Catégorie</th>
                        <th>Canal</th>
                        <th>Message</th>
                        <th>Commande liée</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notifications as $notification)
                    <tr class="{{ $notification->status == 'pending' ? 'table-warning' : ($notification->status == 'failed' ? 'table-danger' : '') }}">
                        <td>{{ $loop->iteration + ($notifications->currentPage()-1)*$notifications->perPage() }}</td>

                        {{-- Category --}}
                        <td>
                            @switch($notification->category)
                                @case('order')
                                    <span class="badge bg-primary">Commande</span>
                                    @break
                                @case('customer')
                                    <span class="badge bg-info text-dark">Client</span>
                                    @break
                                @case('system')
                                    <span class="badge bg-secondary">Système</span>
                                    @break
                            @endswitch
                        </td>

                        {{-- Type / Canal --}}
                        <td>
                            @switch($notification->type)
                                @case('email')
                                    <span class="badge bg-success">Email</span>
                                    @break
                                @case('sms')
                                    <span class="badge bg-warning text-dark">SMS</span>
                                    @break
                                @case('whatsapp')
                                    <span class="badge bg-info text-dark">WhatsApp</span>
                                    @break
                                @default
                                    <span class="badge bg-light text-dark">{{ $notification->type }}</span>
                            @endswitch
                        </td>

                        <td>{{ Str::limit($notification->message, 60) }}</td>
                        <td>
                            @if($notification->order)
                                <a href="{{ route('orders.show', $notification->order) }}" class="text-decoration-none">
                                    #{{ $notification->order->order_number }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        <td>
                            <span class="badge 
                                {{ $notification->status == 'pending' ? 'bg-warning text-dark' : '' }}
                                {{ $notification->status == 'sent' ? 'bg-success' : '' }}
                                {{ $notification->status == 'failed' ? 'bg-danger' : '' }}">
                                {{ ucfirst($notification->status) }}
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

        <div class="d-flex justify-content-center p-3">
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