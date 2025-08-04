<?php

// database/seeders/RolesAndPermissionsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Créer les permissions
        $permissions = [
            // Utilisateurs
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            
            // Commandes
            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.delete',
            'orders.export',
            
            // Zones de livraison
            'delivery-zones.view',
            'delivery-zones.create',
            'delivery-zones.edit',
            'delivery-zones.delete',
            
            // Notifications
            'notifications.view',
            'notifications.send',
            'notifications.delete',
            
            // Statistiques
            'statistics.view',
            'statistics.export',
            
            // Clients
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Créer les rôles
        $superAdmin = Role::create(['name' => 'Super Administrateur']);
        $admin = Role::create(['name' => 'Administrateur']);
        $manager = Role::create(['name' => 'Gestionnaire']);
        $operator = Role::create(['name' => 'Opérateur']);

        // Assigner toutes les permissions au Super Administrateur
        $superAdmin->givePermissionTo(Permission::all());

        // Assigner les permissions à l'Administrateur
        $admin->givePermissionTo([
            'orders.view', 'orders.create', 'orders.edit', 'orders.export',
            'delivery-zones.view', 'delivery-zones.create', 'delivery-zones.edit',
            'notifications.view', 'notifications.send',
            'statistics.view', 'statistics.export',
            'customers.view', 'customers.create', 'customers.edit',
        ]);

        // Assigner les permissions au Gestionnaire
        $manager->givePermissionTo([
            'orders.view', 'orders.edit',
            'delivery-zones.view',
            'notifications.view',
            'statistics.view',
            'customers.view', 'customers.edit',
        ]);

        // Assigner les permissions à l'Opérateur
        $operator->givePermissionTo([
            'orders.view', 'orders.edit',
            'customers.view',
        ]);
    }
}
