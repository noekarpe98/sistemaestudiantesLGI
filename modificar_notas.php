<?php
include 'conexion.php';
include 'header.php'; 

$mensaje = "";

// Activar errores de PHP para depuración (opcional)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Obtener id_alumno e id_materia desde GET o POST
$id_alumno = intval($_GET['id_alumno'] ?? $_POST['id_alumno'] ?? 0);
$id_materia = intval($_GET['id_materia'] ?? $_POST['id_materia'] ?? 0);

if ($id_alumno === 0 || $id_materia === 0) {
    echo "<p style='color:red; text-align:center;'>Alumno o materia no seleccionados.</p>";
    include 'footer.php';
    exit;
}

// Obtener datos del alumno
$stmt = $conn->prepare("SELECT nombre, apellido, dni, id_carrera FROM alumnos WHERE id_alumno=?");
$stmt->bind_param("i", $id_alumno);
$stmt->execute();
$res = $stmt->get_result();
$alumno = $res->fetch_assoc();
$stmt->close();

// Obtener nombre de la carrera
$stmt2 = $conn->prepare("SELECT nombre FROM carreras WHERE id_carrera=?");
$stmt2->bind_param("i", $alumno['id_carrera']);
$stmt2->execute();
$res2 = $stmt2->get_result();
$carrera = $res2->fetch_assoc();
$stmt2->close();

// Obtener nombre de la materia
$stmt3 = $conn->prepare("SELECT nombre FROM materias WHERE id_materia=?");
$stmt3->bind_param("i", $id_materia);
$stmt3->execute();
$res3 = $stmt3->get_result();
$materia = $res3->fetch_assoc();
$stmt3->close();

// Obtener notas actuales
$notas = ['nota1'=>'', 'nota2'=>'', 'nota3'=>''];
$stmt4 = $conn->prepare("SELECT nota1, nota2, nota3 FROM notas WHERE id_alumno=? AND id_materia=?");
$stmt4->bind_param("ii", $id_alumno, $id_materia);
$stmt4->execute();
$res4 = $stmt4->get_result();
if ($row_notas = $res4->fetch_assoc()) $notas = $row_notas;
$stmt4->close();

// Guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nota1 = $_POST['nota1'] !== "" ? floatval($_POST['nota1']) : null;
    $nota2 = $_POST['nota2'] !== "" ? floatval($_POST['nota2']) : null;
    $nota3 = $_POST['nota3'] !== "" ? floatval($_POST['nota3']) : null;

    // Verificar si ya existen las notas
    $check = $conn->prepare("SELECT id_nota FROM notas WHERE id_alumno=? AND id_materia=?");
    $check->bind_param("ii", $id_alumno, $id_materia);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $update = $conn->prepare("UPDATE notas SET nota1=?, nota2=?, nota3=? WHERE id_alumno=? AND id_materia=?");
        $update->bind_param("dddii", $nota1, $nota2, $nota3, $id_alumno, $id_materia);
        if ($update->execute()) {
            $mensaje = "<p style='color:green; text-align:center;'>Notas actualizadas correctamente.</p>";
        } else {
            $mensaje = "<p style='color:red; text-align:center;'>Error al actualizar notas: {$update->error}</p>";
        }
        $update->close();
    } else {
        $insert = $conn->prepare("INSERT INTO notas (id_alumno, id_materia, nota1, nota2, nota3) VALUES (?,?,?,?,?)");
        $insert->bind_param("iiddd", $id_alumno, $id_materia, $nota1, $nota2, $nota3);
        if ($insert->execute()) {
            $mensaje = "<p style='color:green; text-align:center;'>Notas agregadas correctamente.</p>";
        } else {
            $mensaje = "<p style='color:red; text-align:center;'>Error al agregar notas: {$insert->error}</p>";
        }
        $insert->close();
    }
    $check->close();
}
?>

<h2>Modificar Notas</h2>
<?php if ($mensaje) echo $mensaje; ?>

<form method="POST" class="form-alta">
    <input type="hidden" name="id_alumno" value="<?= $id_alumno ?>">
    <input type="hidden" name="id_materia" value="<?= $id_materia ?>">

    <table>
        <tr>
            <th>Nombre</th>
            <td><input type="text" value="<?= htmlspecialchars($alumno['nombre']) ?>" disabled></td>
        </tr>
        <tr>
            <th>Apellido</th>
            <td><input type="text" value="<?= htmlspecialchars($alumno['apellido']) ?>" disabled></td>
        </tr>
        <tr>
            <th>DNI</th>
            <td><input type="text" value="<?= htmlspecialchars($alumno['dni']) ?>" disabled></td>
        </tr>
        <tr>
            <th>Carrera</th>
            <td><input type="text" value="<?= htmlspecialchars($carrera['nombre']) ?>" disabled></td>
        </tr>
        <tr>
            <th>Materia</th>
            <td><input type="text" value="<?= htmlspecialchars($materia['nombre']) ?>" disabled></td>
        </tr>
        <tr>
            <th>Nota 1</th>
            <td><input type="number" name="nota1" step="0.01" min="0" max="10" value="<?= $notas['nota1'] ?>"></td>
        </tr>
        <tr>
            <th>Nota 2</th>
            <td><input type="number" name="nota2" step="0.01" min="0" max="10" value="<?= $notas['nota2'] ?>"></td>
        </tr>
        <tr>
            <th>Nota 3</th>
            <td><input type="number" name="nota3" step="0.01" min="0" max="10" value="<?= $notas['nota3'] ?>"></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align:center;">
                <button type="submit" class="btn-alta">Guardar</button>
                <a href="notas.php" class="btn-alta" style="background-color:#95a5a6; margin-left:10px;">Cancelar</a>
            </td>
        </tr>
    </table>
</form>

<?php include 'footer.php'; ?>

