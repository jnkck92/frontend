<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        // Berechtigungen erstellen
        $permissions = [
            ['name' => 'view users'],
            ['name' => 'create users'],
            ['name' => 'edit users'],
            ['name' => 'delete users'],
            ['name' => 'view roles'],
            ['name' => 'manage roles'],
            ['name' => 'view permissions'],
            ['name' => 'manage permissions'],
            ['name' => 'view dashboard'],
            ['name' => 'manage settings'],
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(['name' => $permissionData['name']]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);
        $adminRole->givePermissionTo(Permission::all());
        $adminRole->update([
            'description' => 'Vollzugriff auf alle Systemfunktionen',
            'is_default' => false
        ]);

        $admin = User::where('email', 'test@example.com')->first();
        $admin->assignRole($adminRole);

        // Benutzer-Rolle erstellen
        $userRole = Role::firstOrCreate(['name' => 'Benutzer']);
        $userRole->givePermissionTo(['view dashboard', 'view users']);
        $userRole->update([
            'description' => 'Standard-Benutzer mit eingeschränkten Rechten',
            'is_default' => true
        ]);

        // Manager-Rolle erstellen (optional)
        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $managerRole->givePermissionTo([
            'view users', 'create users', 'edit users', 
            'view dashboard', 'view roles'
        ]);
        $managerRole->update([
            'description' => 'Kann Benutzer verwalten',
            'is_default' => false
        ]);

    }
}
