<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    use HasFactory;

    protected $table = 'almacenes';

    protected $fillable = [
        'nombre',
        'tipo',
        'ubicacion',
        'estado'
    ];

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_almacen')
                    ->withPivot('cantidad')
                    ->withTimestamps();
    }
}
