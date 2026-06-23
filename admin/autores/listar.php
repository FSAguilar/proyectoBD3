<?php
$titulo_pagina = 'Autores — Admin';
$seccion_actual = 'autores';
require_once __DIR__ . '/../../includes/header.php';
requiere_admin();
require_once __DIR__ . '/../../config/conexion.php';

$mensaje = $_GET['msg'] ?? '';

$autores = $conexion->query("
    SELECT a.id, a.nombre, a.apellido, COUNT(ma.material_id) AS total_materiales
    FROM autores a
    LEFT JOIN material_autor ma ON a.id = ma.autor_id
    GROUP BY a.id
    ORDER BY a.apellido, a.nombre
");
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Autores</h1>
        <p class="page-subtitle">Gestión de autores registrados</p>
    </div>
    <a href="/Proyecto3/admin/autores/crear.php" class="btn btn-primary">➕ Nuevo Autor</a>
</div>

<?php if ($mensaje === 'creado'): ?>
    <div class="alert alert-success">✅ Autor registrado exitosamente.</div>
<?php endif; ?>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Materiales</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($autores->num_rows > 0): ?>
                <?php while ($a = $autores->fetch_assoc()): ?>
                    <tr>
                        <td><?= $a['id'] ?></td>
                        <td><?= htmlspecialchars($a['nombre']) ?></td>
                        <td><strong><?= htmlspecialchars($a['apellido']) ?></strong></td>
                        <td>
                            <span
                                style="background:var(--primary-bg); color:var(--primary); padding:0.2rem 0.6rem; border-radius:12px; font-size:0.8rem; font-weight:600;">
                                <?= $a['total_materiales'] ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">
                        <div class="empty-state">
                            <div class="empty-icon">✍️</div>
                            <p>No hay autores registrados</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>