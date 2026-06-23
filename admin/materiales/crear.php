<?php
$titulo_pagina = 'Nuevo Material — Admin';
$seccion_actual = 'materiales';
require_once __DIR__ . '/../../includes/header.php';
requiere_admin();
require_once __DIR__ . '/../../config/conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $categoria_id = intval($_POST['categoria_id'] ?? 0);
    $editorial_id = !empty($_POST['editorial_id']) ? intval($_POST['editorial_id']) : null;
    $isbn = trim($_POST['isbn'] ?? '') ?: null;
    $anio = !empty($_POST['anio_publicacion']) ? intval($_POST['anio_publicacion']) : null;
    $cantidad = intval($_POST['cantidad_total'] ?? 1);
    $descripcion = trim($_POST['descripcion'] ?? '') ?: null;
    $autores_ids = $_POST['autores'] ?? [];

    if (empty($titulo) || $categoria_id === 0) {
        $error = 'El título y la categoría son obligatorios.';
    } else {
        // Insertar material
        $stmt = $conexion->prepare("INSERT INTO materiales (titulo, categoria_id, editorial_id, isbn, anio_publicacion, cantidad_total, cantidad_disponible, descripcion) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siisiiss", $titulo, $categoria_id, $editorial_id, $isbn, $anio, $cantidad, $cantidad, $descripcion);

        if ($stmt->execute()) {
            $material_id = $conexion->insert_id;

            // Insertar relaciones con autores
            if (!empty($autores_ids)) {
                $stmt_autor = $conexion->prepare("INSERT INTO material_autor (material_id, autor_id) VALUES (?, ?)");
                foreach ($autores_ids as $autor_id) {
                    $autor_id = intval($autor_id);
                    $stmt_autor->bind_param("ii", $material_id, $autor_id);
                    $stmt_autor->execute();
                }
                $stmt_autor->close();
            }

            header("Location: /Proyecto3/admin/materiales/listar.php?msg=creado");
            exit;
        } else {
            $error = 'Error al registrar el material.';
        }
        $stmt->close();
    }
}

// Obtener datos para los selects
$categorias = $conexion->query("SELECT * FROM categorias ORDER BY nombre");
$editoriales = $conexion->query("SELECT * FROM editoriales ORDER BY nombre");
$autores = $conexion->query("SELECT * FROM autores ORDER BY apellido, nombre");
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Nuevo Material</h1>
        <p class="page-subtitle">Registrar un libro, paper, tesis o proyecto de grado</p>
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
                <input type="text" id="titulo" name="titulo" class="form-control" placeholder="Título del material"
                    value="<?= htmlspecialchars($titulo ?? '') ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="categoria_id">Categoría *</label>
                    <select id="categoria_id" name="categoria_id" class="form-control" required>
                        <option value="">Seleccionar...</option>
                        <?php while ($cat = $categorias->fetch_assoc()): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($categoria_id ?? '') == $cat['id'] ? 'selected' : '' ?>>
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
                            <option value="<?= $ed['id'] ?>" <?= ($editorial_id ?? '') == $ed['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ed['nombre']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="isbn">ISBN</label>
                    <input type="text" id="isbn" name="isbn" class="form-control" placeholder="978-0-000-00000-0"
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
                    value="<?= $cantidad ?? 1 ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Autores</label>
                <div class="form-hint" style="margin-bottom:0.5rem;">Mantén presionado Ctrl para seleccionar varios
                </div>
                <select name="autores[]" class="form-control" multiple style="min-height:120px;">
                    <?php while ($a = $autores->fetch_assoc()): ?>
                        <option value="<?= $a['id'] ?>">
                            <?= htmlspecialchars($a['nombre'] . ' ' . $a['apellido']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" class="form-control"
                    placeholder="Breve descripción del material..."><?= htmlspecialchars($descripcion ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-lg">💾 Registrar Material</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>