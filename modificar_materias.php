<?php
include 'conexion.php';
include 'header.php';

$mensaje = "";
$materia = null;

// 🔹 Obtener todas las carreras y profesores para los selects
$carreras_result = $conn->query("SELECT id_carrera, nombre FROM carreras ORDER BY nombre ASC");
$profesores_result = $conn->query("SELECT id_profesor, nombre, apellido FROM profesores ORDER BY apellido ASC");

// 🔹 Verificar si viene un id en la URL
if (isset($_GET['id'])) {
    $id_materia = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM materias WHERE id_materia = ?");
    $stmt->bind_param("i", $id_materia);
    $stmt->execute();
    $result = $stmt->get_result();
    $materia = $result->fetch_assoc();
    $stmt->close();
}

// 🔹 Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_id'])) {
    $id_materia = intval($_POST['actualizar_id']);
    $nombre = $_POST['nombre'];
    $id_carrera = intval($_POST['carrera']);
    $id_profesor = intval($_POST['profesor']);

    $stmt = $conn->prepare("UPDATE materias 
                            SET nombre = ?, id_carrera = ?, id_profesor = ?
                            WHERE id_materia = ?");
    $stmt->bind_param("siii", $nombre, $id_carrera, $id_profesor, $id_materia);

    if ($stmt->execute()) {
        $mensaje = "<p style='color:green; text-align:center;'>Materia actualizada correctamente.</p>";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'materias.php';
                }, 2000);
              </script>";
    } else {
        $mensaje = "<p style='color:red; text-align:center;'>Error al actualizar: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>

<h2>Modificar Materia</h2>
<?php if ($mensaje) echo $mensaje; ?>

<?php if ($materia): ?>
<form method="POST" class="form-alta">
    <input type="hidden" name="actualizar_id" value="<?= $materia['id_materia'] ?>">
    <table>
        <tr>
            <th>Nombre</th>
            <td>
                <input type="text" name="nombre" placeholder="Nombre de la materia" required 
                       value="<?= htmlspecialchars($materia['nombre']) ?>">
            </td>
        </tr>
        <tr>
            <th>Carrera</th>
            <td>
                <select name="carrera" required>
                    <option value="">Seleccione una carrera</option>
                    <?php while($c = $carreras_result->fetch_assoc()): ?>
                        <option value="<?= $c['id_carrera'] ?>" <?= $materia['id_carrera'] == $c['id_carrera'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nombre']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>Profesor</th>
            <td>
                <select name="profesor" required>
                    <option value="">Seleccione un profesor</option>
                    <?php while($p = $profesores_result->fetch_assoc()): ?>
                        <option value="<?= $p['id_profesor'] ?>" <?= $materia['id_profesor'] == $p['id_profesor'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['apellido'] . ', ' . $p['nombre']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">
                <button type="submit" class="btn-alta">Guardar cambios</button>
                <a href="materias.php" class="btn-alta" style="background-color:#95a5a6; margin-left:10px;">Cancelar</a>
            </td>
        </tr>
    </table>
</form>
<?php else: ?>
    <p style="text-align:center; color:red;">Materia no encontrada.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>
