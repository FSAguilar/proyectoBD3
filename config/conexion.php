<?php
// ============================================
// Conexión a la Base de Datos — Biblioteca UPB
// ============================================
// Cadena de conexión MySQLi:
//   - "localhost" : Servidor MySQL local (XAMPP)
//   - "root"      : Usuario por defecto de MySQL en XAMPP
//   - ""          : Contraseña vacía (por defecto en XAMPP)
//   - "biblioteca_upb" : Nombre de la base de datos
$host = 'localhost';
$dbname = 'biblioteca_upb';
$user = 'root';
$password = '';


$conexion = new mysqli($host, $user, $password, $dbname);

// Verificar si la conexión fue exitosa
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Establecer charset UTF-8 para soportar caracteres especiales
$conexion->set_charset("utf8mb4");
