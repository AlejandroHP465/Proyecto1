<?php
session_name("Tienda");
session_start();

if (isset($_POST["cerrar"])) {

    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

if (!isset($_COOKIE['carrito'])) {
    setcookie('carrito', serialize([]), time() + 60 * 60 * 24 * 30, '/');
    header('Location: index.php');
    exit;
}

if (isset($_POST['accion']) && $_POST['accion'] == 'añadir') {
    $precio = $_POST['precio'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $id = $_POST['id'] ?? null;
    $carrito = unserialize($_COOKIE['carrito']);
    if (!is_array($carrito)) {
        $carrito = [];
    }

    if ($nombre && $id && $precio) {
        if (!empty($carrito)) {
            $productoEncontrado = false;
            foreach ($carrito as &$item) {
                if ($item['id'] == $id) {
                    $item['cantidad']++;
                    $productoEncontrado = true;
                    break;
                }
            }
            if ($productoEncontrado===false) {
                $carrito[] = ['nombre' => $nombre, 'precio' => $precio, 'id' => $id, 'cantidad' => 1];
            }
        } else {
            $carrito[] = ['nombre' => $nombre, 'precio' => $precio, 'id' => $id, 'cantidad' => 1];
        }

        setcookie('carrito', serialize($carrito), time() + 60 * 60 * 24 * 30, '/');
    }

    header('Location: index.php');
    exit;
}

if (isset($_POST["borrar-juego"])) {
    $productoID = $_POST['id_producto'] ?? null;
    try {
        if (!$productoID) {
            throw new Exception('El ID del producto no está configurado');
        }
        include "connect.php";

        $query1 = "DELETE FROM  genero_juegos  WHERE producto_id = :producto_id";

        $query2 = "DELETE FROM  plataforma_juegos  WHERE producto_id = :producto_id";

        $query3 = "DELETE FROM producto WHERE producto_id = :producto_id";


        $statement1 = $pdo->prepare($query1);
        $statement1->bindParam(':producto_id', $productoID);
        $statement1->execute();

        $statement2 = $pdo->prepare($query2);
        $statement2->bindParam(':producto_id', $productoID);
        $statement2->execute();

        $statement3 = $pdo->prepare($query3);
        $statement3->bindParam(':producto_id', $productoID);
        $statement3->execute();


        // echo "Producto eliminado correctamente";
        //Por depuración, así no mostrará nada cuando se elimine :)

    } catch (PDOException $e) {
        echo "Error al eliminar el producto: " . $e->getMessage();
    }




}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Tienda de Videojuegos</title>
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
                    echo '<a href="actualizar_datos.php"class="cerrarsesion-btn">Actualizar mis datos</a>';
                    if ($_SESSION['email'] === 'root@email.com') {
                        echo '<a href="insertar_juego.php" class="insertar-btn">Añadir un juego</a>';
                    } else {
                        echo '<a href="realizar_pedido.php" class="insertar-btn">Realizar Pedidos</a>';
                    }
                } else {
                    echo '<a href="iniciar_sesion.php" class="comprar-btn">Iniciar Sesión</a>';
                }
                ?>
            </div>
        </div>

        <div class="apartadoLogo">
            <span class="main-title">Isma & Ale Games</span>
            <h2>Nuestros productos</h2>
        </div>
    </header>


    <main>
        <h3 class="mensajeProductos">Estos son los productos que tenemos actualmente en nuestra tienda</h3>

        <section class="juegos">
            <?php

            class productos
            {
                private $producto_id;
                private $nombre;
                private $descripcion;
                private $precio;
                private $generos;
                private $plataformas;
                protected $Foto;

                public function __construct($producto_id, $nombre, $descripcion, $precio, $Foto)
                {
                    $this->producto_id = $producto_id;
                    $this->nombre = $nombre;
                    $this->descripcion = $descripcion;
                    $this->precio = $precio;
                    $this->generos = [];
                    $this->plataformas = [];
                    $this->Foto = $Foto;
                }

                public function getProductoId()
                {
                    return $this->producto_id;
                }
                public function getFoto()
                {
                    return $this->Foto;
                }

                public function getNombre()
                {
                    return $this->nombre;
                }

                public function getDescripcion()
                {
                    return $this->descripcion;
                }

                public function getPrecio()
                {
                    return $this->precio;
                }

                public function getGeneros()
                {
                    return $this->generos;
                }

                public function getPlataformas()
                {
                    return $this->plataformas;
                }

                public function añadirGenero($genero)
                {
                    if (!in_array($genero, $this->generos)) {
                        $this->generos[] = $genero;
                    }
                }

                public function añadirPlataforma($plataforma)
                {
                    if (!in_array($plataforma, $this->plataformas)) {
                        $this->plataformas[] = $plataforma;
                    }
                }
            }


            include 'connect.php';


            $statement = $pdo->prepare('select * from producto');
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            $arrayFinal = [];


            foreach ($result as $item) {

                $producto = new productos($item['producto_id'], $item['nombre'], $item['descripcion'], $item['precio'], $item['Foto']);
                $statement = $pdo->prepare('select nombre from genero inner join genero_juegos using (genero_id) where genero_juegos.producto_id=:producto_id');
                $statement->execute(['producto_id' => $producto->getProductoId()]);
                $generos = $statement->fetchAll(PDO::FETCH_ASSOC);
                foreach ($generos as $genero) {
                    $producto->añadirGenero($genero['nombre']);

                }

                $statement = $pdo->prepare('select nombre from plataforma inner join plataforma_juegos using (plataforma_id) where plataforma_juegos.producto_id=:producto_id');
                $statement->execute(['producto_id' => $producto->getProductoId()]);
                $plataformas = $statement->fetchAll(PDO::FETCH_ASSOC);
                foreach ($plataformas as $plataforma) {
                    $producto->añadirPlataforma($plataforma['nombre']);
                }
                $arrayFinal[] = $producto; //aquí se añade el producto
            



            }


            ?>

            <?php foreach ($arrayFinal as $producto): ?>
                <div class="tarjeta-contenedor">
                    <div class="tarjeta">
                        <div class="tarjeta-lado tarjeta-lado-frontal">
                            <div class="nombre-juego"><?php echo htmlspecialchars($producto->getNombre()); ?></div>
                            <img src="<?php echo htmlspecialchars($producto->getFoto()); ?>" width="150px" height="150px"
                                alt="Imagen de <?php echo htmlspecialchars($producto->getNombre()); ?>">

                            <div class="precio"><?php echo htmlspecialchars($producto->getPrecio()); ?>€</div>
                        </div>
                        <div class="tarjeta-lado tarjeta-lado-trasera">
                            <h2>Género: </h2>
                            <div class="manolito">
                                <ul>

                                    <?php
                                    foreach ($producto->getGeneros() as $genero) {
                                        echo "<li>$genero</li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                            <h2>Plataforma:</h2>
                            <div class="manolito">
                                <ul>

                                    <?php
                                    foreach ($producto->getPlataformas() as $plataforma) {
                                        echo "<li>$plataforma</li>";
                                    }
                                    ?>
                                </ul>
                            </div>

                            <?php
                            if(isset($_SESSION['email'])){
                                if ($_SESSION['email'] !== 'root@email.com') {
                                    echo '<form action="" method="post">
                                        <input type="hidden" name="accion" value="añadir">
                                        <input type="hidden" name="nombre" value="' . $producto->getNombre() . '">
                                        <input type="hidden" name="precio" value="' . $producto->getPrecio() . '">
                                        <input type="hidden" name="id" value="' . $producto->getProductoId() . '">
                                        <button type="submit">Añadir al carrito</button>
                                        </form>';
                                } else {
                                    echo '<form action="" method="post">';
                                    echo '<input type="hidden" name="accion" value="eliminar">';
                                    echo '<input type="hidden" name="id_producto" value="' . $producto->getProductoId() . '">';
                                    echo '<button class="btn-comprar" name="borrar-juego" type="submit">Borrar Juego</button>
                                </form>';
    
                                    echo '<form action="actualizar_juegos.php" method="get">
                                    <input type="hidden" name="accion" value="actualizar">';
                                    echo '<input type="hidden" name="id_producto" value="' . $producto->getProductoId() . '">';
                                    echo '<button class="btn-comprar" name="actualizar" type="submit">Actualizar Juego</button>
                                </form>';
                                }
                            }
                            ?>

                            <?php





                            ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>


        </section>
    </main>

    <footer>
        <p>©Isma & Ale Games 2024</p>
    </footer>




</body>

</html>