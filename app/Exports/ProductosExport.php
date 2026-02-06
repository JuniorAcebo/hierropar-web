<?php

namespace App\Exports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductosExport implements FromCollection, WithHeadings, WithMapping
{
    protected $productos;
    protected $includePrices;
    protected $includeStock;
    protected $includeAllDetails;

    public function __construct($productos, $includePrices = true, $includeStock = true, $includeAllDetails = true)
    {
        $this->productos = $productos;
        $this->includePrices = $includePrices;
        $this->includeStock = $includeStock;
        $this->includeAllDetails = $includeAllDetails;
    }

    public function collection()
    {
        return $this->productos;
    }

    public function headings(): array
    {
        $headers = [
            'ID',
            'Código',
            'Nombre',
        ];

        if ($this->includeAllDetails) {
            $headers[] = 'Categoría';
            $headers[] = 'Marca';
        }

        if ($this->includePrices) {
            $headers[] = 'Precio Compra';
            $headers[] = 'Precio Venta';
        }

        if ($this->includeStock) {
            $headers[] = 'Stock Total';
        }

        $headers[] = 'Estado';

        return $headers;
    }

    public function map($producto): array
    {
        $row = [
            $producto->id,
            $producto->codigo,
            $producto->nombre,
        ];

        if ($this->includeAllDetails) {
            $row[] = $producto->categoria ? $producto->categoria->nombre : 'N/A';
            $row[] = $producto->marca ? $producto->marca->nombre : 'N/A';
        }

        if ($this->includePrices) {
            $row[] = $producto->precio_compra;
            $row[] = $producto->precio_venta;
        }

        if ($this->includeStock) {
            $row[] = $producto->stock_total ?? 0;
        }

        $row[] = $producto->estado ? 'Activo' : 'Inactivo';

        return $row;
    }
}
