<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;
    protected $table = 'ventas';

    protected $fillable = [
        'fecha_hora', 
        'numero_comprobante', 
        'total', 
        'estado_pago', 
        'estado_entrega', 
        'estado', 
        'cliente_id', 
        'grupo_cliente_id', 
        'almacen_id', 
        'user_id', 
        'comprobante_id',
        'nota_personal',
        'nota_cliente'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comprobante()
    {
        return $this->belongsTo(Comprobante::class, 'comprobante_id');
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }
}
