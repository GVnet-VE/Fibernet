<?php

session_start();
include('include/config.php');
include('include/header.php');
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$message = '';

if (isset($_POST['submit'])) {
    $file = $_FILES['excel']['tmp_name'];

    if (!empty($file)) {
        try {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            $productosActualizados = 0;

            for ($i = 1; $i < count($data); $i++) {
                $codigo = trim($data[$i][0]);
                $precio = floatval($data[$i][1]);

                if (!empty($codigo) && $precio > 0) {
                    $codigo = mysqli_real_escape_string($con, $codigo);

                    $checkQuery = "SELECT COUNT(*) FROM products WHERE codigo = '$codigo'";
                    $result = mysqli_query($con, $checkQuery);
                    if (!$result) {
                        echo "Error al verificar el código: " . mysqli_error($con);
                        continue;
                    }

                    $row = mysqli_fetch_row($result);

                    if ($row[0] > 0) {
                        $updateQuery = "UPDATE products SET productPrice = '$precio', updationDate = NOW() WHERE codigo = '$codigo'";
                        if (mysqli_query($con, $updateQuery)) {
                            $productosActualizados++;
                        } else {
                            echo "Error al actualizar el producto con código $codigo: " . mysqli_error($con);
                        }
                    }
                }
            }

            if ($productosActualizados > 0) {
                $_SESSION['msg'] = "$productosActualizados productos actualizados correctamente.";
            } else {
                $_SESSION['msg'] = "No se actualizó ningún precio, verifica los códigos.";
            }
        } catch (Exception $e) {
            $_SESSION['msg'] = 'Error al cargar el archivo: ' . $e->getMessage();
        }
    } else {
        $_SESSION['msg'] = 'Por favor, seleccione un archivo.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashnet | Administración</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
</head>

<body>

    <div class="wrapper">
        <div class="container">
            <div class="row">
                <!-- Barra lateral -->
                <?php include('include/sidebar.php'); ?>

                <!-- Contenido principal -->
                <div class="span9">
                    <div class="content">
                        <div class="module">
                            <div class="module-head">
                                <h3>Actualizar Precios de Productos</h3>
                            </div>
                            <div class="module-body">

                                <!-- Mostrar mensaje de éxito o error -->
                                <?php if (isset($_SESSION['msg'])) { ?>
                                    <div class="alert alert-info">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong>Notificación:</strong> <?php echo $_SESSION['msg'];
                                                                        unset($_SESSION['msg']); ?>
                                    </div>
                                <?php } ?>

                                <form method="post" enctype="multipart/form-data" class="form-horizontal row-fluid">
                                    <div class="control-group">
                                        <strong>Importante:</strong> El archivo Excel debe tener <b>dos columnas</b>:
                                        <ul class="mb-2">
                                            <li><strong>codigo</strong>: Código del producto</li>
                                            <li><strong>precio</strong>: Nuevo precio del producto</li>
                                        </ul>
                                        Asegúrate de que las columnas tengan estos nombres en la primera fila.<br>
                                        <small>Ejemplo de contenido:</small>
                                        <div class="table-responsive mt-2">
                                            <table class="table table-bordered table-sm mb-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>codigo</th>
                                                        <th>precio</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>PROD001</td>
                                                        <td>1500.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td>PROD002</td>
                                                        <td>2450.50</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <br>
                                            <label class="control-label" for="excel">Seleccionar archivo Excel:</label>
                                            <div class="controls">
                                                <input type="file" class="span8" id="excel" name="excel" required>
                                            </div>

                                            <button type="submit" name="submit" class="btn btn-">Actualizar Precios</button>

                                        </div>
                                </form>



                            </div>
                        </div>
                    </div>
                </div><!--/.span9-->
            </div><!--/.row-->
        </div><!--/.container-->
    </div><!--/.wrapper-->

    <?php include('include/footer.php'); ?>

    <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

</body>

</html>