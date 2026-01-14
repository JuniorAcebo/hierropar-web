<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $producto = $this->route('producto');

        return [
            'codigo' => 'required|unique:productos,codigo,' . $producto->id . '|max:50',
            'nombre' => 'required|unique:productos,nombre,' . $producto->id . '|max:80',
            'descripcion' => 'nullable|max:255',
            'fecha_vencimiento' => 'nullable|date',
            'img_path' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'marca_id' => 'required|integer|exists:marcas,id',
            'presentacione_id' => 'required|integer|exists:presentaciones,id',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'incremento_stock' => 'nullable|integer|min:0',
            'decremento_stock' => 'nullable|integer|min:0',
            'categorias' => 'required|array|min:1',
            'categorias.*' => 'integer|exists:categorias,id'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes()
    {
        return [
            'marca_id' => 'marca',
            'presentacione_id' => 'presentación',
            'categorias' => 'categorías',
            'categorias.*' => 'categoría'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages()
    {
        return [
            'codigo.required' => 'El campo código es obligatorio',
            'codigo.unique' => 'Este código ya está registrado',
            'nombre.required' => 'El campo nombre es obligatorio',
            'nombre.unique' => 'Este nombre ya está registrado',
            'marca_id.required' => 'Debe seleccionar una marca',
            'presentacione_id.required' => 'Debe seleccionar una presentación',
            'categorias.required' => 'Debe seleccionar al menos una categoría',
            'categorias.min' => 'Debe seleccionar al menos una categoría',
            'precio_compra.required' => 'El precio de compra es obligatorio',
            'precio_venta.required' => 'El precio de venta es obligatorio',
            'precio_compra.min' => 'El precio de compra no puede ser negativo',
            'precio_venta.min' => 'El precio de venta no puede ser negativo'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $stock_actual = $this->route('producto')->stock;
            $decremento = $this->decremento_stock ?? 0;

            if ($decremento > $stock_actual) {
                $validator->errors()->add(
                    'decremento_stock',
                    'No se puede disminuir más stock del disponible. Stock actual: ' . $stock_actual
                );
            }

            if ($this->precio_venta < $this->precio_compra) {
                $validator->errors()->add(
                    'precio_venta',
                    'El precio de venta no puede ser menor al precio de compra'
                );
            }
        });
    }
}
