<?php
session_start();

// Si ya está logueado, redirigir al dashboard correspondiente
if (isset($_SESSION['usuario_id'])) {
    if ($_SESSION['rol'] === 'admin') {
        header("Location: /Proyecto3/admin/index.php");
    } else {
        header("Location: /Proyecto3/usuario/index.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Biblioteca UPB - Sistema de gestión de inventario bibliográfico">
    <title>Biblioteca UPB — Sistema de Inventarios</title>
    <link rel="stylesheet" href="/Proyecto3/assets/css/styles.css">
</head>
<body>


<div class="landing-hero">
    <!-- Navigation -->
    <nav class="landing-nav">
        <div class="landing-logo">
            <div class="logo-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="M480-60q-72-68-165-104t-195-36v-440q101 0 194 36.5T480-498q73-69 166-105.5T840-640v440q-103 0-195.5 36T480-60Zm0-104q63-47 134-75t146-37v-276q-73 13-143.5 52.5T480-394q-66-66-136.5-105.5T200-552v276q75 9 146 37t134 75ZM367-647q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47Zm169.5-56.5Q560-727 560-760t-23.5-56.5Q513-840 480-840t-56.5 23.5Q400-793 400-760t23.5 56.5Q447-680 480-680t56.5-23.5ZM480-760Zm0 366Z"/></svg>
            </div>
            <span>Biblioteca UPB</span>
        </div>
        <div class="landing-nav-links">
            <a href="/Proyecto3/auth/login.php" class="btn btn-secondary">Iniciar Sesión</a>
            <a href="/Proyecto3/auth/register.php" class="btn btn-primary">Registrarse</a>
        </div>
    </nav>

    <!-- Hero Content -->
    <div class="landing-content">
        <div class="landing-text">
            <h1>BIBLIOTECA<br><span>UPB</span></h1>
            <p>
                Bienvenido a la biblioteca de la Universidad Privada Boliviana. 
                Accede al catálogo completo de libros, papers, tesis y proyectos de grado.
                Solicita préstamos de forma rápida y sencilla.
            </p>
            <div class="landing-buttons">
                <a href="/Proyecto3/auth/register.php" class="btn btn-primary btn-lg">
                    Comenzar Ahora
                </a>
                <a href="/Proyecto3/auth/login.php" class="btn btn-secondary btn-lg">
                    Ya tengo cuenta
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="landing-stats">
        <div class="landing-stat">
            <div class="stat-number">500+</div>
            <div class="stat-label">Libros disponibles</div>
        </div>
        <div class="landing-stat">
            <div class="stat-number">200+</div>
            <div class="stat-label">Papers académicos</div>
        </div>
        <div class="landing-stat">
            <div class="stat-number">150+</div>
            <div class="stat-label">Tesis y proyectos</div>
        </div>
        <div class="landing-stat">
            <div class="stat-number">1000+</div>
            <div class="stat-label">Usuarios activos</div>
        </div>
    </div>
</div>


</body>
</html>
