<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

        Permission::create(['name' => 'show all contracts']);
        Permission::create(['name' => 'add child']);
        Permission::create(['name' => 'edit child']);
        Permission::create(['name' => 'store child']);
        Permission::create(['name' => 'delete child']);
        Permission::create(['name' => 'sign contract']);
        Permission::create(['name' => 'show my contract']);


        $userRole = Role::create(['name' => 'user']);
        $adminRole = Role::create(['name' => 'admin']);

        $userRole->givePermissionTo(['add child', 'edit child', 'store child', 'delete child', 'sign contract', 'show my contract']);
        $adminRole->givePermissionTo(Permission::all());
    }
}
