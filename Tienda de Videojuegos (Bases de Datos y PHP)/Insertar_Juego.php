<?php
session_name("Tienda");
session_start();
include 'connect.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insertar Producto</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php
    $errores = [];
    $nombreFinal = null; // Inicializamos por seguridad.

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recoger datos del formulario
        $nombre = $_POST['nombre'] ?? null;
        $descripcion = $_POST['descripcion'] ?? null;
        $precio = $_POST['precio'] ?? null;
        $foto = $_FILES['foto'] ?? null;
        $generos = $_POST['generos'] ?? [];
        $plataformas = $_POST['plataformas'] ?? [];

        // Validaciones básicas
        if (!$nombre) {
            $errores[] = 'El nombre no puede estar en blanco';
        }
        if (!$descripcion) {
            $errores[] = 'La descripción no puede estar en blanco';
        }
        if (!$precio) {
            $errores[] = 'El precio no puede estar en blanco';
        }
        if (!$foto) {
            $errores[] = 'La foto no puede estar en blanco';
        } else {
            if ($foto['error'] === 0) {
                $ruta = './imagen/';
                if (!is_dir($ruta)) {
                    mkdir($ruta, 0777, true);
                }
                $imagenInfo = pathinfo($foto['name']);
                if (in_array(strtolower($imagenInfo['extension']), ['jpg', 'png', 'webp', 'jpeg'])) {
                    $nombreFinal = $ruta . uniqid() . '.' . $imagenInfo['extension'];
                    move_uploaded_file($foto['tmp_name'], $nombreFinal);
                } else {
                    $errores[] = 'La extensión de la foto no es válida';
                }
            } else {
                $errores[] = 'Error al subir la foto';
            }
        }

        // Si no hay errores, procesar los datos
        if (empty($errores)) {
            try {
                $pdo->beginTransaction();

                // Insertar nuevo producto
                $statement = $pdo->prepare('
                    INSERT INTO producto (nombre, descripcion, precio, foto) 
                    VALUES (:nombre, :descripcion, :precio, :foto)
                ');
                $statement->execute([
                    'nombre' => $nombre,
                    'descripcion' => $descripcion,
                    'precio' => $precio,
                    'foto' => $nombreFinal
                ]);
                echo 'Producto insertado correctamente.<br>';

                // Obtener el id del nuevo producto
                $id = $pdo->lastInsertId();

                // Insertar géneros relacionados
                foreach ($generos as $genero) {
                    $statement = $pdo->prepare('
                        INSERT INTO genero_juegos (genero_id, producto_id) 
                        VALUES (:genero, :producto)
                    ');
                    $statement->execute([
                        'genero' => $genero,
                        'producto' => $id
                    ]);
                }
                echo 'Géneros insertados correctamente.<br>';

                // Insertar plataformas relacionadas
                foreach ($plataformas as $plataforma) {
                    $statement = $pdo->prepare('
                        INSERT INTO plataforma_juegos (plataforma_id, producto_id) 
                        VALUES (:plataforma, :producto)
                    ');
                    $statement->execute([
                        'plataforma' => $plataforma,
                        'producto' => $id
                    ]);
                }
                echo 'Plataformas insertadas correctamente.<br>';

                $pdo->commit();
                header('Location: index.php');
                exit;
            } catch (Exception $e) {
                $pdo->rollBack();
                echo 'Error: ' . $e->getMessage();
            }
        }
    }
    ?>

    <!-- Mostrar formulario con datos rellenados previamente -->
    <form action="" method="post" enctype="multipart/form-data" class="formulario-Actualizar">
        <label for="nombre">Introduce el nombre del producto</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($nombre ?? ''); ?>">

        <label for="descripcion">Introduce la descripción</label>
        <textarea name="descripcion"><?php echo htmlspecialchars($descripcion ?? ''); ?></textarea>

        <label for="precio">Introduce el precio</label>
        <input type="number" name="precio" step=".01" value="<?php echo htmlspecialchars($precio ?? ''); ?>">

        <label for="foto">Introduce la foto del producto</label>
        <input type="file" name="foto" class="btn-actualizar-juegos">

        <label for="generos">Introduce los géneros del juego</label>
        <select name="generos[]" multiple>
            <?php
            $statement = $pdo->prepare('SELECT * FROM genero');
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $genero) {
                $selected = in_array($genero['genero_id'], $generos ?? []) ? 'selected' : '';
                echo '<option value="' . $genero['genero_id'] . '" ' . $selected . '>' . $genero['nombre'] . '</option>';
            }
            ?>
        </select>

        <label for="plataformas">Introduce las plataformas del juego</label>
        <select name="plataformas[]" multiple>
            <?php
            $statement = $pdo->prepare('SELECT * FROM plataforma');
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $plataforma) {
                $selected = in_array($plataforma['plataforma_id'], $plataformas ?? []) ? 'selected' : '';
                echo '<option value="' . $plataforma['plataforma_id'] . '" ' . $selected . '>' . $plataforma['nombre'] . '</option>';
            }
            ?>
        </select>

        <button type="submit" class="btn-actualizar-juegos">Insertar Producto</button>
    </form>

    <!-- Mostrar errores si existen -->
    <?php if (!empty($errores)): ?>
        <div>
            <?php foreach ($errores as $error): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</body>

</html>
