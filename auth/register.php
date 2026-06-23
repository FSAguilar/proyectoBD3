<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmar = $_POST['confirmar_password'] ?? '';
    $rol = $_POST['rol'] ?? 'estudiante';
    $codigo = trim($_POST['codigo'] ?? '');

    // Validaciones
    if (empty($nombre) || empty($email) || empty($password)) {
        $error = 'Por favor completa todos los campos obligatorios.';
    } elseif ($password !== $confirmar) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif (!in_array($rol, ['estudiante', 'externo'])) {
        $error = 'Tipo de usuario no válido.';
    } else {
        // Verificar si el email ya existe
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Ya existe una cuenta con ese correo electrónico.';
        } else {
            // Crear usuario
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $codigo_valor = !empty($codigo) ? $codigo : null;

            $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, email, password, rol, codigo) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $nombre, $email, $password_hash, $rol, $codigo_valor);

            if ($stmt->execute()) {
                $exito = 'Cuenta creada exitosamente. Ya puedes iniciar sesión.';
                // Limpiar campos
                $nombre = $email = $codigo = '';
            } else {
                $error = 'Error al crear la cuenta. Intenta nuevamente.';
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse — Biblioteca UPB</title>
    <link rel="stylesheet" href="/Proyecto3/assets/css/styles.css">
</head>

<body>

    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-logo">
                <div class="logo-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="M480-60q-72-68-165-104t-195-36v-440q101 0 194 36.5T480-498q73-69 166-105.5T840-640v440q-103 0-195.5 36T480-60Zm0-104q63-47 134-75t146-37v-276q-73 13-143.5 52.5T480-394q-66-66-136.5-105.5T200-552v276q75 9 146 37t134 75ZM367-647q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47Zm169.5-56.5Q560-727 560-760t-23.5-56.5Q513-840 480-840t-56.5 23.5Q400-793 400-760t23.5 56.5Q447-680 480-680t56.5-23.5ZM480-760Zm0 366Z"/></svg>
                </div>
                <h2>Crear Cuenta</h2>
                <p>Regístrate para acceder a la Biblioteca UPB</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($exito): ?>
                <div class="alert alert-success">✅ <?= htmlspecialchars($exito) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label" for="nombre">Nombre completo *</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Tu nombre completo"
                        value="<?= htmlspecialchars($nombre ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Correo electrónico *</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="tu@email.com"
                        value="<?= htmlspecialchars($email ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="rol">Tipo de usuario *</label>
                    <select id="rol" name="rol" class="form-control" required>
                        <option value="estudiante" <?= ($rol ?? '') === 'estudiante' ? 'selected' : '' ?>>Estudiante
                        </option>
                        <option value="externo" <?= ($rol ?? '') === 'externo' ? 'selected' : '' ?>>Externo</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="codigo">codigo universitario</label>
                    <input type="text" id="codigo" name="codigo" class="form-control"
                        placeholder="Opcional para externos" value="<?= htmlspecialchars($codigo ?? '') ?>">
                    <div class="form-hint">Obligatorio para estudiantes</div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="password">Contraseña *</label>
                        <input type="password" id="password" name="password" class="form-control"
                            placeholder="Mín. 6 caracteres" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="confirmar_password">Confirmar *</label>
                        <input type="password" id="confirmar_password" name="confirmar_password" class="form-control"
                            placeholder="Repetir contraseña" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">
                    Crear Cuenta
                </button>
            </form>

            <div class="auth-footer">
                ¿Ya tienes cuenta? <a href="/Proyecto3/auth/login.php">Inicia sesión</a>
            </div>
        </div>
    </div>

</body>

</html>