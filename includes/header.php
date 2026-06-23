<?php
// Incluir verificación de sesión si no fue incluida antes
if (session_status() === PHP_SESSION_NONE) {
    require_once __DIR__ . '/auth_check.php';
}

// Determinar la sección activa para el menú
$seccion_actual = $seccion_actual ?? '';
$es_admin_page = es_admin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de gestión de inventario de la Biblioteca UPB">
    <title><?= $titulo_pagina ?? 'Biblioteca UPB' ?></title>
    <link rel="stylesheet" href="/Proyecto3/assets/css/styles.css">
</head>
<body>

<?php if (esta_logueado()): ?>
<div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="M480-60q-72-68-165-104t-195-36v-440q101 0 194 36.5T480-498q73-69 166-105.5T840-640v440q-103 0-195.5 36T480-60Zm0-104q63-47 134-75t146-37v-276q-73 13-143.5 52.5T480-394q-66-66-136.5-105.5T200-552v276q75 9 146 37t134 75ZM367-647q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47Zm169.5-56.5Q560-727 560-760t-23.5-56.5Q513-840 480-840t-56.5 23.5Q400-793 400-760t23.5 56.5Q447-680 480-680t56.5-23.5ZM480-760Zm0 366Z"/></svg>
            </div>
            <span>Biblioteca UPB</span>
        </div>

        <?php if ($es_admin_page): ?>
        <!-- Menú Admin -->
        <div class="sidebar-section">
            <div class="sidebar-section-title">Principal</div>
            <a href="/Proyecto3/admin/index.php" class="sidebar-link <?= $seccion_actual === 'dashboard' ? 'active' : '' ?>">
                <span class="link-icon">📊</span> Dashboard
            </a>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Inventario</div>
            <a href="/Proyecto3/admin/materiales/listar.php" class="sidebar-link <?= $seccion_actual === 'materiales' ? 'active' : '' ?>">
                <span class="link-icon">📖</span> Materiales
            </a>
            <a href="/Proyecto3/admin/autores/listar.php" class="sidebar-link <?= $seccion_actual === 'autores' ? 'active' : '' ?>">
                <span class="link-icon">✍️</span> Autores
            </a>
            <a href="/Proyecto3/admin/editoriales/listar.php" class="sidebar-link <?= $seccion_actual === 'editoriales' ? 'active' : '' ?>">
                <span class="link-icon">🏢</span> Editoriales
            </a>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Préstamos</div>
            <a href="/Proyecto3/admin/prestamos/listar.php" class="sidebar-link <?= $seccion_actual === 'prestamos' ? 'active' : '' ?>">
                <span class="link-icon">📋</span> Todos los Préstamos
            </a>
            <a href="/Proyecto3/admin/prestamos/registrar.php" class="sidebar-link <?= $seccion_actual === 'registrar_prestamo' ? 'active' : '' ?>">
                <span class="link-icon">➕</span> Nuevo Préstamo
            </a>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Usuarios</div>
            <a href="/Proyecto3/admin/usuarios/listar.php" class="sidebar-link <?= $seccion_actual === 'usuarios' ? 'active' : '' ?>">
                <span class="link-icon">👥</span> Lista de Usuarios
            </a>
        </div>

        <?php else: ?>
        <!-- Menú Usuario -->
        <div class="sidebar-section">
            <div class="sidebar-section-title">Principal</div>
            <a href="/Proyecto3/usuario/index.php" class="sidebar-link <?= $seccion_actual === 'dashboard' ? 'active' : '' ?>">
                <span class="link-icon">🏠</span> Inicio
            </a>
            <a href="/Proyecto3/usuario/catalogo.php" class="sidebar-link <?= $seccion_actual === 'catalogo' ? 'active' : '' ?>">
                <span class="link-icon">🔍</span> Catálogo
            </a>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Mis Préstamos</div>
            <a href="/Proyecto3/usuario/mis_prestamos.php" class="sidebar-link <?= $seccion_actual === 'mis_prestamos' ? 'active' : '' ?>">
                <span class="link-icon">📋</span> Historial
            </a>
        </div>
        <?php endif; ?>

        <!-- Footer del sidebar -->
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">
                    <?= strtoupper(substr($_SESSION['nombre'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name"><?= htmlspecialchars($_SESSION['nombre'] ?? '') ?></div>
                    <div class="sidebar-user-role"><?= $_SESSION['rol'] ?? '' ?></div>
                </div>
            </div>
            <a href="/Proyecto3/auth/logout.php" class="btn btn-sm btn-secondary" style="width:100%;">
                🚪 Cerrar Sesión
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
<?php endif; ?>
