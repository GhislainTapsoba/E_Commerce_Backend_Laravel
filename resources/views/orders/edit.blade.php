{{-- resources/views/orders/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier la commande #' . $order->order_number)
@section('page-title', 'Modifier la commande #' . $order->order_number)

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Commandes</a></li>
                    <li class="breadcrumb-item active">Modifier #{{ $order->order_number }}</li>
                </ol>
            </nav>
            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary">
                <i class="bi bi-eye me-2"></i>
                Voir la commande
            </a>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('orders.update', $order) }}">
    @csrf
    @method('PUT')
    
    <div class="row">
        <!-- Informations principales -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Informations de la commande
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Statut de la commande</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="nouvelle" {{ $order->status == 'nouvelle' ? 'selected' : '' }}>Nouvelle</option>
                                    <option value="en_cours_livraison" {{ $order->status == 'en_cours_livraison' ? 'selected' : '' }}>En cours de livraison</option>
                                    <option value="livree" {{ $order->status == 'livree' ? 'selected' : '' }}>Livrée</option>
                                    <option value="annulee" {{ $order->status == 'annulee' ? 'selected' : '' }}>Annulée</option>
                                    <option value="payee" {{ $order->status == 'payee' ? 'selected' : '' }}>Payée</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Zone de livraison</label>
                                <select name="delivery_zone_id" class="form-select @error('delivery_zone_id') is-invalid @enderror" required>
                                    @foreach($deliveryZones as $zone)
                                    <option value="{{ $zone->id }}" {{ $order->delivery_zone_id == $zone->id ? 'selected' : '' }}>
                                        {{ $zone->name }} ({{ number_format($zone->delivery_fee) }} F)
                                    </option>
                                    @endforeach
                                </select>
                                @error('delivery_zone_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Adresse de livraison</label>
                                <textarea name="delivery_address" class="form-control @error('delivery_address') is-invalid @enderror" rows="3" required>{{ old('delivery_address', $order->delivery_address) }}</textarea>
                                @error('delivery_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Notes (optionnel)</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Notes internes ou instructions particulières...">{{ old('notes', $order->notes) }}</textarea>
                                @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Articles de la commande -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-cart me-2"></i>
                        Articles commandés
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Produit</th>
                                    <th style="width: 120px;">Quantité</th>
                                    <th style="width: 120px;">Prix unitaire</th>
                                    <th style="width: 120px;">Total</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="order-items">
                                @foreach($order->items as $index => $item)
                                <tr>
                                    <td>
                                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                        <input type="text" name="items[{{ $index }}][product_name]" 
                                               class="form-control" value="{{ $item->product_name }}" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $index }}][quantity]" 
                                               class="form-control quantity-input" value="{{ $item->quantity }}" 
                                               min="1" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $index }}][unit_price]" 
                                               class="form-control price-input" value="{{ $item->unit_price }}" 
                                               min="0" step="0.01" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $index }}][total_price]" 
                                               class="form-control total-input" value="{{ $item->total_price }}" 
                                               readonly>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <button type="button" id="add-item" class="btn btn-outline-primary">
                        <i class="bi bi-plus me-2"></i>
                        Ajouter un article
                    </button>
                </div>
            </div>
        </div>

        <!-- Résumé -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person me-2"></i>
                        Informations client
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>{{ $order->customer->name }}</strong><br>
                        <i class="bi bi-telephone me-1"></i> {{ $order->customer->phone }}<br>
                        @if($order->customer->email)
                        <i class="bi bi-envelope me-1"></i> {{ $order->customer->email }}
                        @endif
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calculator me-2"></i>
                        Résumé financier
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Sous-total:</span>
                        <strong id="subtotal">{{ number_format($order->subtotal) }} F</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Frais de livraison:</span>
                        <strong id="delivery-fee">{{ number_format($order->delivery_fee) }} F</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong id="total" class="text-primary">{{ number_format($order->total) }} F</strong>
                    </div>
                    
                    <input type="hidden" name="subtotal" id="subtotal-input" value="{{ $order->subtotal }}">
                    <input type="hidden" name="delivery_fee" id="delivery-fee-input" value="{{ $order->delivery_fee }}">
                    <input type="hidden" name="total" id="total-input" value="{{ $order->total }}">
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>
                            Mettre à jour la commande
                        </button>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-2"></i>
                            Annuler
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
let itemIndex = {{ $order->items ? $order->items->count() : 0 }};

// Ajouter un nouvel article
document.getElementById('add-item').addEventListener('click', function() {
    const tbody = document.getElementById('order-items');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <input type="text" name="items[${itemIndex}][product_name]" class="form-control" required>
        </td>
        <td>
            <input type="number" name="items[${itemIndex}][quantity]" class="form-control quantity-input" value="1" min="1" required>
        </td>
        <td>
            <input type="number" name="items[${itemIndex}][unit_price]" class="form-control price-input" value="0" min="0" step="0.01" required>
        </td>
        <td>
            <input type="number" name="items[${itemIndex}][total_price]" class="form-control total-input" value="0" readonly>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    itemIndex++;
    
    // Ajouter les événements aux nouveaux éléments
    addItemEvents(row);
});

// Supprimer un article
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
        const row = e.target.closest('tr');
        row.remove();
        calculateTotals();
    }
});

// Ajouter les événements à une ligne
function addItemEvents(row) {
    const quantityInput = row.querySelector('.quantity-input');
    const priceInput = row.querySelector('.price-input');
    
    quantityInput.addEventListener('input', function() {
        calculateItemTotal(row);
        calculateTotals();
    });
    
    priceInput.addEventListener('input', function() {
        calculateItemTotal(row);
        calculateTotals();
    });
}

// Calculer le total d'un article
function calculateItemTotal(row) {
    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    const total = quantity * price;
    
    row.querySelector('.total-input').value = total;
}

// Calculer les totaux généraux
function calculateTotals() {
    const totalInputs = document.querySelectorAll('.total-input');
    let subtotal = 0;
    
    totalInputs.forEach(input => {
        subtotal += parseFloat(input.value) || 0;
    });
    
    const deliveryFee = parseFloat(document.querySelector('select[name="delivery_zone_id"] option:checked').textContent.match(/\((\d+(?:\.\d+)?)/)?.[1]) || 0;
    const total = subtotal + deliveryFee;
    
    // Mettre à jour l'affichage
    document.getElementById('subtotal').textContent = new Intl.NumberFormat('fr-FR').format(subtotal) + ' F';
    document.getElementById('delivery-fee').textContent = new Intl.NumberFormat('fr-FR').format(deliveryFee) + ' F';
    document.getElementById('total').textContent = new Intl.NumberFormat('fr-FR').format(total) + ' F';
    
    // Mettre à jour les champs cachés
    document.getElementById('subtotal-input').value = subtotal;
    document.getElementById('delivery-fee-input').value = deliveryFee;
    document.getElementById('total-input').value = total;
}

// Ajouter les événements aux éléments existants
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#order-items tr').forEach(row => {
        addItemEvents(row);
    });
    
    // Événement pour le changement de zone de livraison
    document.querySelector('select[name="delivery_zone_id"]').addEventListener('change', function() {
        calculateTotals();
    });
});
</script>
@endpush