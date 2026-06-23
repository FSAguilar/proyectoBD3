<?php
$titulo_pagina = 'Editoriales — Admin';
$seccion_actual = 'editoriales';
require_once __DIR__ . '/../../includes/header.php';
requiere_admin();
require_once __DIR__ . '/../../config/conexion.php';

$mensaje = $_GET['msg'] ?? '';

$editoriales = $conexion->query("
    SELECT e.id, e.nombre, e.pais, COUNT(m.id) AS total_materiales
    FROM editoriales e
    LEFT JOIN materiales m ON e.id = m.editorial_id
    GROUP BY e.id
    ORDER BY e.nombre
");
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Editoriales</h1>
        <p class="page-subtitle">Gestión de casas editoriales</p>
    </div>
    <a href="/Proyecto3/admin/editoriales/crear.php" class="btn btn-primary">➕ Nueva Editorial</a>
</div>

<?php if ($mensaje === 'creado'): ?>
    <div class="alert alert-success">✅ Editorial registrada exitosamente.</div>
<?php endif; ?>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>País</th>
                <th>Materiales</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($editoriales->num_rows > 0): ?>
                <?php while ($e = $editoriales->fetch_assoc()): ?>
                    <tr>
                        <td><?= $e['id'] ?></td>
                        <td><strong><?= htmlspecialchars($e['nombre']) ?></strong></td>
                        <td><?= htmlspecialchars($e['pais'] ?? '—') ?></td>
                        <td>
                            <span
                                style="background:var(--primary-bg); color:var(--primary); padding:0.2rem 0.6rem; border-radius:12px; font-size:0.8rem; font-weight:600;">
                                <?= $e['total_materiales'] ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">
                        <div class="empty-state">
                            <div class="empty-icon">🏢</div>
                            <p>No hay editoriales registradas</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>