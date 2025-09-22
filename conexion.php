<?php
$host = "localhost";       // Servidor de base de datos
$usuario = "root";          // Usuario de MariaDB
$password = "1818";        // Contraseña del usuario
$base_datos = "sistemaLGI2025";

// Crear conexión
$conn = new mysqli($host, $usuario, $password, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
// echo "Conexión exitosa";
?>
