<?php
// routes/web.php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DeliveryZoneController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes(['register' => false]); // Désactiver l'inscription publique

Route::post('/test-reset-link', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return response()->json(['status' => $status]);
});

Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [DashboardController::class, 'index'])->name('home');

    // Gestion des utilisateurs
    Route::resource('users', UserController::class);

    // Gestion des commandes
    Route::resource('orders', OrderController::class)->except(['create', 'store']);
    Route::get('orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');
    Route::resource('orders', OrderController::class);
    // Route spécifique pour la mise à jour du statut
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->name('orders.update-status')
        ->middleware('permission:orders.edit');

    // Gestion des clients
    Route::resource('customers', CustomerController::class);

    // Gestion des zones de livraison
    Route::resource('delivery-zones', DeliveryZoneController::class);

    // Statistiques et rapports
    Route::get('statistics', [StatisticsController::class, 'index'])
         ->name('statistics.index')
         ->middleware('permission:statistics.view');
    
    Route::post('statistics/export', [StatisticsController::class, 'export'])
         ->name('statistics.export')
         ->middleware('permission:statistics.export');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])
         ->name('notifications.index')
         ->middleware('permission:notifications.view');
    
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy'])
         ->name('notifications.destroy')
         ->middleware('permission:notifications.delete');

    // Profil utilisateur
    Route::get('profile', function () {
        return view('profile.edit');
    })->name('profile.edit');
    
    Route::put('profile', function (Illuminate\Http\Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . auth()->id(),
            'current_password' => 'required|current_password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        $user = User::findOrFail(auth()->id());
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profil mis à jour avec succès.');
    })->name('profile.update');
});
