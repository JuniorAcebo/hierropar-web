<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>COMPRA #{{ $compra->numero_comprobante }}</title>
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
            <div><strong>COMPRA #{{ $compra->numero_comprobante }}</strong></div>
<div>{{ $compra->fecha_hora ? \Carbon\Carbon::parse($compra->fecha_hora)->format('d/m/Y') : 'N/A' }}</div>

        </div>
    </div>

    <table class="info-table">
        <tr>
            <td width="20%"><strong>Proveedor:</strong></td>
            <td>{{ $compra->proveedor->persona->razon_social }}</td>
        </tr>
        <tr>
            <td><strong>Documento:</strong></td>
            <td>{{ $compra->proveedor->persona->tipo_documento }} {{ $compra->proveedor->persona->numero_documento }}
            </td>
        </tr>
        <tr>
            <td><strong>Comprobante:</strong></td>
            <td>{{ $compra->comprobante->tipo_comprobante}}</td>
        </tr>
        <tr>
            <td><strong>Registrado por:</strong></td>
            <td>{{ $compra->user->name ?? 'Sistema' }}</td>
        </tr>
    </table>

    <h2>DETALLE DE PRODUCTOS</h2>
    <table>
        <thead>
            <tr>
                <th width="5%">#</th>
                <th>Producto</th>
                <th width="10%">Cant.</th>
                <th width="15%">P. Compra</th>
                <th width="15%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($compra->detalles as $detalle)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $compra->detalles->count() > 5 ? Str::limit($detalle->producto->nombre, 20) : $detalle->producto->nombre }}
                    </td>
                    <td class="text-right">{{ $detalle->cantidad }}</td>
                    <td class="text-right">Bs. {{ number_format($detalle->precio_compra, 2) }}</td>
                    <td class="text-right">Bs.
                        {{ number_format($detalle->cantidad * $detalle->precio_compra, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right"><strong>TOTAL Bs.</strong></td>
                <td class="text-right">Bs. {{ number_format($compra->total, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <table style="width: 100%; margin-top: 50px; font-size: 9px; border: none; border-collapse: collapse;">
        <tr>
            <td style="width: 45%; text-align: center; border-top: 1px solid #000; border: none;">Recibido por</td>
            <td style="width: 45%; text-align: center; border-top: 1px solid #000; border: none;">Entregado por</td>
        </tr>
    </table>



    <div style="text-align: center; font-size: 8px; margin-top: 3px;">
        {{ now()->format('d/m/Y H:i') }} - Documento no válido como factura
    </div>
    <div style="text-align: center; font-size: 8px; margin-top: 3px;">
        {{ $compra->nota_personal }}
    </div>
</body>

</html>
