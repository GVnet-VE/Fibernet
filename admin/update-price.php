<?php
session_start();
include('include/config.php');
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
            $codigosNoEncontrados = [];

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
                    } else {
                        $codigosNoEncontrados[] = $codigo;
                    }
                }
            }

            $msg = '';
            if ($productosActualizados > 0) {
                $msg .= "$productosActualizados productos actualizados correctamente. ";
            } else {
                $msg .= "No se actualizó ningún precio, verifica los códigos.";
            }
            
            if (!empty($codigosNoEncontrados)) {
                $msg .= "Códigos no encontrados en la base de datos: ";
                $msg .= implode(', ', $codigosNoEncontrados);
            }

            $_SESSION['msg'] = $msg;

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
    <title>CashNet | Administración</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>

</head>

<body>
    <?php include('include/header.php'); ?>

    <div class="wrapper">
        <div class="container">
            <div class="row">
                <?php include('include/sidebar.php'); ?>

                <div class="span9">
                    <div class="content">
                        <div class="module">
                            <div class="module-head">
                                <h3>Actualizar Precios de Productos</h3>
                            </div>
                            <div class="module-body">

                                <?php if (isset($_SESSION['msg'])) { ?>
                                    <div class="alert alert-info">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong>Notificación:</strong> <?php echo htmlentities($_SESSION['msg']); ?>
                                        <?php unset($_SESSION['msg']); ?>
                                    </div>
                                <?php } ?>

                                <div class="alert alert-info">
                                    <strong>Importante:</strong> El archivo Excel debe tener <b>dos columnas</b>: <code>codigo</code> y <code>precio</code>. Asegúrate de que las columnas tengan estos nombres en la primera fila.
                                </div>

                                <div class="mb-3">
                                    <h5>Ejemplo de contenido:</h5>
                                    <table class="table table-bordered table-striped">
                                        <thead>
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
                                </div>

                                <form method="post" enctype="multipart/form-data" class="form-horizontal">
                                    <div class="control-group">
                                        <label class="control-label" for="excel">Seleccionar archivo Excel:</label>
                                        <div class="controls">
                                            <input type="file" class="span8" id="excel" name="excel" required>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <div class="controls">
                                            <button type="submit" name="submit" class="btn">
                                                <i class="icon-upload"></i> Actualizar Precios
                                            </button>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div><!--/.content-->
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