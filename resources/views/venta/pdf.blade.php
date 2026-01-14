<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>VENTA #{{ $venta->numero_comprobante }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 5px 10px;
        }

        .header {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
            padding-bottom: 3px;
            border-bottom: 1px solid #333;
        }

        .logo {
            height: 30px;
            margin-right: 10px;
        }

        .company-info {
            flex-grow: 1;
            line-height: 1.2;
        }

        .company-name {
            font-size: 12px;
            font-weight: bold;
        }

        h2 {
            font-size: 11px;
            margin: 5px 0;
            text-align: center;
            background-color: #f5f5f5;
            padding: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            font-size: 9px;
        }

        th,
        td {
            padding: 3px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f5f5f5;
        }

        .total-row {
            font-weight: bold;
        }

        .signature-area {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
        }

        .signature {
            width: 45%;
            border-top: 1px solid #000;
            text-align: center;
            padding-top: 2px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .info-table {
            width: 100%;
            margin-bottom: 5px;
        }

        .info-table td {
            padding: 1px 3px;
            border: none;
        }

        .compact {
            margin: 0;
            padding: 0;
        }

        .badge {
            padding: 1px 3px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body class="compact">
    <div class="header">
        <img class="logo" src="{{ public_path('img/MaraDoors.png') }}" alt="Logo">
        <div class="company-info">
            <div class="company-name">MARA-DOORS</div>
            <div>Ballivián entre 13 y 14 - Telf: 71190122</div>
        </div>
        <div style="text-align: right; font-size: 9px;">
            <div><strong>VENTA #{{ $venta->numero_comprobante }}</strong></div>
            <div>{{ $venta->fecha_hora ? \Carbon\Carbon::parse($venta->fecha_hora)->format('d/m/Y') : 'N/A' }}</div>
            <div>
                <span class="badge {{ $venta->estado ? 'badge-success' : 'badge-danger' }}">
                    {{ $venta->estado ? 'ACTIVA' : 'ANULADA' }}
                </span>
            </div>
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td width="20%"><strong>Cliente:</strong></td>
            <td>{{ $venta->cliente->persona->nombre }}</td>
        </tr>
        <tr>
            <td><strong>Documento:</strong></td>
            <td>{{ $venta->cliente->persona->tipo_documento }} {{ $venta->cliente->persona->numero_documento }}</td>
        </tr>
        <tr>
            <td><strong>Comprobante:</strong></td>
            <td>{{ $venta->comprobante->tipo_comprobante }}</td>
        </tr>
        <tr>
            <td><strong>Vendedor:</strong></td>
            <td>{{ $venta->user->name ?? 'Sistema' }}</td>
        </tr>
    </table>

    <h2>DETALLE DE PRODUCTOS VENDIDOS</h2>
    <table>
        <thead>
            <tr>
                <th width="5%">#</th>
                <th>Producto</th>
                <th width="10%">Cant.</th>
                <th width="15%">P. Venta</th>
                <th width="15%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($venta->productos as $producto)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $venta->productos->count() > 5 ? Str::limit($producto->nombre, 20) : $producto->nombre }}</td>
                    <td class="text-right">{{ $producto->pivot->cantidad }}</td>
                    <td class="text-right">Bs. {{ number_format($producto->pivot->precio_venta, 2) }}</td>
                    <td class="text-right">Bs. {{ number_format($producto->pivot->cantidad * $producto->pivot->precio_venta, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            @if($venta->descuento > 0)
            <tr>
                <td colspan="4" class="text-right"><strong>SUBTOTAL Bs.</strong></td>
                <td class="text-right">Bs. {{ number_format($venta->total + $venta->descuento, 2) }}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-right"><strong>DESCUENTO Bs.</strong></td>
                <td class="text-right">- Bs. {{ number_format($venta->descuento, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td colspan="4" class="text-right"><strong>TOTAL Bs.</strong></td>
                <td class="text-right">Bs. {{ number_format($venta->total, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <table style="width: 100%; margin-top: 50px; font-size: 9px; border: none; border-collapse: collapse;">
        <tr>
            <td style="width: 45%; text-align: center; border-top: 1px solid #000; border: none;">Entregado a (Cliente)</td>
            <td style="width: 45%; text-align: center; border-top: 1px solid #000; border: none;">Vendedor</td>
        </tr>
    </table>

    <div style="text-align: center; font-size: 8px; margin-top: 3px;">
        {{ now()->format('d/m/Y H:i') }} - {{ $venta->comprobante->tipo_comprobante === 'Factura' ? 'Documento válido como crédito fiscal' : 'Documento no válido como factura' }}
    </div>
</body>

</html>
