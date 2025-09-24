<?php
include 'conexion.php';
include 'header.php';

// Obtener término de búsqueda si existe
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : "";

// Obtener filtro de profesor si existe
$filtro_profesor = isset($_GET['profesor']) ? intval($_GET['profesor']) : 0;

// Obtener listado de profesores para el filtro
$profesores_result = $conn->query("SELECT id_profesor, nombre, apellido FROM profesores ORDER BY nombre ASC");
$profesores = [];
while ($p = $profesores_result->fetch_assoc()) {
    $profesores[$p['id_profesor']] = $p['nombre'] . ' ' . $p['apellido'];
}

// Construir consulta de materias con JOIN profesor
$sql = "SELECT m.id_materia, m.nombre AS materia, 
               p.nombre AS profesor_nombre, p.apellido AS profesor_apellido
        FROM materias m
        LEFT JOIN profesores p ON m.id_profesor = p.id_profesor";

$params = [];
$types = "";
$where = [];

if ($buscar !== "") {
    $where[] = "(m.nombre LIKE ? OR p.nombre LIKE ? OR p.apellido LIKE ?)";
    $buscar_like = "%$buscar%";
    $params[] = $buscar_like;
    $params[] = $buscar_like;
    $params[] = $buscar_like;
    $types .= "sss";
}

if ($filtro_profesor > 0) {
    $where[] = "m.id_profesor = ?";
    $params[] = $filtro_profesor;
    $types .= "i";
}

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY m.nombre ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>


<!-- Header Materias: filtro + buscador + alta -->
<div class="header-materias">
    <form method="GET" class="buscar-materias">
        <!-- Filtro por profesor -->
        <select name="profesor" onchange="this.form.submit()">
            <option value="0">Todos los profesores</option>
            <?php foreach($profesores as $id => $nombre): ?>
                <option value="<?= $id ?>" <?= $filtro_profesor == $id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($nombre) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Buscador por materia -->
        <input type="text" name="buscar" placeholder="Buscar por materia o profesor" 
               value="<?= htmlspecialchars($buscar) ?>">

        <!-- Botón Buscar -->
        <button type="submit">Buscar</button>
    </form>

    <!-- Botón Agregar Materia -->
    <a href="alta_materias.php"><button class="btn-alta">Agregar Materia</button></a>
</div>

<table border="1" style="width:100%; border-collapse: collapse;">
    <tr>
        <th>Materia</th>
        <th>Profesor</th>
        <th>Acciones</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['materia']) ?></td>
            <td><?= htmlspecialchars($row['profesor_nombre'] . ' ' . $row['profesor_apellido']) ?></td>
            <td>
                <a href="modificar_materias.php?id=<?= $row['id_materia'] ?>" class="btn-modificar"><i class="fas fa-edit"></i></a>
                <a href="eliminar_materias.php?id=<?= $row['id_materia'] ?>" class="btn-borrar"><i class="fas fa-trash"></i></a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php include 'footer.php'; ?>





