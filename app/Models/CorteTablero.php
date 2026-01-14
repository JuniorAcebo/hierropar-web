<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorteTablero extends Model
{
    use HasFactory;

    protected $table = 'cortes_tablero';

    protected $fillable = [
        'cliente_id',
        'nombre_trabajo',
        'descripcion',
        'largo_tablero',
        'ancho_tablero',
        'cantidad_tableros',
        'piezas',
        'total_piezas',
        'total_cortes',
        'estado'
    ];

    protected $casts = [
        'piezas' => 'array',
        'largo_tablero' => 'decimal:2',
        'ancho_tablero' => 'decimal:2',
        'cantidad_tableros' => 'integer',
        'total_piezas' => 'integer',
        'total_cortes' => 'integer'
    ];

    // Relación con cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // Accesores
    public function getAreaTableroAttribute()
    {
        return $this->largo_tablero * $this->ancho_tablero;
    }

    public function getMedidasTableroAttribute()
    {
        return "{$this->largo_tablero} x {$this->ancho_tablero} cm";
    }

    // Calcular total de piezas y cortes
    public function calcularTotales()
    {
        $totalPiezas = 0;
        $totalCortes = 0;

        if ($this->piezas) {
            foreach ($this->piezas as $pieza) {
                $totalPiezas += $pieza['cantidad'];
                // Cada pieza requiere 4 cortes (rectangular)
                $totalCortes += $pieza['cantidad'] * 4;
            }
        }

        $this->total_piezas = $totalPiezas;
        $this->total_cortes = $totalCortes;
    }
}
