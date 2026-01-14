<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha_hora' => 'required|date',
            'numero_comprobante' => 'required|unique:ventas,numero_comprobante|max:255',
            'total' => 'required|numeric|min:0',
            'cliente_id' => 'required|exists:clientes,id',
            'user_id' => 'required|exists:users,id',
            'comprobante_id' => 'required|exists:comprobantes,id',
            'arrayidproducto' => 'required|array|min:1',
            'arrayidproducto.*' => 'required|exists:productos,id',
            'arraycantidad' => 'required|array|min:1',
            'arraycantidad.*' => 'required|numeric|min:0.0001',
            'arrayprecioventa' => 'required|array|min:1',
            'arrayprecioventa.*' => 'required|numeric|min:0.01',
            'arraydescuento' => 'required|array|min:1',
            'arraydescuento.*' => 'required|numeric|min:0'
        ];
    }

    public function messages(): array
    {
        return [
            'arrayidproducto.required' => 'Debe agregar al menos un producto',
            'arrayidproducto.min' => 'Debe agregar al menos un producto',
            'arraycantidad.*.min' => 'La cantidad debe ser al menos 0.0001',
            'arrayprecioventa.*.min' => 'El precio de venta debe ser al menos 0.01',
            'arraydescuento.*.min' => 'El descuento no puede ser negativo'
        ];
    }

    // 🔥 IMPORTANTE: Preparar los datos para validación decimal
    protected function prepareForValidation()
    {
        // Convertir todos los valores de arrays a números
        $this->merge([
            'arraycantidad' => array_map('floatval', $this->arraycantidad ?? []),
            'arrayprecioventa' => array_map('floatval', $this->arrayprecioventa ?? []),
            'arraydescuento' => array_map('floatval', $this->arraydescuento ?? []),
            'total' => floatval($this->total)
        ]);
    }
}
