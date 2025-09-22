<?php
include 'conexion.php';
include 'header.php'; 

$mensaje = "";
$alumno = null;

// Activar errores de PHP para depuración (opcional)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Obtener todas las carreras para el select
$carreras_result = $conn->query("SELECT id_carrera, nombre FROM carreras ORDER BY nombre ASC");

// Obtener el ID del alumno desde la URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM alumnos WHERE id_alumno = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $alumno = $result->fetch_assoc();
    $stmt->close();
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_id'])) {
    $id_alumno = intval($_POST['actualizar_id']);
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $email = $_POST['correo'];
    $id_carrera = intval($_POST['carrera']); 

    // Verificar si el DNI ya existe en otro alumno
    $check = $conn->prepare("SELECT id_alumno FROM alumnos WHERE dni = ? AND id_alumno != ?");
    $check->bind_param("si", $dni, $id_alumno);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $mensaje = "<p style='color:red; text-align:center;'>Error: Ya existe otro alumno con ese DNI.</p>";
    } else {
        $stmt = $conn->prepare("UPDATE alumnos SET nombre = ?, apellido = ?, dni = ?, fecha_nacimiento = ?, email = ?, id_carrera = ? WHERE id_alumno = ?");
        $stmt->bind_param("sssssii", $nombre, $apellido, $dni, $fecha_nacimiento, $email, $id_carrera, $id_alumno);

        if ($stmt->execute()) {
            $mensaje = "<p style='color:green; text-align:center;'>Alumno actualizado correctamente.</p>";
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'estudiantes.php';
                    }, 2000);
                  </script>";
        } else {
            $mensaje = "<p style='color:red; text-align:center;'>Error al actualizar: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }

    $check->close();
}
?>

<h2>Modificar Alumno</h2>

<?php if ($mensaje) echo $mensaje; ?>

<?php if ($alumno): ?>
<form method="POST" class="form-alta">
    <input type="hidden" name="actualizar_id" value="<?= $alumno['id_alumno'] ?>">
    <table>
        <tr>
            <th>Nombre</th>
            <td><input type="text" name="nombre" placeholder="Nombre" required value="<?= htmlspecialchars($alumno['nombre']) ?>"></td>
        </tr>
        <tr>
            <th>Apellido</th>
            <td><input type="text" name="apellido" placeholder="Apellido" required value="<?= htmlspecialchars($alumno['apellido']) ?>"></td>
        </tr>
        <tr>
            <th>DNI</th>
            <td><input type="text" name="dni" placeholder="DNI" required value="<?= htmlspecialchars($alumno['dni']) ?>"></td>
        </tr>
        <tr>
            <th>Fecha de Nacimiento</th>
            <td><input type="date" name="fecha_nacimiento" required value="<?= $alumno['fecha_nacimiento'] ?>"></td>
        </tr>
        <tr>
            <th>Correo Electrónico</th>
            <td><input type="email" name="correo" placeholder="ejemplo@correo.com" required value="<?= htmlspecialchars($alumno['email']) ?>"></td>
        </tr>
        <tr>
            <th>Carrera</th>
            <td>
                <select name="carrera" required>
                    <option value="">Seleccione una carrera</option>
                    <?php while($carrera = $carreras_result->fetch_assoc()): ?>
                        <option value="<?= $carrera['id_carrera'] ?>" <?= $carrera['id_carrera'] == $alumno['id_carrera'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($carrera['nombre']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">
                <button type="submit" class="btn-alta">Guardar cambios</button>
                <a href="estudiantes.php" class="btn-alta" style="background-color:#95a5a6; margin-left:10px;">Cancelar</a>
            </td>
        </tr>
    </table>
</form>
<?php else: ?>
    <p style="text-align:center; color:red;">Alumno no encontrado.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>

