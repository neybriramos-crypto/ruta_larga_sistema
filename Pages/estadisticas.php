<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// 1. SEGURIDAD
if (!isset($_SESSION["usuario"])) { header("Location: login.php"); exit(); }

// 2. CONEXIÓN Y CONSULTAS ESTADÍSTICAS
class Estadisticas {
    private $db;

    public function __construct() {
        $this->db = new mysqli("localhost", "root", "", "proyecto");
        $this->db->set_charset("utf8mb4");
    }

    public function getResumenGenerales() {
        $res = [];
        // Total Fletes
        $res['total_fletes'] = $this->db->query("SELECT COUNT(*) FROM fletes")->fetch_row()[0];
        // Ingresos Totales (Suma de precios de fletes)
        $res['ingresos'] = $this->db->query("SELECT SUM(precio) FROM fletes")->fetch_row()[0] ?? 0;
        // Total Choferes
        $res['total_choferes'] = $this->db->query("SELECT COUNT(*) FROM choferes")->fetch_row()[0];
        // Valor del Inventario
        $res['valor_inventario'] = $this->db->query("SELECT SUM(cantidad * precio_unidad) FROM inventario")->fetch_row()[0] ?? 0;
        return $res;
    }

    public function getFletesPorMes() {
        $sql = "SELECT MONTHNAME(fecha) as mes, COUNT(*) as cantidad 
                FROM fletes GROUP BY MONTH(fecha) ORDER BY MONTH(fecha) ASC";
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function getTopClientes() {
        $sql = "SELECT cliente, COUNT(*) as total FROM fletes GROUP BY cliente ORDER BY total DESC LIMIT 5";
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function getStockBajo() {
        return $this->db->query("SELECT nombre, cantidad FROM inventario WHERE cantidad < 10")->fetch_all(MYSQLI_ASSOC);
    }
}

$est = new Estadisticas();
$resumen = $est->getResumenGenerales();
$fletesMes = $est->getFletesPorMes();
$topClientes = $est->getTopClientes();
$stockBajo = $est->getStockBajo();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas | Ruta Larga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.0.3/src/regular/style.css">
    <style>
        body { font-family: Georgia, serif; background-color: #f3f4f6; }
        .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="p-6">

    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8 bg-[rgb(8,8,44)] p-6 rounded-2xl text-white shadow-xl">
            <div>
                <h1 class="text-3xl font-bold italic">Panel Estadístico</h1>
                <p class="text-blue-200 text-sm tracking-widest uppercase">Análisis de Operaciones y Rendimiento</p>
            </div>
            <a href="menu.php" class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                <i class="ph ph-house"></i> Volver al Menú
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-blue-600">
                <p class="text-gray-500 text-xs uppercase font-bold tracking-tighter">Total Fletes</p>
                <h2 class="text-3xl font-bold text-gray-800"><?= $resumen['total_fletes'] ?></h2>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-green-600">
                <p class="text-gray-500 text-xs uppercase font-bold tracking-tighter">Ingresos Brutos</p>
                <h2 class="text-3xl font-bold text-gray-800">$<?= number_format($resumen['ingresos'], 2) ?></h2>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-yellow-600">
                <p class="text-gray-500 text-xs uppercase font-bold tracking-tighter">Activos en Inventario</p>
                <h2 class="text-3xl font-bold text-gray-800">$<?= number_format($resumen['valor_inventario'], 2) ?></h2>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-purple-600">
                <p class="text-gray-500 text-xs uppercase font-bold tracking-tighter">Personal (Choferes)</p>
                <h2 class="text-3xl font-bold text-gray-800"><?= $resumen['total_choferes'] ?></h2>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="text-lg font-bold mb-4 text-gray-700 italic border-b pb-2">Tendencia Mensual de Fletes</h3>
                <canvas id="chartFletes"></canvas>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="text-lg font-bold mb-4 text-gray-700 italic border-b pb-2">Distribución por Clientes (Top 5)</h3>
                <canvas id="chartClientes"></canvas>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border-t-4 border-red-500">
            <h3 class="text-lg font-bold mb-4 text-red-700 flex items-center gap-2">
                <i class="ph ph-warning-octagon"></i> Alertas de Stock Bajo (Menos de 10 unidades)
            </h3>
            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-400 text-sm border-b">
                        <th class="py-2">Producto</th>
                        <th class="py-2">Cantidad Actual</th>
                        <th class="py-2">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($stockBajo as $item): ?>
                    <tr class="border-b">
                        <td class="py-3 font-medium"><?= $item['nombre'] ?></td>
                        <td class="py-3 text-red-600 font-bold"><?= $item['cantidad'] ?></td>
                        <td class="py-3"><span class="bg-red-100 text-red-600 px-2 py-1 rounded text-xs uppercase font-bold italic">Reabastecer</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Configuración Chart de Fletes
        const ctxFletes = document.getElementById('chartFletes');
        new Chart(ctxFletes, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($fletesMes, 'mes')) ?>,
                datasets: [{
                    label: 'Cantidad de Fletes',
                    data: <?= json_encode(array_column($fletesMes, 'cantidad')) ?>,
                    borderColor: 'rgb(8, 8, 44)',
                    backgroundColor: 'rgba(8, 8, 44, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            }
        });

        // Configuración Chart de Clientes
        const ctxClientes = document.getElementById('chartClientes');
        new Chart(ctxClientes, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_column($topClientes, 'cliente')) ?>,
                datasets: [{
                    data: <?= json_encode(array_column($topClientes, 'total')) ?>,
                    backgroundColor: ['#1e3a8a', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
                }]
            },
            options: {
                plugins: { legend: { position: 'bottom' } }
            }
        });
    </script>
</body>
</html>