<?php
include 'conexion.php';
include 'header.php';

$mensaje = "";

// Activar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $duracion = intval($_POST['duracion_anios']);

    if ($nombre === "" || $duracion <= 0) {
        $mensaje = "<p style='color:red; text-align:center;'>Todos los campos son obligatorios.</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO carreras (nombre, duracion_anios) VALUES (?, ?)");
        $stmt->bind_param("si", $nombre, $duracion);

        if ($stmt->execute()) {
            $mensaje = "<p style='color:green; text-align:center;'>Carrera agregada correctamente.</p>";
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'carreras.php';
                    }, 2000);
                  </script>";
        } else {
            $mensaje = "<p style='color:red; text-align:center;'>Error al agregar carrera: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}
?>

<h2>Alta de Carrera</h2>

<?php if ($mensaje) echo $mensaje; ?>

<form method="POST" class="form-alta">
    <table style="width:100%; border-collapse: collapse; background:#fff; box-shadow: 0 3px 6px rgba(0,0,0,0.15);">
        <tr>
            <th>Nombre</th>
            <td><input type="text" name="nombre" placeholder="Nombre de la carrera" required></td>
        </tr>
        <tr>
            <th>Duración (años)</th>
            <td><input type="number" name="duracion_anios" min="1" max="10" placeholder="Ej: 5" required></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align:center; padding:15px;">
                <button type="submit" class="btn-alta">Guardar</button>
                <a href="carreras.php" class="btn-cancelar">Cancelar</a>
            </td>
        </tr>
    </table>
</form>

<?php include 'footer.php'; ?>
