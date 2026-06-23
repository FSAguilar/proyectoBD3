<?php
$titulo_pagina = 'Nuevo Préstamo — Admin';
$seccion_actual = 'registrar_prestamo';
require_once __DIR__ . '/../../includes/header.php';
requiere_admin();
require_once __DIR__ . '/../../config/conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = intval($_POST['usuario_id'] ?? 0);
    $material_id = intval($_POST['material_id'] ?? 0);
    $dias = intval($_POST['dias_prestamo'] ?? 7);

    if ($usuario_id === 0 || $material_id === 0) {
        $error = 'Selecciona un usuario y un material.';
    } else {
        // Verificar disponibilidad
        $stmt = $conexion->prepare("SELECT cantidad_disponible FROM materiales WHERE id = ?");
        $stmt->bind_param("i", $material_id);
        $stmt->execute();
        $mat = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$mat || $mat['cantidad_disponible'] <= 0) {
            $error = 'Este material no tiene ejemplares disponibles.';
        } else {
            // Verificar límite de préstamos activos (máximo 3)
            $stmt = $conexion->prepare("SELECT COUNT(*) AS total FROM prestamos WHERE usuario_id = ? AND estado = 'activo'");
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $activos = $stmt->get_result()->fetch_assoc()['total'];
            $stmt->close();

            if ($activos >= 3) {
                $error = 'Este usuario ya tiene 3 préstamos activos (límite máximo).';
            } else {
                // Registrar préstamo
                $fecha_devolucion = date('Y-m-d H:i:s', strtotime("+$dias days"));
                $stmt = $conexion->prepare("INSERT INTO prestamos (usuario_id, material_id, fecha_devolucion_esperada) VALUES (?, ?, ?)");
                $stmt->bind_param("iis", $usuario_id, $material_id, $fecha_devolucion);

                if ($stmt->execute()) {
                    // Reducir disponibilidad
                    $conexion->query("UPDATE materiales SET cantidad_disponible = cantidad_disponible - 1 WHERE id = $material_id");
                    header("Location: /Proyecto3/admin/prestamos/listar.php?msg=registrado");
                    exit;
                } else {
                    $error = 'Error al registrar el préstamo.';
                }
                $stmt->close();
            }
        }
    }
}

// Datos para los selects
$usuarios = $conexion->query("SELECT id, nombre, email, rol FROM usuarios WHERE rol != 'admin' ORDER BY nombre");
$materiales = $conexion->query("SELECT id, titulo, cantidad_disponible FROM materiales WHERE cantidad_disponible > 0 ORDER BY titulo");
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Nuevo Préstamo</h1>
        <p class="page-subtitle">Registrar préstamo de material</p>
    </div>
    <a href="/Proyecto3/admin/prestamos/listar.php" class="btn btn-outline">← Volver</a>
</div>

<?php if ($error): ?>
    <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" class="form-container">
            <div class="form-group">
                <label class="form-label" for="usuario_id">Usuario *</label>
                <select id="usuario_id" name="usuario_id" class="form-control" required>
                    <option value="">Seleccionar usuario...</option>
                    <?php while ($u = $usuarios->fetch_assoc()): ?>
                        <option value="<?= $u['id'] ?>" <?= ($usuario_id ?? '') == $u['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['nombre']) ?> — <?= htmlspecialchars($u['email']) ?> (<?= $u['rol'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="material_id">Material *</label>
                <select id="material_id" name="material_id" class="form-control" required>
                    <option value="">Seleccionar material...</option>
                    <?php while ($m = $materiales->fetch_assoc()): ?>
                        <option value="<?= $m['id'] ?>" <?= ($material_id ?? '') == $m['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['titulo']) ?> (<?= $m['cantidad_disponible'] ?> disponibles)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="dias_prestamo">Días de préstamo</label>
                <select id="dias_prestamo" name="dias_prestamo" class="form-control">
                    <option value="7">7 días</option>
                    <option value="14" selected>14 días</option>
                    <option value="21">21 días</option>
                    <option value="30">30 días</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-lg">📋 Registrar Préstamo</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>