<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>




<form action="" method="POST" class="formulario-registrar">
    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre" required>

    <label for="email">Correo Electrónico:</label>
    <input type="email" id="email" name="email" required>

    <label for="Telefono">Telefono:</label>
    <input type="text" id="Telefono" name="telefono" required>

    <label for="password">Contraseña:</label>
    <input type="password" id="password" name="contraseña" required>

    <label for="confirmar_password">Confirmar Contraseña:</label>
    <input type="password" id="confirmar_password" name="confirmar_contraseña" required>

    <button type="submit" class="btn-registrar" name="Registrar">Registrar</button>


   <p><a href="index.php">Volver a la pagina principal</a></p>
</form>



<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $errores = [];

    $nombre = isset($_POST["nombre"]) ? htmlspecialchars(trim($_POST["nombre"])) : '';
    $email = isset($_POST["email"]) ? $_POST["email"] : '';
    $telefono = isset($_POST["telefono"]) ? htmlspecialchars(trim($_POST["telefono"])) : '';
    $contraseña = isset($_POST["contraseña"]) ? $_POST["contraseña"] : '';
    $confimarContraseña = isset($_POST["confirmar_contraseña"]) ? $_POST["confirmar_contraseña"] : '';

    if(empty($nombre) || empty($email) || empty($telefono) || empty($contraseña) || empty($confimarContraseña)){
        $errores[] = "Todos los campos son requeridos";
    }



    if ($contraseña != $confimarContraseña) {
        $errores[] = "Las contraseñas deben de ser iguales";
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no es válido.";
    }
    
    if (!empty($telefono) && !preg_match('/^[0-9]{9}$/', $telefono)) { 
        $errores[] = "El teléfono debe tener 9 dígitos.";
    }
    
    
if (empty($errores)) {

    try {
        include 'connect.php';

        $hashedPassword = password_hash($contraseña, PASSWORD_DEFAULT);  


        $query = "INSERT INTO clientes ( nombre, email, telefono, contrasena) VALUES (:nombre, :email, :telefono, :contrasena)";
        $statement = $pdo->prepare($query);
        $statement->bindParam(':nombre', $nombre);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':telefono', $telefono);
        $statement->bindParam(':contrasena', $hashedPassword);



        $statement->execute();
        echo '<div class="errores">';

        echo "<p>¡Registro exitoso!</p>";
        echo "<p>¡Inicia sesión para empezar a usar tu cuenta!</p>";
        echo '</div>';

    } catch (PDOException $e) {
        echo '<div class="errores">';
        echo "Error: " . $e->getMessage();
        echo '</div>';
    }

} else {

    foreach ($errores as $error) {
        echo $error;
    }
}
 
}
   


?>


      
</body>
</html>


