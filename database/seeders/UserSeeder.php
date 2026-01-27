<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Almacen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    
    public function run(): void
    {
        // Obtener almacén CENTRAL
        $almacenCentral = Almacen::where('codigo', 'CENTRAL')->first();

        $user = User::create([
            'name' => 'ewartesan',
            'email' => 'yuca@gmail.com',
            'almacen_id' => $almacenCentral?->id,
            'password' => bcrypt('12345678')
        ]);

        //Usuario administrador
        $rol = Role::firstOrCreate(['name' => 'administrador']);
        $permisos = Permission::pluck('id','id')->all();
        $rol->syncPermissions($permisos);
        $user->assignRole('administrador');
    }
}
