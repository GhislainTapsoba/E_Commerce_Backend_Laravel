{{-- resources/views/statistics/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Statistiques')
@section('page-title', 'Statistiques générales')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <!-- Filtre période -->
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Période</label>
                <select name="period" class="form-select">
                    <option value="7" {{ $period == 7 ? 'selected' : '' }}>7 derniers jours</option>
                    <option value="30" {{ $period == 30 ? 'selected' : '' }}>30 derniers jours</option>
                    <option value="90" {{ $period == 90 ? 'selected' : '' }}>90 derniers jours</option>
                    <option value="365" {{ $period == 365 ? 'selected' : '' }}>12 derniers mois</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-bar-chart me-1"></i> Voir
                </button>
            </div>
        </form>
    </div>
    <div class="col-md-4 text-end">
        @can('statistics.export')
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
            <i class="bi bi-download me-2"></i> Exporter
        </button>
        @endcan
    </div>
</div>

{{-- Cartes statistiques --}}
<div class="row mb-4">
    @php
        $cards = [
            ['value' => $stats['orders_count'] ?? 0, 'label' => 'Commandes', 'color' => 'primary', 'icon' => 'bi-cart-check'],
            ['value' => number_format($stats['orders_total'] ?? 0), 'label' => 'Total ventes', 'color' => 'success', 'icon' => 'bi-currency-dollar'],
            ['value' => $stats['customers_count'] ?? 0, 'label' => 'Nouveaux clients', 'color' => 'info', 'icon' => 'bi-people'],
            ['value' => $stats['zones_count'] ?? 0, 'label' => 'Zones actives', 'color' => 'warning', 'icon' => 'bi-geo-alt']
        ];
    @endphp
    @foreach($cards as $card)
    <div class="col-md-3 mb-3">
        <div class="card text-center shadow-sm border-0">
            <div class="card-body">
                <i class="bi {{ $card['icon'] }} fs-2 text-{{ $card['color'] }}"></i>
                <h4 class="mt-2">{{ $card['value'] }}</h4>
                <small class="text-muted">{{ $card['label'] }}</small>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Graphique évolution des commandes --}}
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <h5 class="mb-3">📈 Évolution des commandes</h5>
        <canvas id="ordersChart" height="80"></canvas>
    </div>
</div>

{{-- Top zones --}}
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="mb-3">🌍 Top zones de livraison</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Zone</th>
                        <th>Commandes</th>
                        <th>Total ventes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topZones as $zone)
                    <tr>
                        <td>{{ $zone->name }}</td>
                        <td><span class="badge bg-primary">{{ $zone->orders_count }}</span></td>
                        <td><strong>{{ number_format($zone->orders_total) }} F</strong></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">Aucune donnée</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Export Modal --}}
@can('statistics.export')
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('statistics.export') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Exporter les commandes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Date de début</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date de fin</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Statut (optionnel)</label>
                        <select name="status" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="nouvelle">Nouvelle</option>
                            <option value="en_cours_livraison">En cours</option>
                            <option value="livree">Livrée</option>
                            <option value="annulee">Annulée</option>
                            <option value="payee">Payée</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-download me-2"></i> Exporter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('ordersChart').getContext('2d');
    const chartData = @json($chartData['data'] ?? []);
    const chartLabels = @json($chartData['labels'] ?? []);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Commandes',
                data: chartData,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13,110,253,0.15)',
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#0d6efd'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Auto-set export dates
    const today = new Date().toISOString().split('T')[0];
    const thirtyDaysAgo = new Date(Date.now() - 30*24*60*60*1000).toISOString().split('T')[0];
    const startDateInput = document.querySelector('input[name="start_date"]');
    const endDateInput = document.querySelector('input[name="end_date"]');
    if(startDateInput && !startDateInput.value) startDateInput.value = thirtyDaysAgo;
    if(endDateInput && !endDateInput.value) endDateInput.value = today;
});
</script>
@endpush
