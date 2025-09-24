<?php
include 'conexion.php';
include 'header.php';

$mensaje = "";
$carrera = null;

// 🔹 Obtener la carrera desde la URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT id_carrera, nombre FROM carreras WHERE id_carrera = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $carrera = $result->fetch_assoc();
    $stmt->close();
}

// 🔹 Eliminar carrera si confirma
if (isset($_POST['eliminar_id'])) {
    $id_carrera = intval($_POST['eliminar_id']);
    $stmt = $conn->prepare("DELETE FROM carreras WHERE id_carrera = ?");
    $stmt->bind_param("i", $id_carrera);

    if ($stmt->execute()) {
        echo "<p style='color:green; text-align:center;'>Carrera eliminada correctamente.</p>";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'carreras.php';
                }, 2000);
              </script>";
        $carrera = null;
    } else {
        echo "<p style='color:red; text-align:center;'>Error al eliminar: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

echo $mensaje;
?>

<?php if ($carrera): ?>
    <!-- 🔹 Modal de confirmación -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <p class="modal-text">
                ¿Realmente desea eliminar la carrera: 
                <strong><?= htmlspecialchars($carrera['nombre']) ?></strong>?
            </p>
            <form method="POST">
                <input type="hidden" name="eliminar_id" value="<?= $carrera['id_carrera'] ?>">
                <button type="submit" class="btn-borrar">Sí, eliminar</button>
                <button type="button" id="cancelBtn" class="btn-cancelar">Cancelar</button>
            </form>
        </div>
    </div>

    <script>
        var modal = document.getElementById('confirmModal');

        // Botón cancelar vuelve a la lista
        document.getElementById("cancelBtn").onclick = function() {
            window.location.href = 'carreras.php';
        }

        // Cerrar modal si clic fuera
        window.onclick = function(event) {
            if (event.target == modal) {
                window.location.href = 'carreras.php';
            }
        }
    </script>
<?php endif; ?>

<?php include 'footer.php'; ?>
