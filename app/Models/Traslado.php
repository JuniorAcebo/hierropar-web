<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Traslado extends Model
{
    use HasFactory;
    protected $table = 'traslados';
    protected $fillable = ['fecha_hora', 'origen', 'destino', 'costo_envio', 'user_id', 'estado'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleTraslado::class, 'traslado_id');
    }
}
