<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>REPORTE DE PRODUCTOS</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #555;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
        }
        .doc-title {
            font-size: 14px;
            font-weight: bold;
            text-align: right;
            color: #2c3e50;
        }
        .table-details {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        .table-details th {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
        }
        .table-details td {
            border: 1px solid #ddd;
            padding: 6px;
            vertical-align: middle;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge {
            padding: 2px 5px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-success { background-color: #d1fae5; color: #065f46; }
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
        .footer-note {
            font-size: 8px;
            color: #777;
            text-align: right;
            margin-top: 20px;
            position: fixed;
            bottom: 20px;
            right: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td width="60%">
                <div class="company-name">HIERROPAR</div>
                <div style="font-size:10px;">Sistema de Gestion de Inventario</div>
            </td>
            <td width="40%" class="text-right">
                <div class="doc-title">REPORTE DE PRODUCTOS</div>
                <div style="font-size:9px; color:#555;">Fecha: {{ now()->format('d/m/Y H:i A') }}</div>
            </td>
        </tr>
    </table>

    <table class="table-details">
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="10%">CODIGO</th>
                <th>PRODUCTO</th>
                <th width="15%">CATEGORIA</th>
                <th width="15%">MARCA</th>
                <th width="10%" class="text-right">P. VENTA</th>
                <th width="8%" class="text-center">STOCK</th>
                <th width="8%" class="text-center">ESTADO</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($productos as $producto)
            <tr>
                <td class="text-center">{{ $producto->id }}</td>
                <td>{{ $producto->codigo }}</td>
                <td>
                    <strong>{{ $producto->nombre }}</strong>
                </td>
                <td>{{ $producto->categoria->nombre ?? 'N/A' }}</td>
                <td>{{ $producto->marca->nombre ?? 'N/A' }}</td>
                <td class="text-right">Bs. {{ number_format($producto->precio_venta, 2) }}</td>
                <td class="text-center">
                    <span style="font-weight: bold; {{ ($producto->stock_total <= 10) ? 'color: #991b1b;' : 'color: #065f46;' }}">
                        {{ number_format($producto->stock_total, 0) }}
                    </span>
                </td>
                <td class="text-center">
                    @if($producto->estado == 1)
                        <span class="badge badge-success">ACTIVO</span>
                    @else
                        <span class="badge badge-danger">INACTIVO</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer-note">
        Usuario: {{ auth()->user()->name }} | Pagina: {PAGINA_ACTUAL} de {TOTAL_PAGINAS} | Generado: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>

