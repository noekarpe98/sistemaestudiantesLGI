<?php
include 'conexion.php';
include 'header.php'; 

// Activar errores de PHP para depuración (opcional)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Obtener todas las carreras para el select
$carreras_result = $conn->query("SELECT id_carrera, nombre FROM carreras ORDER BY nombre ASC");

// Manejar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $email = $_POST['correo']; // input "correo" mapeado a columna "email"
    $id_carrera = intval($_POST['carrera']); 

    // 1️⃣ Verificar si el DNI ya existe
    $check = $conn->prepare("SELECT id_alumno FROM alumnos WHERE dni = ?");
    $check->bind_param("s", $dni);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<p style='color:red; text-align:center;'>Error: Ya existe un alumno con ese DNI.</p>";
    } else {
        // 2️⃣ Insertar alumno
        $stmt = $conn->prepare("INSERT INTO alumnos (nombre, apellido, dni, fecha_nacimiento, email, id_carrera) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $nombre, $apellido, $dni, $fecha_nacimiento, $email, $id_carrera);

        if ($stmt->execute()) {
            echo "<p style='color:green; text-align:center;'>Alumno agregado correctamente.</p>";
        } else {
            echo "<p style='color:red; text-align:center;'>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }

    $check->close();
}

?>

<h2>Dar de Alta un Alumno</h2>

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






