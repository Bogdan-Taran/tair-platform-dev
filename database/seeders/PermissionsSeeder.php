<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(['name' => 'show all contracts']);
        Permission::firstOrCreate(['name' => 'add child']);
        Permission::firstOrCreate(['name' => 'edit child']);
        Permission::firstOrCreate(['name' => 'store child']);
        Permission::firstOrCreate(['name' => 'delete child']);
        Permission::firstOrCreate(['name' => 'sign contract']);
        Permission::firstOrCreate(['name' => 'show my contract']);
        Permission::firstOrCreate(['name' => 'show my children']);
        Permission::firstOrCreate(['name' => 'show all children']);

        $userRole = Role::firstOrCreate(['name' => 'user']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        $userRole->givePermissionTo(['add child', 'edit child', 'store child', 'delete child', 'sign contract', 'show my contract', 'show my children']);
        $adminRole->givePermissionTo(Permission::all());
    }
}
