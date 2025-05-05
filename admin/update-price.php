<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
require __DIR__ . '/../vendor/autoload.php'; // Ajusta la ruta según tu estructura
include('include/config.php');

use PhpOffice\PhpSpreadsheet\IOFactory;

$message = '';

if (isset($_POST['submit'])) {
  // Obtener el archivo Excel
  $file = $_FILES['excel']['tmp_name'];

  if (!empty($file)) {
      // Cargar el archivo Excel
      try {
          // Cargar el archivo Excel (.xlsx o .xls)
          $spreadsheet = IOFactory::load($file);

          $sheet = $spreadsheet->getActiveSheet();
          $data = $sheet->toArray(); // Obtener los datos del Excel

          // Contador para productos actualizados
          $productosActualizados = 0;

          // Recorre cada fila del Excel, comenzando desde la segunda (índice 1) para evitar la cabecera
          for ($i = 1; $i < count($data); $i++) {
              $codigo = trim($data[$i][0]);  // Columna A: Código
              $precio = floatval($data[$i][1]); // Columna B: Precio

              // Verificar que el código no esté vacío y que el precio sea mayor a 0
              if (!empty($codigo) && $precio > 0) {
                  // Escapar el código para prevenir inyecciones SQL
                  $codigo = mysqli_real_escape_string($con, $codigo);

                  // Verificar si el código ya existe en la base de datos
                  $checkQuery = "SELECT COUNT(*) FROM products WHERE codigo = '$codigo'";
                  $result = mysqli_query($con, $checkQuery);
                  if (!$result) {
                      echo "Error al verificar el código: " . mysqli_error($con);
                      continue;  // Si hay un error en la consulta, continuamos con la siguiente fila
                  }

                  $row = mysqli_fetch_row($result);
                  
                  // Si el código existe, actualizamos el precio
                  if ($row[0] > 0) {
                      // Consulta para actualizar el precio del producto con el código correspondiente
                      $updateQuery = "UPDATE products SET productPrice = '$precio', updationDate = NOW() WHERE codigo = '$codigo'";

                      // Ejecutar la consulta
                      if (mysqli_query($con, $updateQuery)) {
                          $productosActualizados++; // Aumentar contador de productos actualizados
                      } else {
                          echo "Error al actualizar el producto con código $codigo: " . mysqli_error($con);
                      }
                  } else {
                      echo "El producto con código $codigo no existe en la base de datos.<br>";
                  }
              }
          }

          // Mensaje de éxito
          if ($productosActualizados > 0) {
              echo "<script>alert('$productosActualizados productos actualizados correctamente.');</script>";
          } else {
              echo "<script>alert('No se actualizó ningún precio, verifica los códigos.');</script>";
          }

      } catch (Exception $e) {
          // Manejo de errores si el archivo no se puede cargar
          echo "<script>alert('Error al cargar el archivo: " . $e->getMessage() . "');</script>";
      }
  } else {
      // Si no se seleccionó un archivo
      echo "<script>alert('Por favor, seleccione un archivo.');</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Actualizar Precios</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>

<div class="container">
  <h2>Actualizar Precios de Productos</h2>

  <form method="post" enctype="multipart/form-data">
      <div class="form-group">
          <label for="excel">Seleccionar archivo Excel:</label>
          <input type="file" class="form-control" id="excel" name="excel" required>
      </div>
      <button type="submit" name="submit" class="btn btn-primary">Subir y Actualizar Precios</button>
  </form>
</div>

</body>
</html>