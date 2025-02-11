<?php
session_name("Tienda");
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <form action="" method="post" class="formulario-iniciar">
        <label for="correo">Correo Electrónico:</label>
        <input type="email" id="correo" name="email" required>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="contrasena" required>
        <button type="submit" class="btn-iniciar">Iniciar Sesión</button>

    </form>
    <div class="formulario">
        <label for="iniciar-sesion">¿No tienes cuenta?</label>
        <a href="registrar.php"><button type="submit" class="btn-registrar">Registrate</button></a>
    </div>
    <?php

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $errores = [];

        $email = isset($_POST["email"]) ? $_POST["email"] : '';
        $contrasena = isset($_POST["contrasena"]) ? $_POST["contrasena"] : '';


        try {
            include 'connect.php';

            $query = "SELECT * FROM clientes WHERE email = ?";
            $stmt = $pdo->prepare($query);

            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {

                $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!password_verify($contrasena, $cliente['contrasena'])) {
                    $errores[] = "contraseña incorrecta";
                }
            } else {
                $errores[] = "El correo no esta registrado";
            }

            if (empty($errores)) {
                $_SESSION['cliente_id'] = $cliente['cliente_id'];
                $_SESSION['email'] = $cliente['email'];
                $_SESSION['usuario'] = $cliente['nombre'];
                header("Location: index.php");
                exit();
            } else {
                echo '<div class="errores">';
                foreach ($errores as $error) {
                    echo "<p>$error</p>";
                }
                echo '</div>';
            }

        } catch (PDOException $e) {
            echo '<div class="errores">';
            echo "Error: " . $e->getMessage();
            echo '</div>';
        }
    }
    ?>




</body>

</html>