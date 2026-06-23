<?php
$titulo_pagina = 'Detalle — Biblioteca UPB';
$seccion_actual = 'catalogo';
require_once __DIR__ . '/../includes/header.php';
requiere_login();
require_once __DIR__ . '/../config/conexion.php';

$id = intval($_GET['id'] ?? 0);
$mensaje = $_GET['msg'] ?? '';

if ($id === 0) {
    header("Location: /Proyecto3/usuario/catalogo.php");
    exit;
}

// Obtener material con sus datos
$stmt = $conexion->prepare("
    SELECT m.*, c.nombre AS categoria, e.nombre AS editorial, e.pais AS editorial_pais
    FROM materiales m
    JOIN categorias c ON m.categoria_id = c.id
    LEFT JOIN editoriales e ON m.editorial_id = e.id
    WHERE m.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$material = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$material) {
    header("Location: /Proyecto3/usuario/catalogo.php");
    exit;
}

// Obtener autores del material
$autores = $conexion->query("
    SELECT a.nombre, a.apellido 
    FROM autores a
    JOIN material_autor ma ON a.id = ma.autor_id
    WHERE ma.material_id = $id
");

// Verificar préstamos activos del usuario para este material
$usuario_id = $_SESSION['usuario_id'];
$ya_prestado = $conexion->query("
    SELECT COUNT(*) AS total FROM prestamos 
    WHERE usuario_id = $usuario_id AND material_id = $id AND estado = 'activo'
")->fetch_assoc()['total'];

// Determinar clase del cover según categoría
$cover_class = 'cover-libro';
$cover_icon = '📖';
if ($material['categoria'] === 'Paper') {
    $cover_class = 'cover-paper';
    $cover_icon = '📄';
} elseif ($material['categoria'] === 'Proyecto de Grado') {
    $cover_class = 'cover-proyecto';
    $cover_icon = '🎓';
} elseif ($material['categoria'] === 'Tesis') {
    $cover_class = 'cover-tesis';
    $cover_icon = '📜';
}
?>

<div class="page-header">
    <a href="/Proyecto3/usuario/catalogo.php" class="btn btn-outline">← Volver al catálogo</a>
</div>

<?php if ($mensaje === 'solicitado'): ?>
    <div class="alert alert-success">✅ Préstamo solicitado exitosamente. El bibliotecario procesará tu solicitud.</div>
<?php elseif ($mensaje === 'error_limite'): ?>
    <div class="alert alert-error">⚠️ Ya tienes 3 préstamos activos (límite máximo).</div>
<?php elseif ($mensaje === 'error_disponible'): ?>
    <div class="alert alert-error">⚠️ Este material no tiene ejemplares disponibles.</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="detail-header">
            <div class="detail-cover <?= $cover_class ?>">
                <?= $cover_icon ?>
            </div>
            <div class="detail-info">
                <?php
                $badge = 'badge-libro';
                if ($material['categoria'] === 'Paper')
                    $badge = 'badge-paper';
                elseif ($material['categoria'] === 'Proyecto de Grado')
                    $badge = 'badge-proyecto';
                elseif ($material['categoria'] === 'Tesis')
                    $badge = 'badge-tesis';
                ?>
                <span class="category-badge <?= $badge ?>"><?= htmlspecialchars($material['categoria']) ?></span>
                <h1><?= htmlspecialchars($material['titulo']) ?></h1>

                <div class="detail-meta">
                    <div class="detail-meta-item">
                        <strong>Autores:</strong>
                        <span>
                            <?php
                            $lista_autores = [];
                            while ($a = $autores->fetch_assoc()) {
                                $lista_autores[] = htmlspecialchars($a['nombre'] . ' ' . $a['apellido']);
                            }
                            echo !empty($lista_autores) ? implode(', ', $lista_autores) : 'No registrado';
                            ?>
                        </span>
                    </div>

                    <?php if ($material['editorial']): ?>
                        <div class="detail-meta-item">
                            <strong>Editorial:</strong>
                            <span><?= htmlspecialchars($material['editorial']) ?>
                                <?= $material['editorial_pais'] ? '(' . htmlspecialchars($material['editorial_pais']) . ')' : '' ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($material['isbn']): ?>
                        <div class="detail-meta-item">
                            <strong>ISBN:</strong>
                            <span><?= htmlspecialchars($material['isbn']) ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($material['anio_publicacion']): ?>
                        <div class="detail-meta-item">
                            <strong>Año:</strong>
                            <span><?= $material['anio_publicacion'] ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="detail-meta-item">
                        <strong>Disponible:</strong>
                        <span
                            class="availability <?= $material['cantidad_disponible'] > 0 ? 'available' : 'unavailable' ?>">
                            <span class="availability-dot"></span>
                            <?= $material['cantidad_disponible'] ?> de <?= $material['cantidad_total'] ?> ejemplares
                        </span>
                    </div>
                </div>

                <!-- Botón de solicitar préstamo -->
                <div style="margin-top:1.5rem;">
                    <?php if ($ya_prestado > 0): ?>
                        <div class="alert alert-info" style="margin-bottom:0;">ℹ️ Ya tienes este material en préstamo.</div>
                    <?php elseif ($material['cantidad_disponible'] > 0): ?>
                        <a href="/Proyecto3/usuario/solicitar_prestamo.php?material_id=<?= $id ?>"
                            class="btn btn-primary btn-lg"
                            onclick="return confirm('¿Solicitar préstamo de este material?')">
                            📋 Solicitar Préstamo
                        </a>
                    <?php else: ?>
                        <button class="btn btn-lg" disabled
                            style="background:var(--gray-200); color:var(--gray-500); cursor:not-allowed;">
                            No disponible
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($material['descripcion']): ?>
            <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px solid var(--gray-200);">
                <h3 style="font-size:1.1rem; font-weight:700; margin-bottom:0.75rem;">Descripción</h3>
                <p style="color:var(--gray-600); line-height:1.8;"><?= nl2br(htmlspecialchars($material['descripcion'])) ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>