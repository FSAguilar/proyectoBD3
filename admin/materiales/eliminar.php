<?php
session_start();
require_once __DIR__ . '/../../includes/auth_check.php';
requiere_admin();
require_once __DIR__ . '/../../config/conexion.php';

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // Verificar que no tenga préstamos activos
    $stmt = $conexion->prepare("SELECT COUNT(*) AS total FROM prestamos WHERE material_id = ? AND estado = 'activo'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $activos = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    if ($activos > 0) {
        header("Location: /Proyecto3/admin/materiales/listar.php?msg=error_prestamos");
        exit;
    }

    // Eliminar relaciones con autores y luego el material
    $conexion->query("DELETE FROM material_autor WHERE material_id = $id");
    $stmt = $conexion->prepare("DELETE FROM materiales WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: /Proyecto3/admin/materiales/listar.php?msg=eliminado");
exit;
