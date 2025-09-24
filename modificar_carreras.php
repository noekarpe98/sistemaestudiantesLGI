<?php
include 'conexion.php';
include 'header.php';

$mensaje = "";
$carrera = null;

// 🔹 Verificar si viene un id en la URL
if (isset($_GET['id'])) {
    $id_carrera = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM carreras WHERE id_carrera = ?");
    $stmt->bind_param("i", $id_carrera);
    $stmt->execute();
    $result = $stmt->get_result();
    $carrera = $result->fetch_assoc();
    $stmt->close();
}

// 🔹 Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_id'])) {
    $id_carrera = intval($_POST['actualizar_id']);
    $nombre = trim($_POST['nombre']);
    $duracion = intval($_POST['duracion_anios']);

    if ($nombre === "" || $duracion <= 0) {
        $mensaje = "<p style='color:red; text-align:center;'>Todos los campos son obligatorios.</p>";
    } else {
        $stmt = $conn->prepare("UPDATE carreras 
                                SET nombre = ?, duracion_anios = ?
                                WHERE id_carrera = ?");
        $stmt->bind_param("sii", $nombre, $duracion, $id_carrera);

        if ($stmt->execute()) {
            $mensaje = "<p style='color:green; text-align:center;'>Carrera actualizada correctamente.</p>";
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'carreras.php';
                    }, 2000);
                  </script>";
        } else {
            $mensaje = "<p style='color:red; text-align:center;'>Error al actualizar: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}
?>

<h2>Modificar Carrera</h2>
<?php if ($mensaje) echo $mensaje; ?>

<?php if ($carrera): ?>
<form method="POST" class="form-alta">
    <input type="hidden" name="actualizar_id" value="<?= $carrera['id_carrera'] ?>">
    <table>
        <tr>
            <th>Nombre</th>
            <td>
                <input type="text" name="nombre" placeholder="Nombre de la carrera" required 
                       value="<?= htmlspecialchars($carrera['nombre']) ?>">
            </td>
        </tr>
        <tr>
            <th>Duración (años)</th>
            <td>
                <input type="number" name="duracion_anios" min="1" max="10" required
                       value="<?= htmlspecialchars($carrera['duracion_anios']) ?>">
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">
                <button type="submit" class="btn-alta">Guardar cambios</button>
                <a href="carreras.php" class="btn-cancelar">Cancelar</a>
            </td>
        </tr>
    </table>
</form>
<?php else: ?>
    <p style="text-align:center; color:red;">Carrera no encontrada.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>
