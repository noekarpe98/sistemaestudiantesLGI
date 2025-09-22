<?php

session_start(); // iniciar sesión

// Solo verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

include 'conexion.php';
include 'header.php';
include 'funciones.php';

// Obtener todos los estudiantes con sus notas
$sql = "SELECT a.id_alumno, a.nombre, a.apellido, a.fecha_nacimiento, c.nombre AS carrera
        FROM alumnos a
        LEFT JOIN carreras c ON a.id_carrera = c.id_carrera";
$result = $conn->query($sql);

$estudiantes = [];
$promedioPorCarrera = [];
$aprobados = 0;
$desaprobados = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['id_alumno'];
        $estudiantes[$id] = [
            'nombre' => $row['nombre'] . ' ' . $row['apellido'],
            'edad' => date_diff(date_create($row['fecha_nacimiento']), date_create('today'))->y,
            'carrera' => $row['carrera'],
            'notas' => []
        ];

        // Notas de cada estudiante
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

        // Promedio individual
        $promedio = calcularPromedio($estudiantes[$id]['notas']);

        // Promedio por carrera
        if ($row['carrera']) {
            if (!isset($promedioPorCarrera[$row['carrera']])) {
                $promedioPorCarrera[$row['carrera']] = ['suma' => 0, 'count' => 0];
            }
            $promedioPorCarrera[$row['carrera']]['suma'] += $promedio;
            $promedioPorCarrera[$row['carrera']]['count']++;
        }

        // Aprobados vs desaprobados
        if ($promedio >= 6) {
            $aprobados++;
        } else {
            $desaprobados++;
        }
    }
}

// Calcular resumen
$total = count($estudiantes);
$sumaPromedios = 0;
foreach ($estudiantes as $est) {
    $sumaPromedios += calcularPromedio($est['notas']);
}
$promedioGeneral = ($total > 0) ? $sumaPromedios / $total : 0;
?>

<div class="summary">
    <h2>Resumen General</h2>
    <p>Total de estudiantes: <?= $total ?></p>
    <p>Promedio general: <?= number_format($promedioGeneral, 2) ?></p>

    <div class="charts-row">
        <!-- Promedio por carrera -->
        <div class="chart-box">
            <h3>Promedio por Carrera</h3>
            <canvas id="chartCarrera"></canvas>
        </div>

        <!-- Aprobados vs desaprobados -->
        <div class="chart-box">
            <h3>Aprobados vs Desaprobados</h3>
            <canvas id="chartAprobados"></canvas>
        </div>

        <!-- Promedio general -->
        <div class="chart-box">
            <h3>Promedio General</h3>
            <canvas id="chartPromedio"></canvas>
        </div>
    </div>
</div>

<!-- Librería Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Datos desde PHP
const dataCarreras = <?= json_encode(array_keys($promedioPorCarrera)) ?>;
const dataPromedios = <?= json_encode(array_map(fn($c) => $c['suma'] / $c['count'], $promedioPorCarrera)) ?>;
const aprobados = <?= $aprobados ?>;
const desaprobados = <?= $desaprobados ?>;
const promedioGeneral = <?= number_format($promedioGeneral, 2) ?>;

// Gráfico: Promedio por carrera
new Chart(document.getElementById('chartCarrera'), {
    type: 'bar',
    data: {
        labels: dataCarreras,
        datasets: [{
            label: 'Promedio',
            data: dataPromedios,
            backgroundColor: '#42a5f5'
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

// Gráfico: Aprobados vs desaprobados
new Chart(document.getElementById('chartAprobados'), {
    type: 'doughnut',
    data: {
        labels: ['Aprobados', 'Desaprobados'],
        datasets: [{
            data: [aprobados, desaprobados],
            backgroundColor: ['#4caf50', '#e53935']
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

// Gráfico: Promedio general
new Chart(document.getElementById('chartPromedio'), {
    type: 'line',
    data: {
        labels: ['Promedio General'],
        datasets: [{
            label: 'Promedio',
            data: [promedioGeneral],
            borderColor: '#ff9800',
            backgroundColor: '#ffcc80',
            fill: true
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});
</script>

<?php include 'footer.php'; ?>




