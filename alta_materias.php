<?php
include 'conexion.php';
include 'header.php';

$mensaje = "";

// Activar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Traer todas las carreras
$carreras_result = $conn->query("SELECT id_carrera, nombre FROM carreras ORDER BY nombre ASC");

// Traer todos los profesores
$profesores_result = $conn->query("SELECT id_profesor, nombre, apellido FROM profesores ORDER BY nombre ASC");

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $id_carrera = intval($_POST['carrera']);
    $id_profesor = intval($_POST['profesor']);

    if ($nombre === "" || $id_carrera === 0 || $id_profesor === 0) {
        $mensaje = "<p style='color:red; text-align:center;'>Todos los campos son obligatorios.</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO materias (nombre, id_carrera, id_profesor) VALUES (?,?,?)");
        $stmt->bind_param("sii", $nombre, $id_carrera, $id_profesor);

        if ($stmt->execute()) {
            $mensaje = "<p style='color:green; text-align:center;'>Materia agregada correctamente.</p>";
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'materias.php';
                    }, 2000);
                  </script>";
        } else {
            $mensaje = "<p style='color:red; text-align:center;'>Error al agregar materia: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}
?>

<h2>Alta de Materia</h2>

<?php if ($mensaje) echo $mensaje; ?>

<form method="POST" class="form-alta">
    <table style="width:100%; border-collapse: collapse; background:#fff; box-shadow: 0 3px 6px rgba(0,0,0,0.15);">
        <tr>
            <th>Nombre</th>
            <td><input type="text" name="nombre" placeholder="Nombre de la materia" required></td>
        </tr>
        <tr>
            <th>Carrera</th>
            <td>
                <select name="carrera" required>
                    <option value="">Seleccione una carrera</option>
                    <?php while($carrera = $carreras_result->fetch_assoc()): ?>
                        <option value="<?= $carrera['id_carrera'] ?>"><?= htmlspecialchars($carrera['nombre']) ?></option>
                    <?php endwhile; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>Profesor</th>
            <td>
                <select name="profesor" required>
                    <option value="">Seleccione un profesor</option>
                    <?php while($profesor = $profesores_result->fetch_assoc()): ?>
                        <option value="<?= $profesor['id_profesor'] ?>"><?= htmlspecialchars($profesor['nombre'] . " " . $profesor['apellido']) ?></option>
                    <?php endwhile; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align:center; padding:15px;">
                <button type="submit" class="btn-alta">Guardar</button>
                <a href="materias.php" class="btn-cancelar">Cancelar</a>
            </td>
        </tr>
    </table>
</form>

<?php include 'footer.php'; ?>

