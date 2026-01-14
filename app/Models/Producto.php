<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'fecha_vencimiento',
        'img_path',
        'marca_id',
        'presentacione_id',
        'precio_compra',
        'precio_venta',
        // 'stock' removed
    ];

    protected $appends = ['stock_total'];

    public function getStockTotalAttribute()
    {
        return $this->almacenes->sum('pivot.cantidad');
    }

    public function almacenes()
    {
        return $this->belongsToMany(Almacen::class, 'producto_almacen')
                    ->withPivot('cantidad')
                    ->withTimestamps();
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoStock::class);
    }

    public function compras()
    {
        return $this->belongsToMany(Compra::class)->withTimestamps()
            ->withPivot('cantidad', 'precio_compra', 'precio_venta');
    }

    public function ventas()
    {
        return $this->belongsToMany(Venta::class)->withTimestamps()
            ->withPivot('cantidad', 'precio_venta', 'descuento');
    }

    public function categorias()
    {
        return $this->belongsToMany(Categoria::class)->withTimestamps();
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function presentacione()
    {
        return $this->belongsTo(Presentacione::class);
    }
    // En app/Models/Producto.php
    public function getPrecioCompraActualAttribute()
    {
        // Prioriza el precio base del producto sobre el de la última compra
        return $this->precio_compra;
    }

    public function getPrecioVentaActualAttribute()
    {
        // Prioriza el precio base del producto sobre el de la última compra
        return $this->precio_venta;
    }

    public function handleUploadImage($image)
    {
        $file = $image;
        $name = time() . $file->getClientOriginalName();
        //$file->move(public_path() . '/img/productos/', $name);
        Storage::putFileAs('/public/productos/', $file, $name, 'public');

        return $name;
    }
    // metodo para verificar si venta es menor que compra
    protected static function booted()
    {
        static::saving(function ($producto) {
            if ($producto->precio_venta < $producto->precio_compra) {
                throw new \Exception("El precio de venta no puede ser menor al de compra");
            }
        });
    }

    // Scopes útiles
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }

    public function scopeConStock($query)
    {
        return $query->whereHas('almacenes', function($q) {
            $q->where('cantidad', '>', 0);
        });
    }
}
