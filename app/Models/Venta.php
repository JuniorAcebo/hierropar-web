<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    const ESTADO_CANCELADO = 0;
    const ESTADO_POR_PAGAR = 1;
    const ESTADO_POR_ENTREGAR = 2;
    const ESTADO_COMPLETADO = 3;

    protected $table = 'ventas';

    protected $guarded = ['id'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comprobante()
    {
        return $this->belongsTo(Comprobante::class);
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class)->withTimestamps()
            ->withPivot('cantidad', 'precio_venta', 'descuento');
    }
    // En App\Models\Venta
    public function scopeActivos($query)
    {
        return $query->where('estado', 1); // o tu lógica específica
    }


}
