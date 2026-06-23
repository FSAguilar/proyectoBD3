<?php
session_start();
require_once __DIR__ . '/../../includes/auth_check.php';
requiere_admin();
require_once __DIR__ . '/../../config/conexion.php';

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // Obtener datos del préstamo
    $stmt = $conexion->prepare("SELECT material_id, estado FROM prestamos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $prestamo = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($prestamo && ($prestamo['estado'] === 'activo' || $prestamo['estado'] === 'vencido')) {
        // Marcar como devuelto
        $stmt = $conexion->prepare("UPDATE prestamos SET estado = 'devuelto', fecha_devolucion_real = NOW() WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Incrementar disponibilidad del material
        $material_id = $prestamo['material_id'];
        $conexion->query("UPDATE materiales SET cantidad_disponible = cantidad_disponible + 1 WHERE id = $material_id");
    }
}

header("Location: /Proyecto3/admin/prestamos/listar.php?msg=devuelto");
exit;
