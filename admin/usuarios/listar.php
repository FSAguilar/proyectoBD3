<?php
$titulo_pagina = 'Usuarios — Admin';
$seccion_actual = 'usuarios';
require_once __DIR__ . '/../../includes/header.php';
requiere_admin();
require_once __DIR__ . '/../../config/conexion.php';

$usuarios = $conexion->query("
    SELECT u.id, u.nombre, u.email, u.rol, u.codigo, u.created_at,
           COUNT(CASE WHEN p.estado = 'activo' THEN 1 END) AS prestamos_activos,
           COUNT(p.id) AS total_prestamos
    FROM usuarios u
    LEFT JOIN prestamos p ON u.id = p.usuario_id
    WHERE u.rol != 'admin'
    GROUP BY u.id
    ORDER BY u.nombre
");
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Usuarios</h1>
        <p class="page-subtitle">Usuarios registrados en el sistema</p>
    </div>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>codigo</th>
                <th>Préstamos Activos</th>
                <th>Total Préstamos</th>
                <th>Registro</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($usuarios->num_rows > 0): ?>
                <?php while ($u = $usuarios->fetch_assoc()): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><strong><?= htmlspecialchars($u['nombre']) ?></strong></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <span class="status-badge <?= $u['rol'] === 'estudiante' ? 'status-activo' : 'status-devuelto' ?>">
                                <?= ucfirst($u['rol']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($u['codigo'] ?? '—') ?></td>
                        <td>
                            <span
                                style="font-weight:700; color: <?= $u['prestamos_activos'] > 0 ? 'var(--primary)' : 'var(--gray-400)' ?>;">
                                <?= $u['prestamos_activos'] ?>
                            </span>
                        </td>
                        <td><?= $u['total_prestamos'] ?></td>
                        <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <div class="empty-icon">👥</div>
                            <p>No hay usuarios registrados</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>