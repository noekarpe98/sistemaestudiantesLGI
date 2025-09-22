<?php
include 'conexion.php';
include 'header.php';

$mensaje = "";
$alumno = null;

// Obtener el id del alumno desde la URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT id_alumno, nombre, apellido FROM alumnos WHERE id_alumno = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $alumno = $result->fetch_assoc();
    $stmt->close();
}

// Eliminar alumno si confirma
if (isset($_POST['eliminar_id'])) {
    $id_alumno = intval($_POST['eliminar_id']);
    $stmt = $conn->prepare("DELETE FROM alumnos WHERE id_alumno = ?");
    $stmt->bind_param("i", $id_alumno);
    if ($stmt->execute()) {
        echo "<p style='color:green; text-align:center;'>Alumno eliminado correctamente.</p>";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'estudiantes.php';
                }, 2000);
              </script>";
        $alumno = null;
    } else {
        echo "<p style='color:red; text-align:center;'>Error al eliminar: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

echo $mensaje;
?>

<?php if ($alumno): ?>
    <!-- Modal de confirmación -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <p class="modal-text">
                ¿Realmente desea eliminar al alumno: 
                <strong><?= htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellido']) ?></strong>?
            </p>
            <form method="POST">
                <input type="hidden" name="eliminar_id" value="<?= $alumno['id_alumno'] ?>">
                <button type="submit" class="btn-borrar">Sí, eliminar</button>
                <button type="button" id="cancelBtn" class="btn-cancelar">Cancelar</button>
            </form>
        </div>
    </div>

    <script>
        var modal = document.getElementById('confirmModal');

        // Cerrar modal con Cancelar
        document.getElementById("cancelBtn").onclick = function() {
            window.location.href = 'estudiantes.php';
        }

        // Cerrar modal si clic afuera
        window.onclick = function(event) {
            if (event.target == modal) {
                window.location.href = 'estudiantes.php';
            }
        }
    </script>
<?php endif; ?>

<?php include 'footer.php'; ?>
