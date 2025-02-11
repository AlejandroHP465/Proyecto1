<?php
session_name("Tienda");
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar información de la cuenta</title>
    <link rel="stylesheet" href="style.css">

</head>

<body>

    <form action="" method="POST" class="formulario-Actualizar">

        <label for="nombre">Actualizar Nombre:</label>
        <input type="text" id="nombre" name="nombre">

        <label for="email">Actualizar Correo Electrónico:</label>
        <input type="email" id="email" name="email">

        <label for="Telefono">Actualizar Telefono:</label>
        <input type="text" id="Telefono" name="telefono">


        <label for="password">Actualizar contraseña:</label>
        <input type="password" id="password" name="contraseña">

        <label for="confirmar_password">Confirmar Contraseña:</label>
        <input type="password" id="confirmar_password" name="confirmar_contraseña">


        <button type="submit" class="btn-actualizar-Datos" name="Actualizar">Actualizar Datos</button>

        <p><a href="index.php">Volver a la pagina principal</a></p>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $errores = [];

        $nombre = isset($_POST["nombre"]) ? htmlspecialchars(trim($_POST["nombre"])) : '';
        $email = isset($_POST["email"]) ? $_POST["email"] : '';
        $telefono = isset($_POST["telefono"]) ? htmlspecialchars(trim($_POST["telefono"])) : '';
        $contraseña = isset($_POST["contraseña"]) ? $_POST["contraseña"] : '';
        $confirmarContraseña = isset($_POST["confirmar_contraseña"]) ? $_POST["confirmar_contraseña"] : '';

        if (empty($nombre) || empty($email) || empty($telefono) || empty($contraseña) || empty($confirmarContraseña)) {
            $errores[] = "Todos los campos son requeridos, si hay alguno que quiera dejar igual que como lo tenía antes, escribe lo mismo que tenía antes.";
        }

        if ($contraseña !== $confirmarContraseña) {
            $errores[] = "Las contraseñas deben de ser iguales.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El correo electrónico no es válido.";
        }

        if (!preg_match('/^[0-9]{9}$/', $telefono)) {
            $errores[] = "El teléfono debe tener 9 dígitos.";
        }

        if (empty($errores)) {
            try {
                include 'connect.php';

                // Hash the password for security
                $hashedPassword = password_hash($contraseña, PASSWORD_DEFAULT);

                // Prepare the update query
                $query = "UPDATE clientes 
                      SET nombre = :nombre, email = :email, telefono = :telefono, contrasena = :contrasena
                      WHERE cliente_id = :cliente_id";

                $statement = $pdo->prepare($query);

                // Use existing session values if the input is empty
                $nombreToUpdate = empty($nombre) ? $_SESSION['nombre'] : $nombre;
                $telefonoToUpdate = empty($telefono) ? $_SESSION['telefono'] : $telefono;
                $emailToUpdate = empty($email) ? $_SESSION['email'] : $email;

                // Bind parameters
                $statement->bindParam(':nombre', $nombreToUpdate);
                $statement->bindParam(':email', $emailToUpdate);
                $statement->bindParam(':telefono', $telefonoToUpdate);
                $statement->bindParam(':contrasena', $hashedPassword);
                $statement->bindParam(':cliente_id', $_SESSION['id']);

                // Execute the query
                $statement->execute();

                echo '<div class="errores">';
                echo "¡Datos actualizados correctamente!";
                echo '</div>';

                $_SESSION['usuario'] = $nombreToUpdate;


            } catch (PDOException $e) {
                echo '<div class="errores">';
                echo "Error: " . $e->getMessage();
                echo '</div>';
            }
        } else {
            echo '<div class="errores">';
            foreach ($errores as $error) {
                echo "<p>$error</p>";
            }
            echo '</div>';
        }
    }
    ?>





</body>

</html>