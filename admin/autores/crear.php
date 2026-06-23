<?php
$titulo_pagina = 'Nuevo Autor — Admin';
$seccion_actual = 'autores';
require_once __DIR__ . '/../../includes/header.php';
requiere_admin();
require_once __DIR__ . '/../../config/conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');

    if (empty($nombre) || empty($apellido)) {
        $error = 'Nombre y apellido son obligatorios.';
    } else {
        $stmt = $conexion->prepare("INSERT INTO autores (nombre, apellido) VALUES (?, ?)");
        $stmt->bind_param("ss", $nombre, $apellido);
        if ($stmt->execute()) {
            header("Location: /Proyecto3/admin/autores/listar.php?msg=creado");
            exit;
        } else {
            $error = 'Error al registrar el autor.';
        }
        $stmt->close();
    }
}
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Nuevo Autor</h1>
        <p class="page-subtitle">Registrar un nuevo autor</p>
    </div>
    <a href="/Proyecto3/admin/autores/listar.php" class="btn btn-outline">← Volver</a>
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
                    <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre del autor"
                        value="<?= htmlspecialchars($nombre ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="apellido">Apellido *</label>
                    <input type="text" id="apellido" name="apellido" class="form-control"
                        placeholder="Apellido del autor" value="<?= htmlspecialchars($apellido ?? '') ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-lg">💾 Registrar Autor</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>