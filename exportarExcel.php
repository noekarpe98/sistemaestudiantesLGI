<?php
include 'conexion.php';
include 'funciones.php';

// 🔹 Capturar filtros
$filtro_materia = isset($_GET['materia']) ? intval($_GET['materia']) : 0;
$filtro_estado  = $_GET['estado'] ?? 'todos';
$filtro_carrera = isset($_GET['carrera']) ? intval($_GET['carrera']) : 0;

// 🔹 Traer estudiantes con filtros
$sql = "SELECT a.id_alumno, a.nombre, a.apellido, c.nombre AS carrera
        FROM alumnos a
        LEFT JOIN carreras c ON a.id_carrera = c.id_carrera
        WHERE 1=1";
if ($filtro_carrera > 0) $sql .= " AND a.id_carrera = $filtro_carrera";

$result = $conn->query($sql);
$estudiantes = [];

if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $id = $row['id_alumno'];
        $estudiantes[$id] = [
            'nombre'=>$row['nombre'].' '.$row['apellido'],
            'carrera'=>$row['carrera'],
            'notas'=>[]
        ];

        $sql_notas = "SELECT n.nota1,n.nota2,n.nota3,m.nombre AS materia
                      FROM notas n
                      INNER JOIN materias m ON n.id_materia = m.id_materia
                      WHERE n.id_alumno = $id";
        if($filtro_materia > 0) $sql_notas .= " AND m.id_materia=$filtro_materia";

        $res_notas = $conn->query($sql_notas);
        while($n = $res_notas->fetch_assoc()){
            $estudiantes[$id]['notas'][]=$n;
        }
    }
}

// 🔹 Cabeceras para Excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=reportes.xls");

// 🔹 Tabla con formato
echo '<table border="1" cellpadding="5" cellspacing="0">';
echo '<tr style="background:#4CAF50; color:white; font-weight:bold;">
        <th>Nombre</th>
        <th>Carrera</th>
        <th>Materia</th>
        <th>Nota1</th>
        <th>Nota2</th>
        <th>Nota3</th>
        <th>Promedio</th>
      </tr>';

foreach($estudiantes as $est){
    foreach($est['notas'] as $n){
        $prom = calcularPromedio([$n['nota1'],$n['nota2'],$n['nota3']]);

        if($filtro_estado=='aprobados' && $prom<6) continue;
        if($filtro_estado=='desaprobados' && $prom>=6) continue;

        echo "<tr>
                <td>{$est['nombre']}</td>
                <td>{$est['carrera']}</td>
                <td>{$n['materia']}</td>
                <td>{$n['nota1']}</td>
                <td>{$n['nota2']}</td>
                <td>{$n['nota3']}</td>
                <td style='font-weight:bold;'>".number_format($prom,2)."</td>
              </tr>";
    }
}

echo '</table>';
exit;
