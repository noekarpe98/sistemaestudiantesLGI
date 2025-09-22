<?php
include 'conexion.php';
include 'header.php';

$mensaje = "";
$materia = null;

// 🔹 Obtener la materia desde la URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT id_materia, nombre FROM materias WHERE id_materia = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $materia = $result->fetch_assoc();
    $stmt->close();
}

// 🔹 Eliminar materia si confirma
if (isset($_POST['eliminar_id'])) {
    $id_materia = intval($_POST['eliminar_id']);
    $stmt = $conn->prepare("DELETE FROM materias WHERE id_materia = ?");
    $stmt->bind_param("i", $id_materia);

    if ($stmt->execute()) {
        echo "<p style='color:green; text-align:center;'>Materia eliminada correctamente.</p>";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'materias.php';
                }, 2000);
              </script>";
        $materia = null;
    } else {
        echo "<p style='color:red; text-align:center;'>Error al eliminar: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

echo $mensaje;
?>

<?php if ($materia): ?>
    <!-- 🔹 Modal de confirmación -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <p class="modal-text">
                ¿Realmente desea eliminar la materia: 
                <strong><?= htmlspecialchars($materia['nombre']) ?></strong>?
            </p>
            <form method="POST">
                <input type="hidden" name="eliminar_id" value="<?= $materia['id_materia'] ?>">
                <button type="submit" class="btn-borrar">Sí, eliminar</button>
                <button type="button" id="cancelBtn" class="btn-cancelar">Cancelar</button>
            </form>
        </div>
    </div>

    <script>
        var modal = document.getElementById('confirmModal');

        // Botón cancelar vuelve a la lista
        document.getElementById("cancelBtn").onclick = function() {
            window.location.href = 'materias.php';
        }

        // Cerrar modal si clic fuera
        window.onclick = function(event) {
            if (event.target == modal) {
                window.location.href = 'materias.php';
            }
        }
    </script>
<?php endif; ?>

<?php include 'footer.php'; ?>
