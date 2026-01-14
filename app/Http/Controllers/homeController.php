<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\Proveedore;
use App\Models\Cliente;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Presentacione;
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

        // Consulta base reutilizable para agrupar por mes
        $queryBase = function($model) use ($currentYear) {
            return $model::activos()
                ->whereYear('created_at', $currentYear)
                ->selectRaw('MONTH(created_at) as mes, SUM(total) as total')
                ->groupBy('mes')
                ->pluck('total', 'mes');
        };

        // Ejecutamos solo 2 consultas (antes eran 4)
        $ventasPorMes = $queryBase(Venta::class);
        $comprasPorMes = $queryBase(Compra::class);

        // Estructuras de salida
        $data = [
            'ventas' => [],
            'compras' => [],
            'labels' => [],
            'total_anual_ventas' => 0,
            'total_anual_compras' => 0,
        ];

        // Llenamos los 12 meses (rellenando con 0 si no hay datos)
        // Usamos Carbon para nombres de mes localizados
        for ($i = 1; $i <= 12; $i++) {
            $ventaMes  = $ventasPorMes[$i] ?? 0;
            $compraMes = $comprasPorMes[$i] ?? 0;

            $data['ventas'][] = $ventaMes;
            $data['compras'][] = $compraMes;
            // ucfirst para que "enero" sea "Enero"
            $data['labels'][] = ucfirst(Carbon::create()->month($i)->locale('es')->translatedFormat('F'));

            $data['total_anual_ventas'] += $ventaMes;
            $data['total_anual_compras'] += $compraMes;
        }

        $data['balance_neto'] = $data['total_anual_ventas'] - $data['total_anual_compras'];

        return $data;
    }

    private function getTopProducts(): array
    {
        // Optimizamos para traer solo columnas necesarias
        $productos = Producto::activos()
            ->orderByDesc('stock')
            ->take(5) // Eloquent usa take() en lugar de limit() comúnmente, aunque limit funciona
            ->get(['nombre', 'stock']);

        return [
            'nombres' => $productos->pluck('nombre')->toArray(),
            'cantidades' => $productos->pluck('stock')->toArray(),
        ];
    }

    private function getLowStockProducts()
    {
        return Producto::activos()
            ->where('stock', '<', 10)
            ->orderBy('stock', 'asc') // Orden ascendente: los más críticos primero
            ->take(10)
            ->get(['nombre', 'stock', 'precio_compra', 'precio_venta']);
    }

    private function getGeneralMetrics(): array
    {
        // Usamos Carbon today() una sola vez para asegurar consistencia exacta en milisegundos
        $today = Carbon::today();

        return [
            // Consultas de conteo (Counts son rápidos, pero asegúrate de tener índices en 'estado' o 'status')
            'totalProductos'      => Producto::activos()->count(),
            'totalUsuarios'       => User::count(),
            'totalCompras'        => Compra::activos()->count(),

            // Relaciones (WhereHas puede ser pesado, asegúrate de que 'persona' tenga índice)
            'totalProveedores'    => Proveedore::whereHas('persona', fn($q) => $q->activos())->count(),
            'totalClientes'       => Cliente::whereHas('persona', fn($q) => $q->activos())->count(),

            // Agrupación de características (Categorias, Marcas, etc)
            'totalCategorias'     => Categoria::whereHas('caracteristica', fn($q) => $q->activos())->count(),
            'totalMarcas'         => Marca::whereHas('caracteristica', fn($q) => $q->activos())->count(),
            'totalPresentaciones' => Presentacione::whereHas('caracteristica', fn($q) => $q->activos())->count(),

            // Sumatorias financieras del día
            'ventasHoy'           => Venta::activos()->whereDate('created_at', $today)->sum('total'),
            'comprasHoy'          => Compra::activos()->whereDate('created_at', $today)->sum('total'),
        ];
    }
}
