{{-- resources/views/users/create.blade.php --}}

@extends('layouts.app')

@section('title', 'Créer un nouvel utilisateur')

@section('page-title', 'Créer un nouvel utilisateur')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-person-plus-fill me-2"></i>
                    Nouvel utilisateur
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('users.store') }}">
                    @csrf

                    {{-- Nom --}}
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Mot de passe --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               id="password" name="password" required>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Confirmation mot de passe --}}
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" class="form-control"
                               id="password_confirmation" name="password_confirmation" required>
                    </div>

                    {{-- Rôle --}}
                    <div class="mb-3">
                        <label for="role" class="form-label">Rôle</label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="">Sélectionner un rôle</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status actif --}}
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   name="is_active" value="1" id="is_active"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Utilisateur actif
                            </label>
                        </div>
                        @error('is_active')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Boutons --}}
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>
                            Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            Créer l'utilisateur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection