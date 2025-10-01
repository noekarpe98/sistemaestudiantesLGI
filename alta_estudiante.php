<?php
include 'conexion.php';
include 'header.php';

// Traer todas las carreras
$carreras_result = $conn->query("SELECT id_carrera, nombre FROM carreras ORDER BY nombre ASC");

$mensaje = "";

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $dni = trim($_POST['dni']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $email = $_POST['correo'];
    $id_carrera = intval($_POST['carrera']);

    // 1️⃣ Verificar si el DNI ya existe
    $check = $conn->prepare("SELECT id_alumno FROM alumnos WHERE dni = ?");
    $check->bind_param("s", $dni);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $mensaje = "<p style='color:red; text-align:center;'>Error: Ya existe un alumno con ese DNI.</p>";
    } else {
        // 2️⃣ Insertar alumno
        $stmt = $conn->prepare("INSERT INTO alumnos (nombre, apellido, dni, fecha_nacimiento, email, id_carrera) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $nombre, $apellido, $dni, $fecha_nacimiento, $email, $id_carrera);

        if ($stmt->execute()) {
            $mensaje = "<p style='color:green; text-align:center;'>Alumno agregado correctamente.</p>";
            
            // Redirigir a la tabla después de 2 segundos
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'estudiantes.php';
                    }, 2000);
                  </script>";
        } else {
            $mensaje = "<p style='color:red; text-align:center;'>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }

    $check->close();
}
?>
<h2>Dar de Alta un Alumno</h2>

<?php if ($mensaje) echo $mensaje; ?>

<form method="POST" class="form-alta">
    <table>
        <tr>
            <th>Nombre</th>
            <td><input type="text" name="nombre" placeholder="Nombre" required></td>
        </tr>
        <tr>
            <th>Apellido</th>
            <td><input type="text" name="apellido" placeholder="Apellido" required></td>
        </tr>
        <tr>
            <th>DNI</th>
            <td><input type="text" name="dni" placeholder="DNI" required></td>
        </tr>
        <tr>
            <th>Fecha de Nacimiento</th>
            <td><input type="date" name="fecha_nacimiento" required></td>
        </tr>
        <tr>
            <th>Correo Electrónico</th>
            <td><input type="email" name="correo" placeholder="ejemplo@correo.com" required></td>
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
            <td colspan="2" style="text-align: center;">
                <button type="submit" class="btn-alta">Agregar Alumno</button>
            </td>
        </tr>
    </table>
</form>

<?php include 'footer.php'; ?>







