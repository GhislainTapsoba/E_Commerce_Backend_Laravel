{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.auth')

@section('title', 'Connexion')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="auth-card">
                <div class="row g-0">
                    <!-- Section Logo/Branding -->
                    <div class="col-md-6">
                        <div class="logo-container h-100 d-flex flex-column justify-content-center">
                            <div class="mb-4">
                                <img src="{{ asset('images/ecms.png') }}" 
                                     alt="Logo ECMS" 
                                     class="img-fluid">
                            </div>
                            <h2 class="fw-bold mb-3">{{ config('app.name', 'ECMS') }}</h2>
                            <p class="mb-0 opacity-75 lead">
                                Système de gestion e-commerce
                            </p>
                            <p class="small opacity-50 mt-2">
                                Connectez-vous pour accéder à votre tableau de bord
                            </p>
                        </div>
                    </div>

                    <!-- Section Formulaire -->
                    <div class="col-md-6">
                        <div class="form-container">
                            <div class="text-center mb-4">
                                <h3 class="fw-bold text-dark mb-2">Connexion</h3>
                                <p class="text-muted">Entrez vos identifiants pour continuer</p>
                            </div>

                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                {{-- Email --}}
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold text-dark">
                                        <i class="bi bi-envelope me-2"></i>Adresse Email
                                    </label>
                                    <input id="email" 
                                           type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           required 
                                           autofocus 
                                           autocomplete="email"
                                           placeholder="exemple@email.com">
                                    @error('email')
                                        <div class="invalid-feedback">
                                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- Mot de passe --}}
                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold text-dark">
                                        <i class="bi bi-lock me-2"></i>Mot de passe
                                    </label>
                                    <div class="position-relative">
                                        <input id="password" 
                                               type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               name="password" 
                                               required
                                               placeholder="••••••••">
                                        <button type="button" 
                                                class="btn position-absolute end-0 top-50 translate-middle-y border-0 bg-transparent"
                                                onclick="togglePassword()"
                                                style="z-index: 10;">
                                            <i id="password-icon" class="bi bi-eye text-muted"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">
                                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- Options --}}
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               class="form-check-input" 
                                               name="remember" 
                                               id="remember" 
                                               {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label text-muted" for="remember">
                                            Se rappeler de moi
                                        </label>
                                    </div>

                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-link small">
                                            Mot de passe oublié ?
                                        </a>
                                    @endif
                                </div>

                                {{-- Bouton de connexion --}}
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>
                                        Se connecter
                                    </button>
                                </div>
                            </form>

                            {{-- Liens supplémentaires --}}
                            @if (Route::has('register'))
                                <div class="text-center mt-4">
                                    <p class="text-muted mb-0">
                                        Pas encore de compte ? 
                                        <a href="{{ route('register') }}" class="text-link">
                                            Créer un compte
                                        </a>
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('password-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordIcon.classList.remove('bi-eye');
        passwordIcon.classList.add('bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        passwordIcon.classList.remove('bi-eye-slash');
        passwordIcon.classList.add('bi-eye');
    }
}
</script>
@endsection