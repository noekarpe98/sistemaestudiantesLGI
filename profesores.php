<?php
include 'conexion.php';
include 'header.php';

// Buscar
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : "";

// Consulta base
$sql = "SELECT id_profesor, nombre, apellido, email FROM profesores";
$params = [];
$types = [];
$where = [];

if ($buscar !== "") {
    $where[] = "(nombre LIKE ? OR apellido LIKE ? OR email LIKE ?)";
    $buscar_like = "%$buscar%";
    $params = [$buscar_like, $buscar_like, $buscar_like];
    $types = ["s","s","s"];
}

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY apellido, nombre ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param(implode("", $types), ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="header-materias">
    <form method="GET" class="buscar-materias">
        <input type="text" name="buscar" placeholder="Buscar por nombre, apellido o email" 
               value="<?= htmlspecialchars($buscar) ?>">
        <button type="submit">Buscar</button>
    </form>

    <a href="alta_profesor.php"><button class="btn-alta">Agregar Profesor</button></a>
</div>

<table border="1" style="width:100%; border-collapse: collapse;">
    <tr>
        <th>Apellido</th>
        <th>Nombre</th>
        <th>Email</th>
        <th>Acciones</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['apellido']) ?></td>
            <td><?= htmlspecialchars($row['nombre']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td>
                <a href="modificar_profesor.php?id=<?= $row['id_profesor'] ?>" class="btn-modificar"><i class="fas fa-edit"></i></a>
                <a href="eliminar_profesor.php?id=<?= $row['id_profesor'] ?>" class="btn-borrar"><i class="fas fa-trash"></i></a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php include 'footer.php'; ?>
