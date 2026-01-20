<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => 'required|unique:productos,codigo|max:50',
            'nombre' => 'required|max:80',
            'descripcion' => 'nullable|max:255',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',

            'marca_id' => 'required|integer|exists:marcas,id',
            'tipounidad_id' => 'required|integer|exists:tipo_unidades,id',
            'categoria_id' => 'required|integer|exists:categorias,id',

            // STOCK EN TODOS LOS ALMACENES
            'stock_todos' => 'required|array',
            'stock_todos.*' => 'nullable|integer|min:0',
        ];
    }

    public function attributes()
    {
        return [
            'codigo' => 'código del producto',
            'marca_id' => 'marca',
            'tipounidad_id' => 'tipo de unidad',
            'categoria_id' => 'categoría',
            'stock_todos' => 'stock por almacén',
        ];
    }

    public function messages()
    {
        return [
            'codigo.required' => 'Debes ingresar un código para el producto.',
            'codigo.unique' => 'Este código ya existe. Debe ser único, elige otro.',
            'codigo.max' => 'El código no puede tener más de 50 caracteres.',
            
            'nombre.required' => 'Debes ingresar el nombre del producto.',
            'nombre.max' => 'El nombre no puede superar los 80 caracteres.',

            'precio_compra.required' => 'Debes indicar el precio de compra.',
            'precio_compra.numeric' => 'El precio de compra debe ser un número.',
            'precio_compra.min' => 'El precio de compra no puede ser negativo.',

            'precio_venta.required' => 'Debes indicar el precio de venta.',
            'precio_venta.numeric' => 'El precio de venta debe ser un número.',
            'precio_venta.min' => 'El precio de venta no puede ser negativo.',

            'marca_id.required' => 'Debes seleccionar una marca.',
            'marca_id.exists' => 'La marca seleccionada no es válida.',

            'tipounidad_id.required' => 'Debes seleccionar un tipo de unidad.',
            'tipounidad_id.exists' => 'El tipo de unidad seleccionado no es válido.',

            'categoria_id.required' => 'Debes seleccionar una categoría.',
            'categoria_id.exists' => 'La categoría seleccionada no es válida.',

            'stock_todos.required' => 'Debes indicar el stock para los almacenes.',
            'stock_todos.array' => 'El stock debe enviarse como un arreglo.',
            'stock_todos.*.integer' => 'El stock debe ser un número entero.',
            'stock_todos.*.min' => 'El stock no puede ser negativo.',
        ];
    }
}
