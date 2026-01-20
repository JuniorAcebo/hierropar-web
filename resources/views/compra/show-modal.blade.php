<div class="container-fluid p-3">
    <div class="text-center mb-2">
        <h4 class="mb-0">MATA DOORS</h4>
        <p class="mb-1 small">CARPINTERIA</p>
    </div>

    <div class="row small mb-2">
        <div class="col-6"><strong>PROVEEDOR:</strong> {{ $compra->proveedore->persona->razon_social }}</div>
    </div>

    <hr class="my-2">

    <div class="text-center mb-2">
        <h5 class="mb-0">NOTA DE COMPRA N° {{ $compra->numero_comprobante }}</h5>
        <p class="small mb-1">REF: COMPRA{{ \Carbon\Carbon::parse($compra->fecha_hora)->format('Y/m/dHis') }}</p>
    </div>

    <div class="row small mb-3">
        <div class="col-6"><strong>USUARIO:</strong> {{ auth()->user()->name }}</div>
        <div class="col-6"><strong>FECHA:</strong>
            {{ \Carbon\Carbon::parse($compra->fecha_hora)->format('d/m/Y H:i') }}</div>
    </div>

    <table class="table table-sm table-bordered mb-3">
        <thead class="small">
            <tr>
                <th width="5%">Nº</th>
                <th>Descripción</th>
                <th width="12%">Cantidad</th>
                <th width="15%">Costo Unit.</th>
                <th width="15%">Subtotal</th>
            </tr>
        </thead>
        <tbody class="small">
            @foreach ($compra->productos as $index => $producto)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $producto->nombre }}</td>
                    <td class="text-end">{{ number_format($producto->pivot->cantidad, 2) }} UNI</td>
                    <td class="text-end">{{ number_format($producto->pivot->precio_compra, 2) }}</td>
                    <td class="text-end">
                        {{ number_format($producto->pivot->cantidad * $producto->pivot->precio_compra, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="row justify-content-end">
        <div class="col-md-5">
            <table class="table table-sm table-bordered">
                <tr class="small">
                    <th>Total (BOB)</th>
                    <td class="text-end">{{ number_format($compra->total, 2) }}</td>
                </tr>

                <tr class="small">
                    <th>Por pagar (BOB)</th>
                    <td class="text-end">{{ number_format($compra->total, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row small mt-3">
        <div class="col-6 text-center"><span class="d-block border-top pt-1">FIRMA PROVEEDOR</span></div>
        <div class="col-6 text-center"><span class="d-block border-top pt-1">FIRMA RESPONSABLE</span></div>
    </div>

    <div class="row small mt-2">
        <div class="col-6">Creado: {{ auth()->user()->name }}</div>
        <div class="col-6 text-end">Fecha: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</div>
    </div>
</div>
