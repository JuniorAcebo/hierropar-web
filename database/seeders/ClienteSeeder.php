<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;
use App\Models\Persona;

class ClienteSeeder extends Seeder
{

    public function run(): void
    {
        Cliente::create([
            'persona_id' => Persona::where('nombre', 'Cliente General')->first()->id ?? null,
            'estado' => true
        ]);
    }
}
