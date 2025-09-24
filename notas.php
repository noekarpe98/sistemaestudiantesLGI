<?php
include 'conexion.php';
include 'header.php';
include 'funciones.php';

// Obtener término de búsqueda si existe
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : "";

// Obtener filtro de materia si existe
$filtro_materia = isset($_GET['materia']) ? intval($_GET['materia']) : 0;

// Obtener todas las materias para el filtro
$materias_result = $conn->query("SELECT id_materia, nombre FROM materias ORDER BY nombre ASC");
$materias = [];
while ($m = $materias_result->fetch_assoc()) {
    $materias[$m['id_materia']] = $m['nombre'];
}

// Construir consulta SQL para alumnos
$sql = "SELECT a.id_alumno, a.nombre, a.apellido, a.dni, c.nombre AS carrera 
        FROM alumnos a 
        LEFT JOIN carreras c ON a.id_carrera = c.id_carrera";

$params = [];
$types = "";

if ($buscar !== "") {
    $sql .= " WHERE a.nombre LIKE ? OR a.apellido LIKE ? OR a.dni LIKE ?";
    $buscar_like = "%$buscar%";
    $params = [$buscar_like, $buscar_like, $buscar_like];
    $types = "sss";
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$estudiantes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['id_alumno'];
        $estudiantes[$id] = [
            'nombre' => $row['nombre'] . ' ' . $row['apellido'],
            'carrera' => $row['carrera'],
            'dni' => $row['dni'],
            'notas' => []
        ];

        // Obtener notas del alumno, incluyendo id_materia
        $sql_notas = "SELECT n.id_materia, n.nota1, n.nota2, n.nota3, m.nombre AS materia 
                      FROM notas n
                      INNER JOIN materias m ON n.id_materia = m.id_materia
                      WHERE n.id_alumno = ?";
        if ($filtro_materia > 0) {
            $sql_notas .= " AND n.id_materia = $filtro_materia";
        }

        $stmt_notas = $conn->prepare($sql_notas);
        $stmt_notas->bind_param("i", $id);
        $stmt_notas->execute();
        $res_notas = $stmt_notas->get_result();

        $notas = [];
        while ($nr = $res_notas->fetch_assoc()) {
            $notas[] = $nr;
        }
        $estudiantes[$id]['notas'] = $notas;
        $stmt_notas->close();
    }
}
?>

<div class="header-notas">

    <form method="GET" class="buscar-notas">
        <select name="materia" id="materia" onchange="this.form.submit()">
            <option value="0">Todas</option>
            <?php foreach ($materias as $id_m => $nombre_m): ?>
                <option value="<?= $id_m ?>" <?= $filtro_materia == $id_m ? 'selected' : '' ?>>
                    <?= htmlspecialchars($nombre_m) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="text" name="buscar" placeholder="Buscar por nombre o DNI" value="<?= htmlspecialchars($buscar) ?>">

        <button type="submit">Buscar</button>
    </form>

    <!-- Botón Agregar Nota -->
    <a href="alta_notas.php"><button class="btn-azul">Agregar Nota Nueva</button></a>
</div>


<table border="1" cellpadding="8" cellspacing="0" style="width:100%; text-align:center;">
    <tr>
        <th>Nombre</th>
        <th>DNI</th>
        <th>Carrera</th>
        <th>Materia</th>
        <th>Nota 1</th>
        <th>Nota 2</th>
        <th>Nota 3</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($estudiantes as $id => $est): ?>
        <?php foreach ($est['notas'] as $n): ?>
            <tr>
                <td><?= htmlspecialchars($est['nombre']) ?></td>
                <td><?= htmlspecialchars($est['dni']) ?></td>
                <td><?= htmlspecialchars($est['carrera']) ?></td>
                <td><?= htmlspecialchars($n['materia']) ?></td>
                <td><?= htmlspecialchars($n['nota1']) ?></td>
                <td><?= htmlspecialchars($n['nota2']) ?></td>
                <td><?= htmlspecialchars($n['nota3']) ?></td>
                <td>
                    <a class="btn btn-inspeccionar" 
                       href="modificar_notas.php?id_alumno=<?= $id ?>&id_materia=<?= $n['id_materia'] ?>"><i class="fas fa-edit"></i></a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
</table>


<?php include 'footer.php'; ?>





