{{-- resources/views/orders/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier la commande #' . $order->order_number)
@section('page-title', 'Modifier la commande #' . $order->order_number)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Modifier la commande : #{{ $order->order_number }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('orders.update', $order) }}">
                    @csrf
                    @method('PUT')

                    {{-- Statut et zone de livraison --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Statut de la commande</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="nouvelle" {{ $order->status == 'nouvelle' ? 'selected' : '' }}>Nouvelle</option>
                                <option value="en_cours_livraison" {{ $order->status == 'en_cours_livraison' ? 'selected' : '' }}>En cours de livraison</option>
                                <option value="livree" {{ $order->status == 'livree' ? 'selected' : '' }}>Livrée</option>
                                <option value="annulee" {{ $order->status == 'annulee' ? 'selected' : '' }}>Annulée</option>
                                <option value="payee" {{ $order->status == 'payee' ? 'selected' : '' }}>Payée</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Zone de livraison</label>
                            <select name="delivery_zone_id" class="form-select @error('delivery_zone_id') is-invalid @enderror" required>
                                @foreach($deliveryZones as $zone)
                                    <option value="{{ $zone->id }}" {{ $order->delivery_zone_id == $zone->id ? 'selected' : '' }}>
                                        {{ $zone->name }} ({{ number_format($zone->delivery_fee) }} F)
                                    </option>
                                @endforeach
                            </select>
                            @error('delivery_zone_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Adresse et notes --}}
                    <div class="mb-3">
                        <label class="form-label">Adresse de livraison</label>
                        <textarea name="delivery_address" class="form-control @error('delivery_address') is-invalid @enderror" rows="3" required>{{ old('delivery_address', $order->delivery_address) }}</textarea>
                        @error('delivery_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes (optionnel)</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Notes internes ou instructions particulières...">{{ old('notes', $order->notes) }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Articles commandés --}}
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Articles commandés</h6>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produit</th>
                                        <th>Quantité</th>
                                        <th>Prix unitaire</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="order-items">
                                    @foreach($order->items as $index => $item)
                                    <tr>
                                        <td>
                                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                            <input type="text" name="items[{{ $index }}][product_name]" class="form-control" value="{{ $item->product_name }}" required>
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $index }}][quantity]" class="form-control quantity-input" value="{{ $item->quantity }}" min="1" required>
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $index }}][unit_price]" class="form-control price-input" value="{{ $item->unit_price }}" min="0" step="0.01" required>
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $index }}][total_price]" class="form-control total-input" value="{{ $item->total_price }}" readonly>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="p-3">
                                <button type="button" id="add-item" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-plus me-1"></i> Ajouter un article
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Résumé financier --}}
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Résumé financier</h6>
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

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary me-2">Annuler</a>
                        <button type="submit" class="btn btn-primary">Mettre à jour la commande</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let itemIndex = {{ $order->items ? $order->items->count() : 0 }};

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

function calculateItemTotal(row) {
    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    const total = quantity * price;
    row.querySelector('.total-input').value = total;
}

function calculateTotals() {
    const totalInputs = document.querySelectorAll('.total-input');
    let subtotal = 0;
    totalInputs.forEach(input => { subtotal += parseFloat(input.value) || 0; });

    const deliveryFee = parseFloat(document.querySelector('select[name="delivery_zone_id"] option:checked').textContent.match(/\((\d+(?:\.\d+)?)/)?.[1]) || 0;
    const total = subtotal + deliveryFee;

    document.getElementById('subtotal').textContent = new Intl.NumberFormat('fr-FR').format(subtotal) + ' F';
    document.getElementById('delivery-fee').textContent = new Intl.NumberFormat('fr-FR').format(deliveryFee) + ' F';
    document.getElementById('total').textContent = new Intl.NumberFormat('fr-FR').format(total) + ' F';

    document.getElementById('subtotal-input').value = subtotal;
    document.getElementById('delivery-fee-input').value = deliveryFee;
    document.getElementById('total-input').value = total;
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#order-items tr').forEach(row => addItemEvents(row));
    document.querySelector('select[name="delivery_zone_id"]').addEventListener('change', calculateTotals);

    document.getElementById('add-item').addEventListener('click', function() {
        const tbody = document.getElementById('order-items');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="text" name="items[${itemIndex}][product_name]" class="form-control" required></td>
            <td><input type="number" name="items[${itemIndex}][quantity]" class="form-control quantity-input" value="1" min="1" required></td>
            <td><input type="number" name="items[${itemIndex}][unit_price]" class="form-control price-input" value="0" min="0" step="0.01" required></td>
            <td><input type="number" name="items[${itemIndex}][total_price]" class="form-control total-input" value="0" readonly></td>
            <td><button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="bi bi-trash"></i></button></td>
        `;
        tbody.appendChild(row);
        addItemEvents(row);
        itemIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
            e.target.closest('tr').remove();
            calculateTotals();
        }
    });
});
</script>
@endpush
