<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompraRequest extends FormRequest
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
        return [
            'proveedor_id' => 'required|exists:proveedores,id',
            'comprobante_id' => 'required|exists:comprobantes,id',
            'numero_comprobante' => 'required|unique:compras,numero_comprobante|max:50',
            'total' => 'required|numeric|min:0.01',
            'fecha_hora' => 'required|date',
            'arrayidproducto' => 'required|array|min:1',
            'arrayidproducto.*' => 'exists:productos,id',
            'arraycantidad' => 'required|array|min:1',
            'arraycantidad.*' => 'numeric|min:0.0001',
            'arraypreciocompra' => 'required|array|min:1',
            'arraypreciocompra.*' => 'numeric|min:0.01',
            'arrayprecioventa' => 'required|array|min:1',
            'arrayprecioventa.*' => 'numeric|min:0.01'
        ];
    }

    public function messages()
    {
        return [
            'arrayidproducto.min' => 'Debe agregar al menos un producto',
            'arrayidproducto.*.exists' => 'Uno de los productos seleccionados no existe',
            'arraycantidad.*.min' => 'La cantidad debe ser al menos 0.01',
            'arraypreciocompra.*.min' => 'El precio de compra debe ser al menos 0.01',
            'arrayprecioventa.*.min' => 'El precio de venta debe ser al menos 0.01'
        ];
    }
}
