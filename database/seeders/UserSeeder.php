<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    
    public function run(): void
    {
        $user = User::create([
            'name' => 'ewartesan',
            'email' => 'yuca@gmail.com',
            'sucursal_id' => 0,
            'password' => bcrypt('12345678')
        ]);

        //Usuario administrador
        $rol = Role::create(['name' => 'administrador']);
        $permisos = Permission::pluck('id','id')->all();
        $rol->syncPermissions($permisos);
        //$user = User::find(1);
        $user->assignRole('administrador');
    }
}
