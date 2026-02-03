<?php

namespace App\Exports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductosExport implements FromCollection, WithHeadings, WithMapping
{
    protected $productos;

    public function __construct($productos)
    {
        $this->productos = $productos;
    }

    public function collection()
    {
        return $this->productos;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Código',
            'Nombre',
            'Categoría',
            'Marca',
            'Precio Compra',
            'Precio Venta',
            'Stock Total',
            'Estado'
        ];
    }

    public function map($producto): array
    {
        return [
            $producto->id,
            $producto->codigo,
            $producto->nombre,
            $producto->categoria ? $producto->categoria->nombre : 'N/A',
            $producto->marca ? $producto->marca->nombre : 'N/A',
            $producto->precio_compra,
            $producto->precio_venta,
            $producto->stock_total ?? 0,
            $producto->estado ? 'Activo' : 'Inactivo',
        ];
    }
}
