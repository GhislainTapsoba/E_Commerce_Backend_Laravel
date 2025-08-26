{{-- resources/views/customers/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier client')
@section('page-title', 'Modifier client')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Modifier les informations du client</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('customers.update', $customer) }}">
                    @csrf
                    @method('PUT')

                    {{-- Nom --}}
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom</label>
                        <input type="text" id="name" name="name" 
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $customer->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse email</label>
                        <input type="email" id="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $customer->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Téléphone --}}
                    <div class="mb-3">
                        <label for="phone" class="form-label">Téléphone</label>
                        <input type="text" id="phone" name="phone"
                               class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone', $customer->phone) }}" required>
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Adresse --}}
                    <div class="mb-3">
                        <label for="address" class="form-label">Adresse</label>
                        <input type="text" id="address" name="address"
                               class="form-control @error('address') is-invalid @enderror"
                               value="{{ old('address', $customer->address) }}" required>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Zone de livraison --}}
                    <div class="mb-3">
                        <label for="delivery_zone_id" class="form-label">Zone de livraison</label>
                        <select id="delivery_zone_id" name="delivery_zone_id"
                                class="form-select @error('delivery_zone_id') is-invalid @enderror" required>
                            <option value="">Sélectionner une zone</option>
                            @foreach($deliveryZones as $zone)
                                <option value="{{ $zone->id }}" 
                                    {{ old('delivery_zone_id', $customer->delivery_zone_id) == $zone->id ? 'selected' : '' }}>
                                    {{ $zone->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('delivery_zone_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Boutons --}}
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary me-2">Annuler</a>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
