<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GameZone</title>

    <!-- Enlace al archivo CSS principal (estilos generales) -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Enlace al archivo CSS específico del login -->
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <h1>Iniciar Sesión</h1>
        <form action="login.php" method="POST">
            <label for="username">Usuario:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Ingresar</button>
        </form>

        <!-- Aquí puedes agregar enlaces a registrar o recuperar contraseña -->
    </div>

    <?php
        // Diccionario de usuarios y contraseñas (simple, para pruebas)
        $users = [
            "admin" => "admin",
            "user2" => "password2",
            "user3" => "password3",
            "user4" => "password4",
            "user5" => "password5"
        ];

        // Verificar si se ha enviado el formulario
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Verificar si el usuario existe y la contraseña es correcta
            if (array_key_exists($username, $users) && $users[$username] == $password) {
                // Iniciar sesión
                session_start();
                $_SESSION['username'] = $username;
                header("Location: index.php"); // Redirigir a la página principal
                exit();
            } else {
                echo "<p style='color: red;'>Usuario o contraseña incorrectos</p>";
            }
        }
    ?>
</body>
</html>
