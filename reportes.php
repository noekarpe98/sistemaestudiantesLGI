<?php
include 'conexion.php';
include 'header.php';
include 'funciones.php';

// 1️⃣ Obtener todos los estudiantes con su carrera
$sql = "SELECT a.id_alumno, a.nombre, a.apellido, c.nombre AS carrera
        FROM alumnos a
        LEFT JOIN carreras c ON a.id_carrera = c.id_carrera";
$result = $conn->query($sql);

$estudiantes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['id_alumno'];
        $estudiantes[$id] = [
            'nombre' => $row['nombre'] . ' ' . $row['apellido'],
            'carrera' => $row['carrera'],
            'notas' => []
        ];

        // Obtener notas de cada estudiante
        $sql_notas = "SELECT nota1, nota2, nota3 FROM notas WHERE id_alumno = $id";
        $res_notas = $conn->query($sql_notas);
        if ($res_notas->num_rows > 0) {
            $notas_row = $res_notas->fetch_assoc();
            $estudiantes[$id]['notas'] = [
                $notas_row['nota1'],
                $notas_row['nota2'],
                $notas_row['nota3']
            ];
        }
    }
}

// 2️⃣ Obtener filtro seleccionado
$filtro = $_GET['filtro'] ?? 'todos';

// 3️⃣ Calcular mejor estudiante según filtro
$mejorPromedio = 0;
$mejorEstudiante = "";
foreach ($estudiantes as $est) {
    $prom = calcularPromedio($est['notas']);

    // Aplicar filtro
    if ($filtro == 'aprobados' && $prom < 6) continue;
    if ($filtro == 'desaprobados' && $prom >= 6) continue;

    if ($prom > $mejorPromedio) {
        $mejorPromedio = $prom;
        $mejorEstudiante = $est['nombre'];
    }
}
?>

<h2>Reporte de Estudiantes</h2>

<!-- Filtro alineado a la derecha -->
<div class="filtro-container" style="display:flex; justify-content:flex-end; margin-bottom:20px;">
    <form method="GET">
        <label for="filtro">Filtrar por:</label>
        <select name="filtro" id="filtro" onchange="this.form.submit()" style="width:400px; height:40px; font-size:14px; padding:5px; border-radius:5px;">
            <option value="todos" <?= ($filtro == 'todos') ? 'selected' : '' ?>>Todos</option>
            <option value="aprobados" <?= ($filtro == 'aprobados') ? 'selected' : '' ?>>Aprobados </option>
            <option value="desaprobados" <?= ($filtro == 'desaprobados') ? 'selected' : '' ?>>Desaprobados </option>
        </select>
    </form>
</div>

<!-- Tabla de estudiantes -->
<table border="1" cellpadding="8" cellspacing="0" style="width:100%; text-align:center;">
    <tr>
        <th>Nombre</th>
        <th>Carrera</th>
        <th>Nota 1</th>
        <th>Nota 2</th>
        <th>Nota 3</th>
        <th>Promedio</th>
    </tr>
    <?php foreach ($estudiantes as $est): 
        $prom = calcularPromedio($est['notas']);

        // Aplicar filtro
        if ($filtro == 'aprobados' && $prom < 6) continue;
        if ($filtro == 'desaprobados' && $prom >= 6) continue;
    ?>
    <tr>
        <td><?= htmlspecialchars($est['nombre']) ?></td>
        <td><?= htmlspecialchars($est['carrera']) ?></td>
        <td><?= isset($est['notas'][0]) ? htmlspecialchars($est['notas'][0]) : "-" ?></td>
        <td><?= isset($est['notas'][1]) ? htmlspecialchars($est['notas'][1]) : "-" ?></td>
        <td><?= isset($est['notas'][2]) ? htmlspecialchars($est['notas'][2]) : "-" ?></td>
        <td><?= number_format($prom, 2) ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- Mejor estudiante -->
<div class="summary" style="margin-top:20px;">
    <h2>Mayor Promedio</h2>
    <?php if($mejorEstudiante): ?>
        <p><strong><?= htmlspecialchars($mejorEstudiante) ?></strong> con promedio de <?= number_format($mejorPromedio, 2) ?></p>
    <?php else: ?>
        <p>No hay estudiantes que cumplan el filtro seleccionado.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>


