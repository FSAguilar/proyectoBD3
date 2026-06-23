<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

$error = '';

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Por favor completa todos los campos.';
    } else {
        // Buscar usuario por email o código
        $stmt = $conexion->prepare("SELECT id, nombre, email, password, rol FROM usuarios WHERE email = ? OR codigo = ?");
        $stmt->bind_param("ss", $email, $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();

            // Verificar contraseña
            if (password_verify($password, $usuario['password'])) {
                // Guardar datos en sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['rol'] = $usuario['rol'];

                // Redirigir según rol
                if ($usuario['rol'] === 'admin') {
                    header("Location: /Proyecto3/admin/index.php");
                } else {
                    header("Location: /Proyecto3/usuario/index.php");
                }
                exit;
            } else {
                $error = 'Contraseña incorrecta.';
            }
        } else {
            $error = 'No se encontró una cuenta con ese email.';
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
    <title>Iniciar Sesión — Biblioteca UPB</title>
    <link rel="stylesheet" href="/Proyecto3/assets/css/styles.css">
</head>

<body>

    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-logo">
                <div class="logo-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor">
                        <path
                            d="M480-60q-72-68-165-104t-195-36v-440q101 0 194 36.5T480-498q73-69 166-105.5T840-640v440q-103 0-195.5 36T480-60Zm0-104q63-47 134-75t146-37v-276q-73 13-143.5 52.5T480-394q-66-66-136.5-105.5T200-552v276q75 9 146 37t134 75ZM367-647q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47Zm169.5-56.5Q560-727 560-760t-23.5-56.5Q513-840 480-840t-56.5 23.5Q400-793 400-760t23.5 56.5Q447-680 480-680t56.5-23.5ZM480-760Zm0 366Z" />
                    </svg>
                </div>
                <h2>Iniciar Sesión</h2>
                <p>Accede a tu cuenta de la Biblioteca UPB</p>
            </div>

            <?php if ($error): ?>
                    <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label" for="email">Correo electrónico o código</label>
                    <input type="text" id="email" name="email" class="form-control" placeholder="tu@email.com"
                        value="<?= htmlspecialchars($email ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control"
                        placeholder="Tu contraseña" required>
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">
                    Iniciar Sesión
                </button>
            </form>

            <div class="auth-footer">
                ¿No tienes cuenta? <a href="/Proyecto3/auth/register.php">Regístrate aquí</a>
            </div>
        </div>
    </div>

</body>

</html>