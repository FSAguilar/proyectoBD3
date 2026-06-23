<?php
$titulo_pagina = 'Catálogo — Biblioteca UPB';
$seccion_actual = 'catalogo';
require_once __DIR__ . '/../includes/header.php';
requiere_login();
require_once __DIR__ . '/../config/conexion.php';

$busqueda = trim($_GET['q'] ?? '');
$filtro_categoria = $_GET['categoria'] ?? '';

// Construir consulta
$sql = "SELECT m.id, m.titulo, c.nombre AS categoria, e.nombre AS editorial, 
               m.anio_publicacion, m.cantidad_disponible,
               GROUP_CONCAT(CONCAT(a.nombre, ' ', a.apellido) SEPARATOR ', ') AS autores
        FROM materiales m
        JOIN categorias c ON m.categoria_id = c.id
        LEFT JOIN editoriales e ON m.editorial_id = e.id
        LEFT JOIN material_autor ma ON m.id = ma.material_id
        LEFT JOIN autores a ON ma.autor_id = a.id";

$condiciones = [];
$params = [];
$tipos = '';

if (!empty($busqueda)) {
    $condiciones[] = "(m.titulo LIKE ? OR a.nombre LIKE ? OR a.apellido LIKE ?)";
    $like = "%$busqueda%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $tipos .= 'sss';
}

if (!empty($filtro_categoria)) {
    $condiciones[] = "m.categoria_id = ?";
    $params[] = $filtro_categoria;
    $tipos .= 'i';
}

if (count($condiciones) > 0) {
    $sql .= " WHERE " . implode(" AND ", $condiciones);
}

$sql .= " GROUP BY m.id ORDER BY m.titulo ASC";

$stmt = $conexion->prepare($sql);
if (count($params) > 0) {
    $stmt->bind_param($tipos, ...$params);
}
$stmt->execute();
$materiales = $stmt->get_result();

$categorias = $conexion->query("SELECT * FROM categorias ORDER BY nombre");
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Catálogo</h1>
        <p class="page-subtitle">Explora todos los materiales disponibles</p>
    </div>
</div>

<!-- Búsqueda -->
<form method="GET" class="search-bar">
    <div class="search-input-wrapper">
        <span class="search-icon">🔍</span>
        <input type="text" name="q" class="form-control" placeholder="Buscar por título o autor..."
            value="<?= htmlspecialchars($busqueda) ?>">
    </div>
    <select name="categoria" class="form-control" style="max-width:200px;">
        <option value="">Todas las categorías</option>
        <?php while ($cat = $categorias->fetch_assoc()): ?>
            <option value="<?= $cat['id'] ?>" <?= $filtro_categoria == $cat['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['nombre']) ?>
            </option>
        <?php endwhile; ?>
    </select>
    <button type="submit" class="btn btn-primary">Buscar</button>
</form>

<!-- Grid de materiales -->
<?php if ($materiales->num_rows > 0): ?>
    <div class="catalog-grid">
        <?php while ($m = $materiales->fetch_assoc()): ?>
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
                <div class="author">✍️ <?= htmlspecialchars($m['autores'] ?? 'Sin autor registrado') ?></div>
                <div class="meta">
                    <?php if ($m['editorial']): ?>
                        <span>🏢 <?= htmlspecialchars($m['editorial']) ?></span>
                    <?php endif; ?>
                    <?php if ($m['anio_publicacion']): ?>
                        <span>📅 <?= $m['anio_publicacion'] ?></span>
                    <?php endif; ?>
                </div>
                <div class="availability <?= $m['cantidad_disponible'] > 0 ? 'available' : 'unavailable' ?>">
                    <span class="availability-dot"></span>
                    <?= $m['cantidad_disponible'] > 0 ? $m['cantidad_disponible'] . ' disponible(s)' : 'No disponible' ?>
                </div>
            </a>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        <div class="empty-icon">📭</div>
        <p>No se encontraron materiales con esos criterios</p>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>