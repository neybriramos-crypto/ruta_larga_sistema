<?php
session_start();
if (!isset($_SESSION["usuario"])) exit("Acceso denegado");

$mysqli = new mysqli("localhost", "root", "", "proyecto");
$mysqli->set_charset("utf8mb4");

$permitidos = ['fletes', 'inventario', 'clientes'];
$tipo = $_GET['tipo'] ?? 'fletes';
if (!in_array($tipo, $permitidos)) exit("Reporte inválido");

// 1. Cabeceras para CSV (Excel no dará error de formato con .csv)
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="Reporte_'.$tipo.'_'.date('Y-m-d').'.csv"');

// 2. Abrir la salida
$salida = fopen('php://output', 'w');

// 3. EL TRUCO: Escribir el BOM UTF-8 para que Excel reconozca los acentos
fprintf($salida, chr(0xEF).chr(0xBB).chr(0xBF));

// 4. EL OTRO TRUCO: Forzar el separador para que Excel no ponga todo en una sola celda
// Esto le dice a Excel: "Usa la coma como separador de columnas"
fwrite($salida, "sep=,\n");

// 5. Consultas
switch($tipo) {
    case 'inventario':
        $sql = "SELECT codigo, nombre, cantidad, precio_unidad FROM inventario";
        break;
    case 'clientes':
        $sql = "SELECT RIF_cedula, nombre, telefono FROM clientes";
        break;
    default:
        $sql = "SELECT id, destino, fecha, estado FROM fletes";
}

$res = $mysqli->query($sql);

if ($res) {
    // Encabezados
    $campos = $res->fetch_fields();
    $head = [];
    foreach ($campos as $c) $head[] = strtoupper($c->name);
    fputcsv($salida, $head, ",");

    // Datos
    while ($fila = $res->fetch_assoc()) {
        // Limpiamos saltos de línea para no romper las filas de Excel
        $fila_limpia = array_map(function($v) { 
            return str_replace(["\r", "\n"], ' ', $v); 
        }, $fila);
        
        fputcsv($salida, $fila_limpia, ",");
    }
}

fclose($salida);
exit;