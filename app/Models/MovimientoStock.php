<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoStock extends Model
{
    use HasFactory;

    protected $table = 'movimientos_stock';

    protected $fillable = [
        'producto_id',
        'almacen_origen_id',
        'almacen_destino_id',
        'user_id',
        'cantidad',
        'tipo_movimiento',
        'referencia'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function almacenOrigen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_origen_id');
    }

    public function almacenDestino()
    {
        return $this->belongsTo(Almacen::class, 'almacen_destino_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
