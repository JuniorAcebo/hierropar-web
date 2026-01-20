<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Cliente;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Traslado;
use App\Models\Almacen;
use App\Models\GrupoCliente;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return view('welcome');
        }

        // 1. Obtener métricas de flujo de caja (Ventas vs Compras)
        // Esto reemplaza a getMonthlyTotals y getSalesVsPurchases combinados
        $flujoCaja = $this->getCashFlowMetrics();

        // 2. Obtener Top Productos
        $topProductos = $this->getTopProducts();

        // 3. Obtener Métricas Generales (Cards superiores)
        $metricasGenerales = $this->getGeneralMetrics();

        // 4. Productos bajo stock
        $productosBajoStock = $this->getLowStockProducts();

        return view('panel.index', [
            // Datos para Gráficos (Arrays simples [100, 200, ...])
            'mesesVentas'     => $flujoCaja['ventas'],
            'mesesCompras'    => $flujoCaja['compras'],
            'labelsMeses'     => $flujoCaja['labels'], // Nombres: 'Enero', 'Febrero'...

            // Totales Anuales
            'totalVentas'     => $flujoCaja['total_anual_ventas'],
            'totalCompras'    => $flujoCaja['total_anual_compras'],
            'balanceNeto'     => $flujoCaja['balance_neto'],

            // Datos para Top Productos
            'nombresProductos'    => $topProductos['nombres'],
            'cantidadesProductos' => $topProductos['cantidades'],

            // Resto de datos
            'metricas'           => $metricasGenerales,
            'productosBajoStock' => $productosBajoStock,
        ]);
    }

    /**
     * Unifica la lógica de Ventas vs Compras para optimizar consultas.
     * Retorna arrays listos para Chart.js y totales calculados.
     */
    private function getCashFlowMetrics(): array
    {
        $currentYear = Carbon::now()->year;

        // Ventas por mes
        $ventasPorMes = Venta::with('detalle_ventas')
            ->whereYear('created_at', $currentYear)
            ->get()
            ->groupBy(function($venta) {
                return Carbon::parse($venta->created_at)->month;
            })
            ->map(function($ventas) {
                return $ventas->sum(function($venta) {
                    return $venta->detalle_ventas->sum('precio_total');
                });
            });

        // Compras por mes
        $comprasPorMes = Compra::with('detalles')
            ->whereYear('created_at', $currentYear)
            ->get()
            ->groupBy(function($compra) {
                return Carbon::parse($compra->created_at)->month;
            })
            ->map(function($compras) {
                return $compras->sum(function($compra) {
                    return $compra->detalles->sum('precio_total');
                });
            });

        $data = [
            'ventas' => [],
            'compras' => [],
            'labels' => [],
            'total_anual_ventas' => 0,
            'total_anual_compras' => 0,
        ];

        for ($i = 1; $i <= 12; $i++) {
            $ventaMes  = $ventasPorMes[$i] ?? 0;
            $compraMes = $comprasPorMes[$i] ?? 0;

            $data['ventas'][] = $ventaMes;
            $data['compras'][] = $compraMes;
            $data['labels'][] = ucfirst(Carbon::create()->month($i)->locale('es')->translatedFormat('F'));

            $data['total_anual_ventas'] += $ventaMes;
            $data['total_anual_compras'] += $compraMes;
        }

        $data['balance_neto'] = $data['total_anual_ventas'] - $data['total_anual_compras'];

        return $data;
    }

    private function getTopProducts(): array
    {
        // Sumamos el stock de cada producto en todos los almacenes
        $productos = Producto::select('productos.nombre', DB::raw('SUM(inventario_almacenes.stock) as total_stock'))
            ->join('inventario_almacenes', 'productos.id', '=', 'inventario_almacenes.producto_id')
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('total_stock')
            ->take(5)
            ->get();

        return [
            'nombres' => $productos->pluck('nombre')->toArray(),
            'cantidades' => $productos->pluck('total_stock')->toArray(),
        ];
    }


    private function getLowStockProducts()
    {
        // Sumamos stock de todos los almacenes por producto
        $productos = Producto::select(
                'productos.nombre',
                'productos.precio_compra',
                'productos.precio_venta',
                DB::raw('SUM(inventario_almacenes.stock) as total_stock')
            )
            ->join('inventario_almacenes', 'productos.id', '=', 'inventario_almacenes.producto_id')
            ->groupBy('productos.id', 'productos.nombre', 'productos.precio_compra', 'productos.precio_venta')
            ->having('total_stock', '<', 10) // productos con stock menor a 10
            ->orderBy('total_stock', 'asc')  // los más críticos primero
            ->take(10)
            ->get();

        return $productos;
    }

    private function getGeneralMetrics(): array
    {
        $today = Carbon::today();

        return [
            // MÉTRICAS GLOBALES
            'totalAlmacenes'      => Almacen::count(),
            'totalCategorias'     => Categoria::count(),
            'totalClientes'       => Cliente::count(),
            'totalCompras'        => Compra::count(),
            'totalVentas'         => Venta::count(),
            'totalGrupoClientes'  => GrupoCliente::count(),
            'totalMarcas'         => Marca::count(),
            'totalProductos'      => Producto::count(),
            'totalProveedores'    => Proveedor::count(),
            'totalTraslados'      => Traslado::count(),
            'totalUsuarios'       => User::count(),

            // MÉTRICAS DEL DÍA
            'ventasHoy' => Venta::with('detalle_ventas')
                ->whereDate('created_at', $today)
                ->get()
                ->sum(fn($venta) => $venta->detalle_ventas->sum('precio_total')),

            'comprasHoy' => Compra::with('detalles')
                ->whereDate('created_at', $today)
                ->get()
                ->sum(fn($compra) => $compra->detalles->sum('precio_total')),
        ];
    }

}
