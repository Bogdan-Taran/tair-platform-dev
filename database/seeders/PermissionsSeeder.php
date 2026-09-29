<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'show all contracts']);
        Permission::create(['name' => 'add child']);
        Permission::create(['name' => 'edit child']);
        Permission::create(['name' => 'store child']);
        Permission::create(['name' => 'delete child']);
        Permission::create(['name' => 'sign contract']);
        Permission::create(['name' => 'show my contract']);
    }
}
