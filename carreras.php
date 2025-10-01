<?php
include 'conexion.php';
include 'header.php';

// Determinar rol del usuario
$rol = $_SESSION['rol'] ?? '';
$solo_lectura = ($rol !== 'admin'); // solo admin puede modificar/agregar/borrar

// Obtener término de búsqueda si existe
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : "";

// Obtener filtro de duración si existe
$filtro_duracion = isset($_GET['duracion_anios']) ? intval($_GET['duracion_anios']) : 0;

// Obtener listado de duraciones distintas para el filtro
$duraciones_result = $conn->query("SELECT DISTINCT duracion_anios FROM carreras ORDER BY duracion_anios ASC");
$duraciones = [];
while ($d = $duraciones_result->fetch_assoc()) {
    $duraciones[] = $d['duracion_anios'];
}

// Construir consulta de carreras
$sql = "SELECT c.id_carrera, c.nombre AS carrera, c.duracion_anios FROM carreras c";
$params = [];
$types = [];
$where = [];

if ($buscar !== "") {
    $where[] = "c.nombre LIKE ?";
    $buscar_like = "%$buscar%";
    $params[] = $buscar_like;
    $types[] = "s";
}

if ($filtro_duracion > 0) {
    $where[] = "c.duracion_anios = ?";
    $params[] = $filtro_duracion;
    $types[] = "i";
}

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY c.nombre ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param(implode("", $types), ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!-- Header Carreras: filtro + buscador + alta -->
<div class="header-materias">
    <form method="GET" class="buscar-materias">
        <!-- Filtro por duración -->
        <select name="duracion_anios" onchange="this.form.submit()">
            <option value="0">Todas las duraciones</option>
            <?php foreach($duraciones as $dur): ?>
                <option value="<?= $dur ?>" <?= $filtro_duracion == $dur ? 'selected' : '' ?>>
                    <?= htmlspecialchars($dur) ?> años
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Buscador por carrera -->
        <input type="text" name="buscar" placeholder="Buscar por carrera" 
               value="<?= htmlspecialchars($buscar) ?>">

        <!-- Botón Buscar -->
        <button type="submit">Buscar</button>
    </form>

    <!-- Botón Agregar Carrera solo para admin -->
    <?php if (!$solo_lectura): ?>
        <a href="alta_carreras.php"><button class="btn-alta">Agregar Carrera</button></a>
    <?php endif; ?>
</div>

<table border="1" style="width:100%; border-collapse: collapse;">
    <tr>
        <th>Carrera</th>
        <th>Duración (años)</th>
        <?php if (!$solo_lectura): ?>
            <th>Acciones</th>
        <?php endif; ?>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['carrera']) ?></td>
            <td style="text-align: left;"><?= htmlspecialchars($row['duracion_anios']) ?></td>
            <?php if (!$solo_lectura): ?>
                <td>
                    <a href="modificar_carreras.php?id=<?= $row['id_carrera'] ?>" class="btn-modificar"><i class="fas fa-edit"></i></a>
                    <a href="eliminar_carreras.php?id=<?= $row['id_carrera'] ?>" class="btn-borrar"><i class="fas fa-trash"></i></a>
                </td>
            <?php endif; ?>
        </tr>
    <?php endwhile; ?>
</table>

<?php include 'footer.php'; ?>

