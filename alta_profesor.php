<?php
include 'conexion.php';
include 'header.php';

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);

    if ($nombre === "" || $apellido === "" || $email === "") {
        $mensaje = "<p style='color:red; text-align:center;'>Todos los campos son obligatorios.</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO profesores (nombre, apellido, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $apellido, $email);

        if ($stmt->execute()) {
            $mensaje = "<p style='color:green; text-align:center;'>Profesor agregado correctamente.</p>";
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

<h2>Alta de Profesor</h2>
<?php if ($mensaje) echo $mensaje; ?>

<form method="POST" class="form-alta">
    <table style="width:100%; border-collapse: collapse; background:#fff; box-shadow: 0 3px 6px rgba(0,0,0,0.15);">
        <tr>
            <th>Nombre</th>
            <td><input type="text" name="nombre" required></td>
        </tr>
        <tr>
            <th>Apellido</th>
            <td><input type="text" name="apellido" required></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><input type="email" name="email" required></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align:center; padding:15px;">
                <button type="submit" class="btn-alta">Guardar</button>
                <a href="profesores.php" class="btn-cancelar">Cancelar</a>
            </td>
        </tr>
    </table>
</form>

<?php include 'footer.php'; ?>
