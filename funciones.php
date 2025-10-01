<?php
include 'conexion.php';

/**
 * Calcula el promedio de un array de notas
 */
function calcularPromedio($notas) {
    $notas_validas = array_filter($notas, 'is_numeric');

    $cantidad = count($notas_validas);
    if ($cantidad === 0) {
        return 0; 
    }

    $suma = array_sum($notas_validas);
    return round($suma / $cantidad, 2);
}

/**
 * Verifica si el usuario logueado tiene alguno de los roles permitidos
 * @param array $rolesPermitidos Lista de roles que pueden acceder
 */
function verificarRol($rolesPermitidos = []) {
    session_start();

    if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
        echo "<h2 style='color:red; text-align:center; margin-top:20px;'>
                🚫 No tienes permisos para acceder a esta sección.
              </h2>";
        echo "<p style='text-align:center;'><a href='index.php'>Volver al inicio</a></p>";
        exit;
    }
}
