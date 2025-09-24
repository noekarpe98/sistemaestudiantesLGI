<?php
include 'conexion.php';
include 'header.php';
include 'funciones.php';

// 🔹 Obtener materias y carreras
$materias_result = $conn->query("SELECT id_materia, nombre FROM materias ORDER BY nombre ASC");
$materias = [];
while($m = $materias_result->fetch_assoc()){
    $materias[$m['id_materia']] = $m['nombre'];
}

$carreras_result = $conn->query("SELECT id_carrera, nombre FROM carreras ORDER BY nombre ASC");
$carreras = [];
while($c = $carreras_result->fetch_assoc()){
    $carreras[$c['id_carrera']] = $c['nombre'];
}

// 🔹 Capturar filtros
$filtro_materia = isset($_GET['materia']) ? intval($_GET['materia']) : 0;
$filtro_estado = $_GET['estado'] ?? 'todos';
$filtro_carrera = isset($_GET['carrera']) ? intval($_GET['carrera']) : 0;

// 🔹 Traer estudiantes según filtros
$sql = "SELECT a.id_alumno, a.nombre, a.apellido, c.nombre AS carrera
        FROM alumnos a
        LEFT JOIN carreras c ON a.id_carrera = c.id_carrera
        WHERE 1=1";
if ($filtro_carrera > 0) $sql .= " AND a.id_carrera = $filtro_carrera";

$result = $conn->query($sql);
$estudiantes = [];
if($result->num_rows>0){
    while($row = $result->fetch_assoc()){
        $id = $row['id_alumno'];
        $estudiantes[$id] = [
            'nombre'=>$row['nombre'].' '.$row['apellido'],
            'carrera'=>$row['carrera'],
            'notas'=>[]
        ];

        $sql_notas = "SELECT n.nota1,n.nota2,n.nota3,m.nombre AS materia,m.id_materia
                      FROM notas n
                      INNER JOIN materias m ON n.id_materia = m.id_materia
                      WHERE n.id_alumno = $id";
        if($filtro_materia>0) $sql_notas.=" AND m.id_materia=$filtro_materia";

        $res_notas = $conn->query($sql_notas);
        while($n = $res_notas->fetch_assoc()) $estudiantes[$id]['notas'][]=$n;
    }
}
?>

<!-- 🔹 Header filtros + export -->
<div class="header-reportes" style="display:flex; justify-content: space-between; align-items: center;">
    
    <!-- Filtros (izquierda) -->
    <form method="GET" style="display:flex; gap:10px; align-items:center;">
        <select name="carrera" class="carrera" onchange="this.form.submit()">
            <option value="0">Todas las carreras</option>
            <?php foreach($carreras as $id_c => $nombre_c): ?>
                <option value="<?= $id_c ?>" <?= $filtro_carrera==$id_c?'selected':'' ?>><?= htmlspecialchars($nombre_c) ?></option>
            <?php endforeach; ?>
        </select>

        <select name="materia" class="materia" onchange="this.form.submit()">
            <option value="0">Todas las materias</option>
            <?php foreach($materias as $id_m => $nombre_m): ?>
                <option value="<?= $id_m ?>" <?= $filtro_materia==$id_m?'selected':'' ?>><?= htmlspecialchars($nombre_m) ?></option>
            <?php endforeach; ?>
        </select>

        <select name="estado" class="estado" onchange="this.form.submit()">
            <option value="todos" <?= $filtro_estado=='todos'?'selected':'' ?>>Todos</option>
            <option value="aprobados" <?= $filtro_estado=='aprobados'?'selected':'' ?>>Aprobados</option>
            <option value="desaprobados" <?= $filtro_estado=='desaprobados'?'selected':'' ?>>Desaprobados</option>
        </select>
    </form>

    <!-- Botón Exportar (derecha) -->
    <a href="exportarExcel.php?<?php
        $qs = $_GET; // paso los filtros actuales
        echo http_build_query($qs);
    ?>" class="btn-exportar" style="margin-left: 20px;">Exportar a Excel</a>
</div>


<!-- 🔹 Tabla -->
<table border="1" cellpadding="8" cellspacing="0" style="width:100%; text-align:center;">
    <tr>
        <th>Nombre</th>
        <th>Carrera</th>
        <th>Materia</th>
        <th>Nota1</th>
        <th>Nota2</th>
        <th>Nota3</th>
        <th>Promedio</th>
    </tr>
    <?php foreach($estudiantes as $est): ?>
        <?php foreach($est['notas'] as $n):
            $prom = calcularPromedio([$n['nota1'],$n['nota2'],$n['nota3']]);
            if($filtro_estado=='aprobados' && $prom<6) continue;
            if($filtro_estado=='desaprobados' && $prom>=6) continue;
        ?>
        <tr>
            <td><?= htmlspecialchars($est['nombre']) ?></td>
            <td><?= htmlspecialchars($est['carrera']) ?></td>
            <td><?= htmlspecialchars($n['materia']) ?></td>
            <td><?= htmlspecialchars($n['nota1']) ?></td>
            <td><?= htmlspecialchars($n['nota2']) ?></td>
            <td><?= htmlspecialchars($n['nota3']) ?></td>
            <td><?= number_format($prom,2) ?></td>
        </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
</table>

<?php include 'footer.php'; ?>





