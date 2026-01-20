<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{

    public function run(): void
    {
        $permisos = [

            //Tipo de Unidad
            'ver-tipounidad',
            'crear-tipounidad',
            'editar-tipounidad',

            //Categorías
            'ver-categoria',
            'crear-categoria',
            'editar-categoria',

            //Almacen
            'ver-almacen',
            'crear-almacen',
            'editar-almacen',
            'dar-de-baja-almacen',

            //Marca
            'ver-marca',
            'crear-marca',
            'editar-marca',

            //Roles
            'ver-role',
            'crear-role',
            'editar-role',

            //Traslado
            'ver-traslado',
            'crear-traslado',
            'editar-traslado',

            //Cliente
            'ver-cliente',
            'crear-cliente',
            'editar-cliente',

            //Proveedor
            'ver-proveedor',
            'crear-proveedor',
            'editar-proveedor',
            'dar-de-baja-proveedor',

            //Compra
            'ver-compra',
            'crear-compra',
            'editar-compra',
            'mostrar-compra',
            'eliminar-compra',

            //Producto
            'ver-producto',
            'crear-producto',
            'editar-producto',
            'update-stock',
            'update-estado',

            //Venta
            'ver-venta',
            'crear-venta',
            'editar-venta',
            'mostrar-venta',
            'eliminar-venta',

            //User
            'ver-user',
            'crear-user',
            'editar-user',
            'eliminar-user',

            //Perfil
            'ver-perfil',
            'editar-perfil',
            // Panel
            'ver-panel',

        ];

        foreach ($permisos as $permiso) {
            Permission::create(['name' => $permiso]);
        }
    }
}
