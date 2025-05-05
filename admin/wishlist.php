<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{
date_default_timezone_set('America/Caracas');// change according timezone
$currentTime = date( 'd-m-Y h:i:s A', time () );

if(isset($_GET['del']))
		  {
		          mysqli_query($con,"delete from products where id = '".$_GET['id']."'");
                  $_SESSION['delmsg']="Product deleted !!";
		  }

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Administración| Gestión Lista de Deseos</title>
	<link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
	<link type="text/css" href="css/theme.css" rel="stylesheet">
	<link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
	<link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
</head>
<body>
<?php include('include/header.php');?>

	<div class="wrapper">
		<div class="container">
			<div class="row">
					<?php include('include/sidebar.php');?>				
				<div class="span9">
					<div class="content">
						<div class="module">
							<div class="module-head">
								<h3>Lista de Deseos</h3>
							</div>
							<div class="module-body table">
							<br />
								<table class="table">
								<thead>
									<tr>
										<th></th>
										<th>Nombre</th>
										<th>Teléfono</th>
										<th>Producto</th>
										<th>Precio</th>
									</tr>
								</thead>
								<tbody>
										<?php
											$ret=mysqli_query($con,"SELECT products1.`productName` AS pname, products1.`productPrice` AS pprice, products1.`productImage1` AS pimage, wishlist1.`id`, wishlist1.`userId`, wishlist1.`productId` AS pid, users1.`name` AS uname, users1.`contactno` AS ucontactno FROM	(`shopping`.`products` products1 INNER JOIN `shopping`.`wishlist` wishlist1 ON products1.`id` = wishlist1.`id`) INNER JOIN `shopping`.`users` users1 ON wishlist1.`userId` = users1.`id`");
											
												$num=mysqli_num_rows($ret);
												if($num>0)
													{
														while ($row=mysqli_fetch_array($ret)) {
															
										?>
																				
									<tr>
										<td class="col-md-2"><img src="productimages/<?php echo htmlentities($row['pimage']);?>" alt="<?php echo htmlentities($row['pname']);?>" width="60" height="100"></td>
										<td class="col-md-4"><?php echo htmlentities($row['uname']);?></td>
										<td class="col-md-4"><?php echo htmlentities($row['ucontactno']);?></td>
										<td class="col-md-4"><?php echo htmlentities($row['pname']);?></td>
										<td class="col-md-4"><?php echo htmlentities($row['pprice']);?></td>
									</tr>
									
										<?php 			}
													}
										else{ ?>
				<tr>
					<td style="font-size: 18px; font-weight:bold ">La lista de deseos está vacia</td>

				</tr>
				<?php } ?>
			</tbody>
		</table>
							</div>
						</div>						

						
						
					</div><!--/.content-->
				</div><!--/.span9-->
			</div>
		</div><!--/.container-->
	</div><!--/.wrapper-->

<?php include('include/footer.php');?>

	<script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
	<script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
	<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
	<script src="scripts/datatables/jquery.dataTables.js"></script>
	<script>
		$(document).ready(function() {
			$('.datatable-1').dataTable();
			$('.dataTables_paginate').addClass("btn-group datatable-pagination");
			$('.dataTables_paginate > a').wrapInner('<span />');
			$('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
			$('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');
		} );
	</script>
</body>
<?php } ?>