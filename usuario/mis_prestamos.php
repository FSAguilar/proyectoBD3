<?php
$titulo_pagina = 'Mis Préstamos — Biblioteca UPB';
$seccion_actual = 'mis_prestamos';
require_once __DIR__ . '/../includes/header.php';
requiere_login();
require_once __DIR__ . '/../config/conexion.php';

$usuario_id = $_SESSION['usuario_id'];

$prestamos = $conexion->query("
    SELECT p.id, m.titulo, c.nombre AS categoria, 
           p.fecha_prestamo, p.fecha_devolucion_esperada, p.fecha_devolucion_real, p.estado
    FROM prestamos p
    JOIN materiales m ON p.material_id = m.id
    JOIN categorias c ON m.categoria_id = c.id
    WHERE p.usuario_id = $usuario_id
    ORDER BY p.fecha_prestamo DESC
");
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Mis Préstamos</h1>
        <p class="page-subtitle">Historial completo de tus préstamos</p>
    </div>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Material</th>
                <th>Categoría</th>
                <th>Fecha Préstamo</th>
                <th>Devolución Esperada</th>
                <th>Devolución Real</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($prestamos->num_rows > 0): ?>
                <?php while ($p = $prestamos->fetch_assoc()): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><strong><?= htmlspecialchars($p['titulo']) ?></strong></td>
                        <td><?= htmlspecialchars($p['categoria']) ?></td>
                        <td><?= date('d/m/Y', strtotime($p['fecha_prestamo'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($p['fecha_devolucion_esperada'])) ?></td>
                        <td><?= $p['fecha_devolucion_real'] ? date('d/m/Y', strtotime($p['fecha_devolucion_real'])) : '—' ?>
                        </td>
                        <td>
                            <?php
                            $estado = $p['estado'];
                            if ($estado === 'activo' && strtotime($p['fecha_devolucion_esperada']) < time()) {
                                $estado = 'vencido';
                            }
                            ?>
                            <span class="status-badge status-<?= $estado ?>"><?= ucfirst($estado) ?></span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <div class="empty-icon">📋</div>
                            <p>No tienes préstamos registrados</p>
                            <a href="/Proyecto3/usuario/catalogo.php" class="btn btn-primary btn-sm"
                                style="margin-top:1rem;">Explorar catálogo</a>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>