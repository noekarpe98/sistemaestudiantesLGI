<?php
include 'conexion.php';

function calcularPromedio($notas) {
    $notas_validas = array_filter($notas, 'is_numeric');

    $cantidad = count($notas_validas);
    if ($cantidad === 0) {
        return 0; 
    }

    $suma = array_sum($notas_validas);
    return round($suma / $cantidad, 2);
}
?>

