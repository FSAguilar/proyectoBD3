<?php
$titulo_pagina = 'Materiales — Admin';
$seccion_actual = 'materiales';
require_once __DIR__ . '/../../includes/header.php';
requiere_admin();
require_once __DIR__ . '/../../config/conexion.php';

// Mensaje de éxito
$mensaje = $_GET['msg'] ?? '';

// Búsqueda
$busqueda = trim($_GET['q'] ?? '');
$filtro_categoria = $_GET['categoria'] ?? '';

// Construir consulta
$sql = "SELECT m.id, m.titulo, c.nombre AS categoria, e.nombre AS editorial, 
               m.anio_publicacion, m.cantidad_total, m.cantidad_disponible,
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

// Obtener categorías para filtro
$categorias = $conexion->query("SELECT * FROM categorias ORDER BY nombre");
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Materiales</h1>
        <p class="page-subtitle">Gestión del inventario bibliográfico</p>
    </div>
    <a href="/Proyecto3/admin/materiales/crear.php" class="btn btn-primary">➕ Nuevo Material</a>
</div>

<?php if ($mensaje === 'creado'): ?>
    <div class="alert alert-success">✅ Material registrado exitosamente.</div>
<?php elseif ($mensaje === 'editado'): ?>
    <div class="alert alert-success">✅ Material actualizado exitosamente.</div>
<?php elseif ($mensaje === 'eliminado'): ?>
    <div class="alert alert-success">✅ Material eliminado exitosamente.</div>
<?php endif; ?>

<!-- Barra de búsqueda y filtros -->
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

<!-- Tabla de materiales -->
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Categoría</th>
                <th>Autores</th>
                <th>Editorial</th>
                <th>Año</th>
                <th>Disponible</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($materiales->num_rows > 0): ?>
                <?php while ($m = $materiales->fetch_assoc()): ?>
                    <tr>
                        <td><?= $m['id'] ?></td>
                        <td><strong><?= htmlspecialchars($m['titulo']) ?></strong></td>
                        <td>
                            <?php
                            $badge_class = 'badge-libro';
                            if ($m['categoria'] === 'Paper')
                                $badge_class = 'badge-paper';
                            elseif ($m['categoria'] === 'Proyecto de Grado')
                                $badge_class = 'badge-proyecto';
                            elseif ($m['categoria'] === 'Tesis')
                                $badge_class = 'badge-tesis';
                            ?>
                            <span class="category-badge <?= $badge_class ?>"><?= htmlspecialchars($m['categoria']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($m['autores'] ?? 'Sin autor') ?></td>
                        <td><?= htmlspecialchars($m['editorial'] ?? '—') ?></td>
                        <td><?= $m['anio_publicacion'] ?? '—' ?></td>
                        <td>
                            <span class="availability <?= $m['cantidad_disponible'] > 0 ? 'available' : 'unavailable' ?>">
                                <span class="availability-dot"></span>
                                <?= $m['cantidad_disponible'] ?>/<?= $m['cantidad_total'] ?>
                            </span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="/Proyecto3/admin/materiales/editar.php?id=<?= $m['id'] ?>"
                                    class="btn btn-sm btn-outline">✏️</a>
                                <a href="/Proyecto3/admin/materiales/eliminar.php?id=<?= $m['id'] ?>"
                                    class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este material?')">🗑️</a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <div class="empty-icon">📭</div>
                            <p>No se encontraron materiales</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>