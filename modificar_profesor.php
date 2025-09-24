<?php
include 'conexion.php';
include 'header.php';

$mensaje = "";
$profesor = null;

// Obtener profesor
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM profesores WHERE id_profesor = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $profesor = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_id'])) {
    $id = intval($_POST['actualizar_id']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);

    if ($nombre === "" || $apellido === "" || $email === "") {
        $mensaje = "<p style='color:red; text-align:center;'>Todos los campos son obligatorios.</p>";
    } else {
        $stmt = $conn->prepare("UPDATE profesores SET nombre=?, apellido=?, email=? WHERE id_profesor=?");
        $stmt->bind_param("sssi", $nombre, $apellido, $email, $id);

        if ($stmt->execute()) {
            $mensaje = "<p style='color:green; text-align:center;'>Profesor actualizado correctamente.</p>";
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'profesores.php';
                    }, 2000);
                  </script>";
        } else {
            $mensaje = "<p style='color:red; text-align:center;'>Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
}
?>

<h2>Modificar Profesor</h2>
<?php if ($mensaje) echo $mensaje; ?>

<?php if ($profesor): ?>
<form method="POST" class="form-alta">
    <input type="hidden" name="actualizar_id" value="<?= $profesor['id_profesor'] ?>">
    <table>
        <tr>
            <th>Nombre</th>
            <td><input type="text" name="nombre" required value="<?= htmlspecialchars($profesor['nombre']) ?>"></td>
        </tr>
        <tr>
            <th>Apellido</th>
            <td><input type="text" name="apellido" required value="<?= htmlspecialchars($profesor['apellido']) ?>"></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><input type="email" name="email" required value="<?= htmlspecialchars($profesor['email']) ?>"></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align:center;">
                <button type="submit" class="btn-alta">Guardar cambios</button>
                <a href="profesores.php" class="btn-cancelar">Cancelar</a>
            </td>
        </tr>
    </table>
</form>
<?php else: ?>
<p style="text-align:center; color:red;">Profesor no encontrado.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>
