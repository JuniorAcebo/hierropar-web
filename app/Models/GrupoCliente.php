<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoCliente extends Model
{
    use HasFactory;

    protected $table = 'grupos_clientes';

    protected $fillable = [
        'nombre',
        'descripcion',
        'porcentaje_descuento_general',
        'estado'
    ];

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'grupo_id');
    }

    public function productosConDescuento()
    {
        return $this->belongsToMany(Producto::class, 'descuentos_grupo_producto', 'grupo_id', 'producto_id')
                    ->withPivot('descuento_porcentaje')
                    ->withTimestamps();
    }
}
