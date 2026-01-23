<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;
    protected $table = 'personas';

    protected $fillable = [
        'razon_social',
        'direccion',
        'telefono',
        'tipo_persona',
        'estado',
        'numero_documento',
        'documento_id',
        'grupo_cliente_id',

    ];

    public function documento()
    {
        return $this->belongsTo(Documento::class, 'documento_id');
    }

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'persona_id');
    }

    public function proveedor()
    {
        return $this->hasOne(Proveedor::class, 'persona_id');
    }
}
