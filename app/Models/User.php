<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * Champs assignables en masse
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    /**
     * Champs cachés pour les tableaux/JSON
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Type casting
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Laravel 10+ auto-hash
        'is_active' => 'boolean',
    ];

    // -------------------------------------------------
    // Relations avec les commandes
    // -------------------------------------------------

    /**
     * Commandes créées par cet utilisateur (admin ou employé)
     */
    public function createdOrders()
    {
        return $this->hasMany(Order::class, 'created_by');
    }

    /**
     * Commandes mises à jour par cet utilisateur (optionnel)
     */
    public function updatedOrders()
    {
        return $this->hasMany(Order::class, 'updated_by');
    }

    /**
     * Commandes du client (si cet utilisateur est un client)
     */
    public function customerOrders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    /**
     * Alias pratique pour compatibilité
     */
    public function orders()
    {
        return $this->customerOrders();
    }

    // -------------------------------------------------
    // Méthodes utilitaires
    // -------------------------------------------------

    /**
     * Vérifie si l'utilisateur est actif
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Assigne un rôle à l'utilisateur et l'active
     */
    public function assignRoleAndActivate(string $role)
    {
        $this->assignRole($role);
        $this->is_active = true;
        $this->save();
    }
}
