<?php
$titulo_pagina = 'Inicio — Biblioteca UPB';
$seccion_actual = 'dashboard';
require_once __DIR__ . '/../includes/header.php';
requiere_login();
require_once __DIR__ . '/../config/conexion.php';

$usuario_id = $_SESSION['usuario_id'];

// Estadísticas del usuario
$mis_activos = $conexion->query("SELECT COUNT(*) AS total FROM prestamos WHERE usuario_id = $usuario_id AND estado = 'activo'")->fetch_assoc()['total'];
$mis_devueltos = $conexion->query("SELECT COUNT(*) AS total FROM prestamos WHERE usuario_id = $usuario_id AND estado = 'devuelto'")->fetch_assoc()['total'];
$total_catalogo = $conexion->query("SELECT COUNT(*) AS total FROM materiales")->fetch_assoc()['total'];

// Préstamos activos del usuario
$prestamos_activos = $conexion->query("
    SELECT p.id, m.titulo, c.nombre AS categoria, p.fecha_prestamo, p.fecha_devolucion_esperada, p.estado
    FROM prestamos p
    JOIN materiales m ON p.material_id = m.id
    JOIN categorias c ON m.categoria_id = c.id
    WHERE p.usuario_id = $usuario_id AND (p.estado = 'activo' OR p.estado = 'vencido')
    ORDER BY p.fecha_devolucion_esperada ASC
");

// Materiales recientes
$recientes = $conexion->query("
    SELECT m.id, m.titulo, c.nombre AS categoria, m.cantidad_disponible
    FROM materiales m
    JOIN categorias c ON m.categoria_id = c.id
    ORDER BY m.created_at DESC
    LIMIT 4
");
?>

<div class="page-header">
    <div>
        <h1 class="page-title">¡Hola, <?= htmlspecialchars($_SESSION['nombre']) ?>! 👋</h1>
        <p class="page-subtitle">Bienvenido a tu biblioteca digital</p>
    </div>
    <a href="/Proyecto3/usuario/catalogo.php" class="btn btn-primary">🔍 Explorar Catálogo</a>
</div>

<!-- Estadísticas -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">📖</div>
        <div class="stat-info">
            <div class="stat-value"><?= $total_catalogo ?></div>
            <div class="stat-label">En catálogo</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">📋</div>
        <div class="stat-info">
            <div class="stat-value"><?= $mis_activos ?></div>
            <div class="stat-label">Préstamos activos</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">✅</div>
        <div class="stat-info">
            <div class="stat-value"><?= $mis_devueltos ?></div>
            <div class="stat-label">Devueltos</div>
        </div>
    </div>
</div>

<!-- Préstamos activos -->
<div class="card" style="margin-bottom:2rem;">
    <div class="card-header">
        📋 Mis Préstamos Activos
        <a href="/Proyecto3/usuario/mis_prestamos.php" class="btn btn-sm btn-outline">Ver todos</a>
    </div>
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Material</th>
                    <th>Categoría</th>
                    <th>Fecha Préstamo</th>
                    <th>Devolución</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($prestamos_activos->num_rows > 0): ?>
                    <?php while ($p = $prestamos_activos->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($p['titulo']) ?></strong></td>
                            <td><?= htmlspecialchars($p['categoria']) ?></td>
                            <td><?= date('d/m/Y', strtotime($p['fecha_prestamo'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($p['fecha_devolucion_esperada'])) ?></td>
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
                        <td colspan="5">
                            <div class="empty-state">
                                <p>No tienes préstamos activos</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Materiales recientes -->
<h2 style="font-size:1.25rem; font-weight:700; margin-bottom:1rem;">📚 Agregados Recientemente</h2>
<div class="catalog-grid">
    <?php while ($m = $recientes->fetch_assoc()): ?>
        <a href="/Proyecto3/usuario/detalle.php?id=<?= $m['id'] ?>" class="material-card">
            <?php
            $badge = 'badge-libro';
            if ($m['categoria'] === 'Paper')
                $badge = 'badge-paper';
            elseif ($m['categoria'] === 'Proyecto de Grado')
                $badge = 'badge-proyecto';
            elseif ($m['categoria'] === 'Tesis')
                $badge = 'badge-tesis';
            ?>
            <span class="category-badge <?= $badge ?>"><?= htmlspecialchars($m['categoria']) ?></span>
            <h3><?= htmlspecialchars($m['titulo']) ?></h3>
            <div class="availability <?= $m['cantidad_disponible'] > 0 ? 'available' : 'unavailable' ?>">
                <span class="availability-dot"></span>
                <?= $m['cantidad_disponible'] > 0 ? $m['cantidad_disponible'] . ' disponible(s)' : 'No disponible' ?>
            </div>
        </a>
    <?php endwhile; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>