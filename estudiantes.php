<?php
include 'conexion.php';
include 'header.php';

// Obtener todos los estudiantes con DNI
$sql = "SELECT a.id_alumno, a.nombre, a.apellido, a.dni, a.fecha_nacimiento, a.email, c.nombre AS carrera
        FROM alumnos a
        LEFT JOIN carreras c ON a.id_carrera = c.id_carrera";
$result = $conn->query($sql);

$estudiantes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['id_alumno'];
        $estudiantes[$id] = [
            'dni' => $row['dni'],
            'nombre' => $row['nombre'] . ' ' . $row['apellido'],
            'edad' => date_diff(date_create($row['fecha_nacimiento']), date_create('today'))->y,
            'email' => $row['email'],
            'carrera' => $row['carrera']
        ];
    }
}
?>
<div class="header-estudiantes">
    <!-- Buscador -->
    <form method="GET" class="buscar-alumno">
        <div class="input-buscador">
            <input type="text" name="buscar" placeholder="Buscar por nombre o DNI" 
                   value="<?= htmlspecialchars($buscar) ?>">
            <button type="submit">Buscar</button>
        </div>
    </form>

    <!-- Botón Agregar Alumno -->
    <a href="alta_estudiante.php"><button class="btn-azul">Agregar Alumno</button></a>
</div>


<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Nombre</th>
        <th>DNI</th>
        <th>Edad</th>
        <th>Correo</th>
        <th>Carrera</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($estudiantes as $id => $est): ?>
    <tr>
        <td><?= htmlspecialchars($est['nombre']) ?></td>
        <td><?= htmlspecialchars($est['dni']) ?></td>
        <td><?= htmlspecialchars($est['edad']) ?></td>
        <td><?= htmlspecialchars($est['email']) ?></td>
        <td><?= htmlspecialchars($est['carrera']) ?></td>
        <td>
            <a href="modificar_estudiante.php?id=<?= $id ?>">
                <button class="btn-modificar"><i class="fas fa-edit"></i></button>
            </a>
            <a href="eliminar_estudiante.php?id=<?= $id ?>">
                <button class="btn-borrar"><i class="fas fa-trash"></i></button>
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>


<?php include 'footer.php'; ?>


