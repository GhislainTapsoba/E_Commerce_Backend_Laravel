{{-- resources/views/auth/verify.blade.php --}}
@extends('layouts.app')

@section('title', 'Vérification de l\'email')
@section('page-title', 'Vérification de l\'email')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center" 
     style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header text-center bg-white border-bottom-0 py-3">
                <h4 class="mb-0 fw-bold text-primary">{{ __('Vérification de l\'email') }}</h4>
                <p class="text-muted small">Merci de vérifier votre adresse email</p>
            </div>

            <div class="card-body px-5 py-4 text-center">
                @if (session('resent'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ __('Un nouveau lien de vérification a été envoyé à votre adresse email.') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <p>{{ __('Avant de continuer, veuillez vérifier votre email pour le lien de vérification.') }}</p>
                <p>{{ __('Si vous n\'avez pas reçu l\'email') }} :</p>

                <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill shadow-sm">
                        {{ __('Cliquez ici pour en demander un autre') }}
                    </button>
                </form>
            </div>

            <div class="card-footer text-center bg-white border-top-0 py-3">
                <small class="text-muted">© {{ date('Y') }} ECMS. Tous droits réservés.</small>
            </div>
        </div>
    </div>
</div>
@endsection
