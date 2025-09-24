<?php include 'conexion.php';
?>

<?php
// header.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Estudiantes</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather&family=Playfair+Display&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono&family=Source+Code+Pro&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Roboto&display=swap" rel="stylesheet">

</head>
<body>
    <header>
        <h1>Sistema de Registro de Estudiantes</h1>
        <nav class="navbar">
            <ul>
                <li><a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Inicio</a></li>
                <li><a href="estudiantes.php" class="<?= basename($_SERVER['PHP_SELF']) == 'estudiantes.php' ? 'active' : '' ?>">Alumnos</a></li>
                <li><a href="notas.php" class="<?= basename($_SERVER['PHP_SELF']) == 'notas.php' ? 'active' : '' ?>">Notas</a></li>
                <li><a href="materias.php" class="<?= basename($_SERVER['PHP_SELF']) == 'materias.php' ? 'active' : '' ?>">Materias</a></li>
                <li><a href="reportes.php" class="<?= basename($_SERVER['PHP_SELF']) == 'reportes.php' ? 'active' : '' ?>">Reportes</a></li>
                
            </ul>
        </nav>

    </header>
