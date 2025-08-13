@component('mail::message')
<style>
  .reset-btn {
    background-color: #4f46e5; /* Indigo-600 */
    color: white !important;
    padding: 12px 24px;
    font-weight: 600;
    border-radius: 8px;
    text-decoration: none;
    display: inline-block;
    margin-top: 20px;
  }
  .reset-btn:hover {
    background-color: #4338ca; /* Indigo-700 */
  }
  .logo {
    width: 120px;
    margin-bottom: 20px;
  }
</style>

{{-- Logo --}}
<div style="text-align:center;">
  <img src="{{ asset('images/logo-ecms.png') }}" alt="Logo ECMS" class="logo">
</div>

# Bonjour !

Vous recevez cet e-mail parce que nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.

@component('mail::button', ['url' => $actionUrl, 'color' => 'primary'])
Réinitialiser mon mot de passe
@endcomponent

Ce lien expirera dans **60 minutes**.

Si vous n'avez pas demandé cette réinitialisation, aucune autre action n'est requise.

Merci,<br>
**L'équipe ECMS**

---

<small style="color:#666;">
Si vous avez des difficultés à cliquer sur le bouton, copiez-collez ce lien dans votre navigateur :  
[{{ $actionUrl }}]({{ $actionUrl }})
</small>

@endcomponent
