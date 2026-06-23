<?php
// Verificación de sesión y rol
session_start();

// Verificar si el usuario tiene sesión activa
function esta_logueado() {
    return isset($_SESSION['usuario_id']);
}

// Verificar si el usuario es admin
function es_admin() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
}

// Redirigir si no está logueado
function requiere_login() {
    if (!esta_logueado()) {
        header("Location: /Proyecto3/auth/login.php");
        exit;
    }
}

// Redirigir si no es admin
function requiere_admin() {
    requiere_login();
    if (!es_admin()) {
        header("Location: /Proyecto3/usuario/index.php");
        exit;
    }
}
