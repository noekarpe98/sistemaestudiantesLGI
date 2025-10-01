<?php
include 'funciones.php';
verificarRol(['admin', 'profesor']); // Solo acceden admin y profesor
include 'conexion.php';
include 'header.php';

// Buscar alumnos si se pasa parámetro
$buscar = $_GET['buscar'] ?? '';

// Consulta de alumnos con carrera
$sql = "SELECT a.id_alumno, a.nombre, a.apellido, a.dni, a.fecha_nacimiento, a.email, c.nombre AS carrera
        FROM alumnos a
        LEFT JOIN carreras c ON a.id_carrera = c.id_carrera";

if (!empty($buscar)) {
    $buscar = $conn->real_escape_string($buscar);
    $sql .= " WHERE a.nombre LIKE '%$buscar%' 
              OR a.apellido LIKE '%$buscar%' 
              OR a.dni LIKE '%$buscar%'";
}

$result = $conn->query($sql);

$estudiantes = [];
if ($result && $result->num_rows > 0) {
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
            <input type="text" name="buscar" placeholder="Buscar por nombre, apellido o DNI" 
                   value="<?= htmlspecialchars($buscar) ?>">
            <button type="submit">Buscar</button>
        </div>
    </form>

    <!-- Botón Agregar Alumno (solo admin) -->
    <?php if ($_SESSION['rol'] === 'admin'): ?>
        <a href="alta_estudiante.php"><button class="btn-azul">Agregar Alumno</button></a>
    <?php endif; ?>
</div>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Nombre</th>
        <th>DNI</th>
        <th>Edad</th>
        <th>Correo</th>
        <th>Carrera</th>
        <?php if ($_SESSION['rol'] === 'admin'): ?>
            <th>Acciones</th>
        <?php endif; ?>
    </tr>
    <?php if (!empty($estudiantes)): ?>
        <?php foreach ($estudiantes as $id => $est): ?>
        <tr>
            <td><?= htmlspecialchars($est['nombre']) ?></td>
            <td><?= htmlspecialchars($est['dni']) ?></td>
            <td><?= htmlspecialchars($est['edad']) ?></td>
            <td><?= htmlspecialchars($est['email']) ?></td>
            <td style="text-align:left;"><?= htmlspecialchars($est['carrera']) ?></td>
            <?php if ($_SESSION['rol'] === 'admin'): ?>
            <td>
                <a href="modificar_estudiante.php?id=<?= $id ?>">
                    <button class="btn-modificar"><i class="fas fa-edit"></i></button>
                </a>
                <a href="eliminar_estudiante.php?id=<?= $id ?>" onclick="return confirm('¿Seguro que deseas eliminar este alumno?')">
                    <button class="btn-borrar"><i class="fas fa-trash"></i></button>
                </a>
            </td>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="<?= $_SESSION['rol'] === 'admin' ? 6 : 5 ?>" style="text-align:center;">No se encontraron estudiantes</td>
        </tr>
    <?php endif; ?>
</table>


<?php include 'footer.php'; ?>



