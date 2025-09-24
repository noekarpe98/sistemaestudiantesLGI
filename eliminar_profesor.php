<?php
include 'conexion.php';
include 'header.php';

$profesor = null;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT id_profesor, nombre, apellido FROM profesores WHERE id_profesor = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $profesor = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if (isset($_POST['eliminar_id'])) {
    $id = intval($_POST['eliminar_id']);
    $stmt = $conn->prepare("DELETE FROM profesores WHERE id_profesor = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<p style='color:green; text-align:center;'>Profesor eliminado correctamente.</p>";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'profesores.php';
                }, 2000);
              </script>";
        $profesor = null;
    } else {
        echo "<p style='color:red; text-align:center;'>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}
?>

<?php if ($profesor): ?>
<div id="confirmModal" class="modal">
    <div class="modal-content">
        <p class="modal-text">
            ¿Realmente desea eliminar al profesor: 
            <strong><?= htmlspecialchars($profesor['apellido'] . ', ' . $profesor['nombre']) ?></strong>?
        </p>
        <form method="POST">
            <input type="hidden" name="eliminar_id" value="<?= $profesor['id_profesor'] ?>">
            <button type="submit" class="btn-borrar">Sí, eliminar</button>
            <button type="button" id="cancelBtn" class="btn-cancelar">Cancelar</button>
        </form>
    </div>
</div>

<script>
    var modal = document.getElementById('confirmModal');

    document.getElementById("cancelBtn").onclick = function() {
        window.location.href = 'profesores.php';
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            window.location.href = 'profesores.php';
        }
    }
</script>
<?php endif; ?>

<?php include 'footer.php'; ?>
