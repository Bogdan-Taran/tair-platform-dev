<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateAdmin extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'firstname' => 'admin',
            'lastname' => 'admin',
            'patronymic' => 'admin',
            'phone' => '89528003535',
            'birthdate' => '1900-01-01',
            'email' => 'admin@mail.ru',
            'password' => Hash::make('admin'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Role::create([
            'name' => 'admin',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $admin->assignRole('admin');
    }
}
