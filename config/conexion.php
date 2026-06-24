<?php

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
