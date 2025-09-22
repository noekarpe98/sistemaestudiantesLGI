<?php
include 'conexion.php';
include 'header.php';

// Listar materias con su profesor
$sql = "SELECT m.id_materia, m.nombre AS materia, 
               p.nombre AS profesor_nombre, p.apellido AS profesor_apellido
        FROM materias m
        LEFT JOIN profesores p ON m.id_profesor = p.id_profesor";
$result = $conn->query($sql);
?>

<h2>Materias</h2>

<!-- Botón Agregar Materia -->
<div style="margin-bottom: 20px;">
    <a href="alta_materias.php" class="btn-alta">Agregar Materia</a>
</div>

<table border="1" style="width:100%; border-collapse: collapse;">
    <tr>
        <th>Materia</th>
        <th>Profesor</th>
        <th>Acciones</th>
    </tr>
    <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= htmlspecialchars($row['materia']) ?></td>
            <td><?= htmlspecialchars($row['profesor_nombre'] . ' ' . $row['profesor_apellido']) ?></td>
            <td>
                <!-- Botón Editar -->
                <a href="modificar_materias.php?id=<?= $row['id_materia'] ?>" class="btn-modificar">Modificar</a>

                <!-- Botón Eliminar -->
                <a href="eliminar_materias.php?id=<?= $row['id_materia'] ?>" class="btn-borrar">Eliminar</a>
            </td>
        </tr>
    <?php } ?>
</table>

<?php include 'footer.php'; ?>



