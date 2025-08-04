@extends('layouts.app')

@section('title', 'Modifier la zone de livraison')
@section('page-title', 'Modifier la zone de livraison')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Modifier la zone : {{ $deliveryZone->name }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('delivery-zones.update', $deliveryZone) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $deliveryZone->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3">{{ old('description', $deliveryZone->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="delivery_fee" class="form-label">Frais de livraison (F CFA)</label>
                        <input type="number" class="form-control @error('delivery_fee') is-invalid @enderror"
                               id="delivery_fee" name="delivery_fee" value="{{ old('delivery_fee', $deliveryZone->delivery_fee) }}" min="0" step="100" required>
                        @error('delivery_fee')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="delivery_time_min" class="form-label">Temps min. de livraison (minutes)</label>
                            <input type="number" class="form-control @error('delivery_time_min') is-invalid @enderror"
                                   id="delivery_time_min" name="delivery_time_min" value="{{ old('delivery_time_min', $deliveryZone->delivery_time_min) }}" min="0">
                            @error('delivery_time_min')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="delivery_time_max" class="form-label">Temps max. de livraison (minutes)</label>
                            <input type="number" class="form-control @error('delivery_time_max') is-invalid @enderror"
                                   id="delivery_time_max" name="delivery_time_max" value="{{ old('delivery_time_max', $deliveryZone->delivery_time_max) }}" min="0">
                            @error('delivery_time_max')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                               {{ old('is_active', $deliveryZone->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Zone active
                        </label>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('delivery-zones.index') }}" class="btn btn-secondary me-2">Annuler</a>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection