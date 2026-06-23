<?php
$titulo_pagina = 'Préstamos — Admin';
$seccion_actual = 'prestamos';
require_once __DIR__ . '/../../includes/header.php';
requiere_admin();
require_once __DIR__ . '/../../config/conexion.php';

$mensaje = $_GET['msg'] ?? '';
$filtro_estado = $_GET['estado'] ?? '';

// Actualizar préstamos vencidos automáticamente
$conexion->query("UPDATE prestamos SET estado = 'vencido' WHERE estado = 'activo' AND fecha_devolucion_esperada < NOW()");

// Construir consulta
$sql = "SELECT p.id, u.nombre AS usuario, u.email, m.titulo, 
               p.fecha_prestamo, p.fecha_devolucion_esperada, p.fecha_devolucion_real, p.estado
        FROM prestamos p
        JOIN usuarios u ON p.usuario_id = u.id
        JOIN materiales m ON p.material_id = m.id";

if (!empty($filtro_estado)) {
    $sql .= " WHERE p.estado = '" . $conexion->real_escape_string($filtro_estado) . "'";
}

$sql .= " ORDER BY p.fecha_prestamo DESC";

$prestamos = $conexion->query($sql);
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Préstamos</h1>
        <p class="page-subtitle">Gestión de todos los préstamos</p>
    </div>
    <a href="/Proyecto3/admin/prestamos/registrar.php" class="btn btn-primary">➕ Nuevo Préstamo</a>
</div>

<?php if ($mensaje === 'registrado'): ?>
    <div class="alert alert-success">✅ Préstamo registrado exitosamente.</div>
<?php elseif ($mensaje === 'devuelto'): ?>
    <div class="alert alert-success">✅ Devolución registrada exitosamente.</div>
<?php endif; ?>

<!-- Filtros -->
<div class="search-bar">
    <a href="/Proyecto3/admin/prestamos/listar.php"
        class="btn <?= empty($filtro_estado) ? 'btn-primary' : 'btn-outline' ?> btn-sm">Todos</a>
    <a href="?estado=activo"
        class="btn <?= $filtro_estado === 'activo' ? 'btn-primary' : 'btn-outline' ?> btn-sm">Activos</a>
    <a href="?estado=devuelto"
        class="btn <?= $filtro_estado === 'devuelto' ? 'btn-primary' : 'btn-outline' ?> btn-sm">Devueltos</a>
    <a href="?estado=vencido"
        class="btn <?= $filtro_estado === 'vencido' ? 'btn-primary' : 'btn-outline' ?> btn-sm">Vencidos</a>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Material</th>
                <th>Fecha Préstamo</th>
                <th>Devolución Esperada</th>
                <th>Devolución Real</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($prestamos->num_rows > 0): ?>
                <?php while ($p = $prestamos->fetch_assoc()): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($p['usuario']) ?></strong>
                            <div style="font-size:0.75rem; color:var(--gray-400);"><?= htmlspecialchars($p['email']) ?></div>
                        </td>
                        <td><?= htmlspecialchars($p['titulo']) ?></td>
                        <td><?= date('d/m/Y', strtotime($p['fecha_prestamo'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($p['fecha_devolucion_esperada'])) ?></td>
                        <td><?= $p['fecha_devolucion_real'] ? date('d/m/Y', strtotime($p['fecha_devolucion_real'])) : '—' ?>
                        </td>
                        <td><span class="status-badge status-<?= $p['estado'] ?>"><?= ucfirst($p['estado']) ?></span></td>
                        <td>
                            <?php if ($p['estado'] === 'activo' || $p['estado'] === 'vencido'): ?>
                                <a href="/Proyecto3/admin/prestamos/devolver.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-success"
                                    onclick="return confirm('¿Confirmar devolución?')">
                                    ✅ Devolver
                                </a>
                            <?php else: ?>
                                <span style="color:var(--gray-400); font-size:0.85rem;">Completado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <div class="empty-icon">📋</div>
                            <p>No hay préstamos registrados</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>