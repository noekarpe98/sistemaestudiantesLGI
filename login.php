<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'conexion.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['username'];
    $password = $_POST['password'];

    // Consulta usando la tabla y columnas correctas
    $stmt = $conn->prepare("SELECT id, username, password FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Comparación simple si la contraseña no está hasheada
        if ($password === $row['password']) {
            $_SESSION['id_usuario'] = $row['id'];
            $_SESSION['username'] = $row['username'];

            // Redirige al dashboard
            header("Location: index.php");
            exit;
        } else {
            $mensaje = "Usuario o contraseña incorrecta.";
        }
    } else {
        $mensaje = "Usuario o contraseña incorrecta.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Estudiantes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if ($mensaje) echo "<p class='mensaje-error'>$mensaje</p>"; ?>
        <form method="POST" class="form-login">
            <input type="text" name="username" placeholder="Usuario" required class="input-login">
            <input type="password" name="password" placeholder="Contraseña" required class="input-login">
            <button type="submit" class="btn-azul">Ingresar</button>
        </form>
    </div>
</body>
</html>




