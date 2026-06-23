<?php
session_start();
require_once __DIR__ . '/../includes/auth_check.php';
requiere_login();
require_once __DIR__ . '/../config/conexion.php';

$usuario_id = $_SESSION['usuario_id'];
$material_id = intval($_GET['material_id'] ?? 0);

if ($material_id === 0) {
    header("Location: /Proyecto3/usuario/catalogo.php");
    exit;
}

// Verificar disponibilidad
$stmt = $conexion->prepare("SELECT cantidad_disponible FROM materiales WHERE id = ?");
$stmt->bind_param("i", $material_id);
$stmt->execute();
$material = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$material || $material['cantidad_disponible'] <= 0) {
    header("Location: /Proyecto3/usuario/detalle.php?id=$material_id&msg=error_disponible");
    exit;
}

// Verificar límite de 3 préstamos activos
$stmt = $conexion->prepare("SELECT COUNT(*) AS total FROM prestamos WHERE usuario_id = ? AND estado = 'activo'");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$activos = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

if ($activos >= 3) {
    header("Location: /Proyecto3/usuario/detalle.php?id=$material_id&msg=error_limite");
    exit;
}

// Registrar préstamo (14 días por defecto)
$fecha_devolucion = date('Y-m-d H:i:s', strtotime('+14 days'));
$stmt = $conexion->prepare("INSERT INTO prestamos (usuario_id, material_id, fecha_devolucion_esperada) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $usuario_id, $material_id, $fecha_devolucion);

if ($stmt->execute()) {
    // Reducir disponibilidad
    $conexion->query("UPDATE materiales SET cantidad_disponible = cantidad_disponible - 1 WHERE id = $material_id");
    header("Location: /Proyecto3/usuario/detalle.php?id=$material_id&msg=solicitado");
} else {
    header("Location: /Proyecto3/usuario/detalle.php?id=$material_id&msg=error");
}

$stmt->close();
exit;
