<?php
include 'conexion.php';

header('Content-Type: application/json');

// Verificar si viene el DNI por GET
if (isset($_GET['dni'])) {
    $dni = trim($_GET['dni']);

    // Buscar alumno y su carrera
    $stmt = $conn->prepare("
        SELECT a.id_alumno, a.nombre, a.apellido, c.nombre AS carrera
        FROM alumnos a
        LEFT JOIN carreras c ON a.id_carrera = c.id_carrera
        WHERE a.dni = ?
    ");
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $res = $stmt->get_result();
    $alumno = $res->fetch_assoc();
    $stmt->close();

    if ($alumno) {
        echo json_encode([
            "existe" => true,
            "id_alumno" => $alumno['id_alumno'],
            "nombre" => $alumno['nombre'],
            "apellido" => $alumno['apellido'],
            "carrera" => $alumno['carrera']
        ]);
    } else {
        echo json_encode(["existe" => false]);
    }
} else {
    echo json_encode(["existe" => false, "error" => "No se recibió DNI"]);
}
