<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Seeder;
use App\Models\Persona;

class ProveedorSeeder extends Seeder
{

    public function run(): void
    {
        Proveedor::create([
            'persona_id' => Persona::where('nombre', 'Proveedor General')->first()->id ?? null,
            'estado' => true
        ]);
    }
}
