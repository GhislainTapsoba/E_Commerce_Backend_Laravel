{{-- resources/views/customers/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nouveau client')
@section('page-title', 'Créer un client')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center" 
     style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body px-5 py-4">
                <form method="POST" action="{{ route('customers.store') }}">
                    @csrf

                    {{-- Nom --}}
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nom</label>
                        <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required placeholder="Ex: John Doe">
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Adresse email</label>
                        <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email') }}" required placeholder="ex: john.doe@example.com">
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Téléphone --}}
                    <div class="mb-3">
                        <label for="phone" class="form-label fw-semibold">Téléphone</label>
                        <input type="text" class="form-control form-control-lg @error('phone') is-invalid @enderror"
                               id="phone" name="phone" value="{{ old('phone') }}" required placeholder="Ex: +226 70 00 00 00">
                        @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Adresse --}}
                    <div class="mb-3">
                        <label for="address" class="form-label fw-semibold">Adresse</label>
                        <input type="text" class="form-control form-control-lg @error('address') is-invalid @enderror"
                               id="address" name="address" value="{{ old('address') }}" required placeholder="Ex: Ouagadougou, Burkina Faso">
                        @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Zone de livraison --}}
                    <div class="mb-4">
                        <label for="delivery_zone_id" class="form-label fw-semibold">Zone de livraison</label>
                        <select class="form-select form-select-lg @error('delivery_zone_id') is-invalid @enderror"
                                id="delivery_zone_id" name="delivery_zone_id" required>
                            <option value="">Sélectionner une zone</option>
                            @foreach($deliveryZones as $zone)
                                <option value="{{ $zone->id }}" {{ old('delivery_zone_id') == $zone->id ? 'selected' : '' }}>
                                    {{ $zone->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('delivery_zone_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Boutons --}}
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-lg rounded-pill">Annuler</a>
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm">Créer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
