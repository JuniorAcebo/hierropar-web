<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoCliente extends Model
{
    use HasFactory;

    protected $table = 'grupo_cliente';

    protected $fillable = ['nombre', 'descripcion'];

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'grupo_id');
    }
}
