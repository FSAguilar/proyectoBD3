<?php
$titulo_pagina = 'Nueva Editorial — Admin';
$seccion_actual = 'editoriales';
require_once __DIR__ . '/../../includes/header.php';
requiere_admin();
require_once __DIR__ . '/../../config/conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $pais = trim($_POST['pais'] ?? '') ?: null;

    if (empty($nombre)) {
        $error = 'El nombre de la editorial es obligatorio.';
    } else {
        $stmt = $conexion->prepare("INSERT INTO editoriales (nombre, pais) VALUES (?, ?)");
        $stmt->bind_param("ss", $nombre, $pais);
        if ($stmt->execute()) {
            header("Location: /Proyecto3/admin/editoriales/listar.php?msg=creado");
            exit;
        } else {
            $error = 'Error al registrar la editorial.';
        }
        $stmt->close();
    }
}
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Nueva Editorial</h1>
        <p class="page-subtitle">Registrar una casa editorial</p>
    </div>
    <a href="/Proyecto3/admin/editoriales/listar.php" class="btn btn-outline">← Volver</a>
</div>

<?php if ($error): ?>
    <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" class="form-container">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="nombre">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" class="form-control"
                        placeholder="Nombre de la editorial" value="<?= htmlspecialchars($nombre ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="pais">País</label>
                    <input type="text" id="pais" name="pais" class="form-control" placeholder="País de origen"
                        value="<?= htmlspecialchars($pais ?? '') ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-lg">💾 Registrar Editorial</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>