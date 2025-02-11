<?php
ob_start();
session_name("Tienda");
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

if (isset($_POST["cerrar"])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

$carrito = isset($_COOKIE['carrito']) ? unserialize($_COOKIE['carrito']) : [];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Pedido</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Estilos de la tabla para mostrar el carrito */
        .tabla-carrito {
            margin: 0 auto;
            width: 70%;
            margin-top: 30px;
            border-collapse: collapse;
            background-color: #2c2c2c;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .tabla-carrito th,
        .tabla-carrito td {
            padding: 15px;
            text-align: left;
            border: 1px solid #444;
            color: #fff;
        }

        .tabla-carrito th {
            background-color: #444;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .tabla-carrito td {
            background-color: #333;
        }

        .tabla-carrito td img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }

        .tabla-carrito td .boton-eliminar {
            padding: 0.5rem 1rem;
            background-color: #ff4d4d;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .tabla-carrito td .boton-eliminar:hover {
            background-color: #ff1a1a;
        }

        /* Estilos para el total y el botón de finalizar compra */
        .total-compra {
            text-align: right;
            font-size: 1.5rem;
            margin-top: 20px;
            font-weight: bold;
        }

        .boton-pagar {
            padding: 0.8rem 1.5rem;
            background-color: #28a745;
            color: white;
            font-size: 1.2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .boton-pagar:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <header>
        <div class="barraDatos">
            <div class="mensajeUsuario">
                <?php
                if (isset($_SESSION['usuario'])) {
                    echo '<p class="Bienvenido">Hola, ' . htmlspecialchars($_SESSION['usuario']) . '</p>';
                    echo '<img src="https://cdn-icons-png.flaticon.com/512/74/74472.png" class="foto-perfil"> ';
                }
                ?>
            </div>
            <div class="barraBotones">
                <?php
                if (isset($_SESSION['email'])) {
                    echo '<form method="POST">
                <button type="submit" name="cerrar" class="cerrarsesion-btn">Cerrar sesión</button>
                </form>';
                    echo '<a href="actualizar_datos.php" class="cerrarsesion-btn">Actualizar mis datos</a>';
                    if ($_SESSION['email'] === 'root@email.com') {
                        echo '<a href="insertar_juego.php" class="insertar-btn">Añadir un juego</a>';
                    } else {
                        echo '<a href="index.php" class="insertar-btn">Volver atrás</a>';
                    }
                } else {
                    echo '<a href="iniciar_sesion.php" class="comprar-btn">Iniciar Sesión</a>';
                }
                ?>
            </div>
        </div>

        <div class="apartadoLogo">
            <span class="main-title">Isma & Ale Games</span>
        </div>
    </header>

    <h3 class="mensajeProductos">Estos son los productos que tienes en el carrito</h3>
<?php
if(isset($_POST['comprar'])){
    try{
    $carrito = unserialize($_COOKIE['carrito']);
    if (!is_array($carrito) || empty($carrito)) {
        header('Location: realizar_pedido.php');
        exit;
    }
    include 'connect.php';
    $pdo->beginTransaction();
    $totalCarrito=0;
    foreach ($carrito as $item) {
        $totalCarrito += $item['precio'] * $item['cantidad'];
    }
    $statement = $pdo->prepare('INSERT INTO pedido (cliente_id, fecha_pedido, total, estado) VALUES (:id, :fecha, :total, :estado)');
    $statement->execute(['id' => $_SESSION['cliente_id'], 'fecha' => date('Y-m-d H:i:s'), 'total' => $totalCarrito, 'estado' => 'pendiente']);
    $id_pedido = $pdo->lastInsertId();
    foreach ($carrito as $item) {
        $statement = $pdo->prepare('INSERT INTO detalles_pedido (pedido_id,producto_id, cantidad, precio_unitario, subtotal) VALUES (:pedido_id, :id, :cantidad, :precio, :total)');
        $statement->execute(['pedido_id'=>$id_pedido,'id' => $item['id'], 'cantidad' => $item['cantidad'], 'precio' => $item['precio'], 'total' => $item['cantidad'] * $item['precio']]);
    }
    $pdo->commit();
    setcookie('carrito', serialize([]), time() + 60 * 60 * 24 * 30, '/');
    $carrito=[];
    echo '<h2>Pedido realizado correctamente</h2>';
}catch (PDOException $e) {
    echo '<div class="errores">';
    echo "Error: " . $e->getMessage();
    echo '</div>';
}
}

?>

    <?php
    // Verifica si el carrito no está vacío
    if (!empty($carrito)) {
        echo '<table class="tabla-carrito">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Nombre del Producto</th>';
        echo '<th>Precio</th>';
        echo '<th>Cantidad</th>';
        echo '<th>Total</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        $totalCarrito = 0;
        foreach ($carrito as $item) {
            $totalProducto = $item['precio'] * $item['cantidad'];
            $totalCarrito += $totalProducto;

            echo '<tr>';
            echo '<td>' . htmlspecialchars($item['nombre']) . '</td>';
            echo '<td>' . number_format($item['precio'], 2, ',', '.') . '€</td>';
            echo '<td>' . $item['cantidad'] . '</td>';
            echo '<td>' . number_format($totalProducto, 2, ',', '.') . '€</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '<tfoot>';
        echo '<tr>';
        echo '<td colspan="3"><strong>Total</strong></td>';
        echo '<td>' . number_format($totalCarrito, 2, ',', '.') . '€</td>';
        echo '</tr>';
        echo '</tfoot>';
        echo '</table>';

        // Formulario para mostrar el modal de pago
        echo '<button id="btn-pagar" class="boton-pagar">Pagar</button>';

    } else {
        echo '<h2>No tienes elementos en el carrito</h2>';
    }
    ?>
    <form action="" method="post">
        <input type="hidden" name="vaciar">
        <button type="submit">Vaciar Carrito</button>
    </form>

    <!-- Modal de Pago -->
    <div class="modal" id="modal-pago">
        <div class="modal-content">
            <h2>Pasarela de Pago</h2>
            <form method="post">
                <label for="numero-tarjeta">Número de tarjeta:</label>
                <input type="text" id="numero-tarjeta" placeholder="0000 0000 0000 0000" maxlength="19" required>

                <label for="nombre">Titular de la tarjeta:</label>
                <input type="text" id="nombre" placeholder="Tu nombre" required>

                <label for="correo">Correo electrónico:</label>
                <input type="email" id="correo" placeholder="correo" required>

                <label for="fecha-expiracion">Fecha de expiración:</label>
                <input type="text" id="fecha-expiracion" placeholder="MM/AA" maxlength="5" required>

                <label for="cvv">CVV:</label>
                <input type="number" id="cvv" placeholder="123" maxlength="3" required>

                <input type="hidden" name="comprar">

                <button type="submit" id="pagar-btn">Pagar</button>
            </form>

            <div class="close-modal" id="close-modal">Cancelar</div>
        </div>
    </div>

<?php
if(isset($_POST['vaciar'])){
    setcookie('carrito', serialize([]), time() + 60 * 60 * 24 * 30, '/');
    header('Location: realizar_pedido.php');
    exit;
}

?>

    <footer>
        <p>©Isma & Ale Games 2024</p>
    </footer>

    <script>
        // Muestra el modal cuando se hace clic en el botón de pagar
        const btnPagar = document.getElementById('btn-pagar');
        const modal = document.getElementById('modal-pago');
        const closeModal = document.getElementById('close-modal');

        // Mostrar el modal
        btnPagar.addEventListener('click', function () {
            modal.style.display = 'flex';
        });

        // Cerrar el modal
        closeModal.addEventListener('click', function () {
            modal.style.display = 'none';
        });
    </script>

</body>

</html>

<?php
ob_end_flush();
?>
