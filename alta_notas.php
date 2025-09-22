<?php
include 'conexion.php';
include 'header.php';

// Obtener lista de materias
$materias_result = $conn->query("SELECT id_materia, nombre FROM materias ORDER BY nombre ASC");

// Si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = trim($_POST['dni']);
    $materia = intval($_POST['materia']);
    $nota1 = $_POST['nota1'] !== "" ? floatval($_POST['nota1']) : null;
    $nota2 = $_POST['nota2'] !== "" ? floatval($_POST['nota2']) : null;
    $nota3 = $_POST['nota3'] !== "" ? floatval($_POST['nota3']) : null;

    // Buscar alumno por DNI
    $stmt = $conn->prepare("SELECT id_alumno FROM alumnos WHERE dni = ?");
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $res = $stmt->get_result();
    $alumno = $res->fetch_assoc();
    $stmt->close();

    if ($alumno) {
        $id_alumno = $alumno['id_alumno'];

        $insert = $conn->prepare("INSERT INTO notas (id_alumno, id_materia, nota1, nota2, nota3) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("iiddd", $id_alumno, $materia, $nota1, $nota2, $nota3);
        $insert->execute();
        $insert->close();

        echo "<p style='color:green; text-align:center;'>Nota agregada correctamente.</p>";
    } else {
        echo "<p style='color:red; text-align:center;'>No se encontró un alumno con ese DNI.</p>";
    }
}
?>

<h2 style="text-align:center;">Alta de Notas</h2>

<form method="POST" class="form-alta">
    <table>
        <tr>
            <th>DNI Alumno</th>
            <td>
                <input type="text" name="dni" id="dni" required onblur="buscarAlumno()">
            </td>
        </tr>
        <tr>
            <th>Nombre y Apellido</th>
            <td><input type="text" id="nombre_apellido" disabled></td>
        </tr>
        <tr>
            <th>Carrera</th>
            <td><input type="text" id="carrera" disabled></td>
        </tr>
        <tr>
            <th>Materia</th>
            <td>
                <select name="materia" required>
                    <option value="">Seleccione...</option>
                    <?php while($materia = $materias_result->fetch_assoc()): ?>
                        <option value="<?= $materia['id_materia'] ?>"><?= htmlspecialchars($materia['nombre']) ?></option>
                    <?php endwhile; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>Nota 1</th>
            <td><input type="number" name="nota1" step="0.01" min="0" max="10"></td>
        </tr>
        <tr>
            <th>Nota 2</th>
            <td><input type="number" name="nota2" step="0.01" min="0" max="10"></td>
        </tr>
        <tr>
            <th>Nota 3</th>
            <td><input type="number" name="nota3" step="0.01" min="0" max="10"></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align:center;">
                <button type="submit" class="btn-azul">Guardar Nota</button>
                <button type="button" class="btn-cancelar" onclick="window.location.href='notas.php'">Cancelar</button>
            </td>
        </tr>
    </table>
</form>

<script>
function buscarAlumno() {
    let dni = document.getElementById("dni").value;
    if (dni.trim() === "") return;

    fetch("buscar_alumno.php?dni=" + dni)
        .then(res => res.json())
        .then(data => {
            if (data.existe) {
                document.getElementById("nombre_apellido").value = data.nombre + " " + data.apellido;
                document.getElementById("carrera").value = data.carrera;
            } else {
                document.getElementById("nombre_apellido").value = "No encontrado";
                document.getElementById("carrera").value = "";
            }
        });
}
</script>

<?php include 'footer.php'; ?>

