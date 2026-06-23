<?php
$titulo_pagina = 'Editar Material — Admin';
$seccion_actual = 'materiales';
require_once __DIR__ . '/../../includes/header.php';
requiere_admin();
require_once __DIR__ . '/../../config/conexion.php';

$id = intval($_GET['id'] ?? 0);
$error = '';

if ($id === 0) {
    header("Location: /Proyecto3/admin/materiales/listar.php");
    exit;
}

// Obtener datos actuales del material
$stmt = $conexion->prepare("SELECT * FROM materiales WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$material = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$material) {
    header("Location: /Proyecto3/admin/materiales/listar.php");
    exit;
}

// Obtener autores actuales del material
$autores_actuales = [];
$res = $conexion->query("SELECT autor_id FROM material_autor WHERE material_id = $id");
while ($row = $res->fetch_assoc()) {
    $autores_actuales[] = $row['autor_id'];
}

// Procesar edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $categoria_id = intval($_POST['categoria_id'] ?? 0);
    $editorial_id = !empty($_POST['editorial_id']) ? intval($_POST['editorial_id']) : null;
    $isbn = trim($_POST['isbn'] ?? '') ?: null;
    $anio = !empty($_POST['anio_publicacion']) ? intval($_POST['anio_publicacion']) : null;
    $cantidad_total = intval($_POST['cantidad_total'] ?? 1);
    $descripcion = trim($_POST['descripcion'] ?? '') ?: null;
    $autores_ids = $_POST['autores'] ?? [];

    // Calcular la nueva cantidad disponible
    $prestados = $material['cantidad_total'] - $material['cantidad_disponible'];
    $cantidad_disponible = max(0, $cantidad_total - $prestados);

    if (empty($titulo) || $categoria_id === 0) {
        $error = 'El título y la categoría son obligatorios.';
    } else {
        $stmt = $conexion->prepare("UPDATE materiales SET titulo=?, categoria_id=?, editorial_id=?, isbn=?, anio_publicacion=?, cantidad_total=?, cantidad_disponible=?, descripcion=? WHERE id=?");
        $stmt->bind_param("siisiissi", $titulo, $categoria_id, $editorial_id, $isbn, $anio, $cantidad_total, $cantidad_disponible, $descripcion, $id);

        if ($stmt->execute()) {
            // Actualizar autores: eliminar y reinsertar
            $conexion->query("DELETE FROM material_autor WHERE material_id = $id");
            if (!empty($autores_ids)) {
                $stmt_a = $conexion->prepare("INSERT INTO material_autor (material_id, autor_id) VALUES (?, ?)");
                foreach ($autores_ids as $autor_id) {
                    $autor_id = intval($autor_id);
                    $stmt_a->bind_param("ii", $id, $autor_id);
                    $stmt_a->execute();
                }
                $stmt_a->close();
            }

            header("Location: /Proyecto3/admin/materiales/listar.php?msg=editado");
            exit;
        } else {
            $error = 'Error al actualizar el material.';
        }
        $stmt->close();
    }
} else {
    // Cargar datos actuales en las variables del formulario
    $titulo = $material['titulo'];
    $categoria_id = $material['categoria_id'];
    $editorial_id = $material['editorial_id'];
    $isbn = $material['isbn'];
    $anio = $material['anio_publicacion'];
    $cantidad_total = $material['cantidad_total'];
    $descripcion = $material['descripcion'];
}

// Datos para los selects
$categorias = $conexion->query("SELECT * FROM categorias ORDER BY nombre");
$editoriales = $conexion->query("SELECT * FROM editoriales ORDER BY nombre");
$autores = $conexion->query("SELECT * FROM autores ORDER BY apellido, nombre");
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Editar Material</h1>
        <p class="page-subtitle">Modificar información de: <?= htmlspecialchars($material['titulo']) ?></p>
    </div>
    <a href="/Proyecto3/admin/materiales/listar.php" class="btn btn-outline">← Volver</a>
</div>

<?php if ($error): ?>
    <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" class="form-container">
            <div class="form-group">
                <label class="form-label" for="titulo">Título *</label>
                <input type="text" id="titulo" name="titulo" class="form-control"
                    value="<?= htmlspecialchars($titulo) ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="categoria_id">Categoría *</label>
                    <select id="categoria_id" name="categoria_id" class="form-control" required>
                        <?php while ($cat = $categorias->fetch_assoc()): ?>
                            <option value="<?= $cat['id'] ?>" <?= $categoria_id == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="editorial_id">Editorial</label>
                    <select id="editorial_id" name="editorial_id" class="form-control">
                        <option value="">Sin editorial</option>
                        <?php while ($ed = $editoriales->fetch_assoc()): ?>
                            <option value="<?= $ed['id'] ?>" <?= $editorial_id == $ed['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ed['nombre']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="isbn">ISBN</label>
                    <input type="text" id="isbn" name="isbn" class="form-control"
                        value="<?= htmlspecialchars($isbn ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label" for="anio_publicacion">Año de publicación</label>
                    <input type="number" id="anio_publicacion" name="anio_publicacion" class="form-control" min="1900"
                        max="2030" value="<?= $anio ?? '' ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="cantidad_total">Cantidad total de ejemplares</label>
                <input type="number" id="cantidad_total" name="cantidad_total" class="form-control" min="1"
                    value="<?= $cantidad_total ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Autores</label>
                <div class="form-hint" style="margin-bottom:0.5rem;">Mantén presionado Ctrl para seleccionar varios
                </div>
                <select name="autores[]" class="form-control" multiple style="min-height:120px;">
                    <?php while ($a = $autores->fetch_assoc()): ?>
                        <option value="<?= $a['id'] ?>" <?= in_array($a['id'], $autores_actuales) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($a['nombre'] . ' ' . $a['apellido']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion"
                    class="form-control"><?= htmlspecialchars($descripcion ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-lg">💾 Guardar Cambios</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>