<?php
$titulo_pagina = 'Dashboard — Admin';
$seccion_actual = 'dashboard';
require_once __DIR__ . '/../includes/header.php';
requiere_admin();
require_once __DIR__ . '/../config/conexion.php';

// Estadísticas para el dashboard
$total_materiales = $conexion->query("SELECT COUNT(*) AS total FROM materiales")->fetch_assoc()['total'];
$total_usuarios = $conexion->query("SELECT COUNT(*) AS total FROM usuarios WHERE rol != 'admin'")->fetch_assoc()['total'];
$prestamos_activos = $conexion->query("SELECT COUNT(*) AS total FROM prestamos WHERE estado = 'activo'")->fetch_assoc()['total'];
$prestamos_vencidos = $conexion->query("SELECT COUNT(*) AS total FROM prestamos WHERE estado = 'activo' AND fecha_devolucion_esperada < NOW()")->fetch_assoc()['total'];

// Últimos préstamos
$ultimos_prestamos = $conexion->query("
    SELECT p.id, u.nombre AS usuario, m.titulo, p.fecha_prestamo, p.fecha_devolucion_esperada, p.estado
    FROM prestamos p
    JOIN usuarios u ON p.usuario_id = u.id
    JOIN materiales m ON p.material_id = m.id
    ORDER BY p.fecha_prestamo DESC
    LIMIT 5
");

// Materiales por categoría
$por_categoria = $conexion->query("
    SELECT c.nombre AS categoria, COUNT(*) AS total
    FROM materiales m
    JOIN categorias c ON m.categoria_id = c.id
    GROUP BY c.id
    ORDER BY total DESC
");
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Resumen general de la Biblioteca UPB</p>
    </div>
</div>

<!-- Tarjetas de estadísticas -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">📖</div>
        <div class="stat-info">
            <div class="stat-value"><?= $total_materiales ?></div>
            <div class="stat-label">Materiales</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">👥</div>
        <div class="stat-info">
            <div class="stat-value"><?= $total_usuarios ?></div>
            <div class="stat-label">Usuarios</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">📋</div>
        <div class="stat-info">
            <div class="stat-value"><?= $prestamos_activos ?></div>
            <div class="stat-label">Préstamos activos</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red">⚠️</div>
        <div class="stat-info">
            <div class="stat-value"><?= $prestamos_vencidos ?></div>
            <div class="stat-label">Vencidos</div>
        </div>
    </div>
</div>

<!-- Secciones lado a lado -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- Últimos préstamos -->
    <div class="card">
        <div class="card-header">
            📋 Últimos Préstamos
            <a href="/Proyecto3/admin/prestamos/listar.php" class="btn btn-sm btn-outline">Ver todos</a>
        </div>
        <div class="table-container" style="border:none; box-shadow:none;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Material</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($ultimos_prestamos->num_rows > 0): ?>
                        <?php while ($p = $ultimos_prestamos->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['usuario']) ?></td>
                            <td><?= htmlspecialchars($p['titulo']) ?></td>
                            <td><?= date('d/m/Y', strtotime($p['fecha_prestamo'])) ?></td>
                            <td><span class="status-badge status-<?= $p['estado'] ?>"><?= ucfirst($p['estado']) ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="empty-state">No hay préstamos registrados</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Materiales por categoría -->
    <div class="card">
        <div class="card-header">📊 Por Categoría</div>
        <div class="card-body">
            <?php while ($cat = $por_categoria->fetch_assoc()): ?>
            <div style="display:flex; justify-content:space-between; align-items:center; padding:0.75rem 0; border-bottom:1px solid var(--gray-100);">
                <span style="font-weight:500;"><?= htmlspecialchars($cat['categoria']) ?></span>
                <span style="background:var(--primary-bg); color:var(--primary); padding:0.25rem 0.75rem; border-radius:20px; font-size:0.85rem; font-weight:600;">
                    <?= $cat['total'] ?>
                </span>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
