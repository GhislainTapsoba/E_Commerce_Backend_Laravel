<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer ou récupérer le rôle admin
        $role = Role::firstOrCreate(['name' => 'admin']);

        // Définir toutes les permissions nécessaires
        $permissions = [
            'users.create', 'users.view', 'users.edit', 'users.delete',
            'orders.create', 'orders.view', 'orders.edit', 'orders.delete',
            'customers.create', 'customers.view', 'customers.edit', 'customers.delete',
            'delivery-zones.create', 'delivery-zones.view', 'delivery-zones.edit', 'delivery-zones.delete',
            'statistics.view', 'statistics.export',
            'notifications.view', 'notifications.delete'
        ];

        // Créer les permissions si elles n’existent pas
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Assigner les permissions au rôle admin
        $role->syncPermissions($permissions);

        // Créer un utilisateur admin actif et lui assigner le rôle
        $this->admin = User::factory()->create([
            'is_active' => true,
            'email' => 'admin@example.com', // pour test clair
        ]);
        $this->admin->assignRole($role);

        // Se connecter automatiquement comme admin pour tous les tests
        $this->actingAs($this->admin);
    }
}
