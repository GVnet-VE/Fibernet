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

if(isset($_POST['submit']))
{
$sql=mysqli_query($con,"SELECT password FROM  admin where password='".md5($_POST['password'])."' && username='".$_SESSION['alogin']."'");
$num=mysqli_fetch_array($sql);
if($num>0)
{
 $con=mysqli_query($con,"update admin set password='".md5($_POST['newpassword'])."', updationDate='$currentTime' where username='".$_SESSION['alogin']."'");
$_SESSION['msg']="Password actualizado exitosamente !!";
}
else
{
$_SESSION['msg']="El password anterior no coincide !!";
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Cashnet | Administración</title>
	<link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
	<link type="text/css" href="css/theme.css" rel="stylesheet">
	<link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
	<link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
	<script type="text/javascript">
function valid()
{
if(document.chngpwd.password.value=="")
{
alert("El campo password actual esta vacío !!");
document.chngpwd.password.focus();
return false;
}
else if(document.chngpwd.newpassword.value=="")
{
alert("El campo nuevo password esta vacío !!");
document.chngpwd.newpassword.focus();
return false;
}
else if(document.chngpwd.confirmpassword.value=="")
{
alert("El campo confirmar nuevo password esta vacío !!");
document.chngpwd.confirmpassword.focus();
return false;
}
else if(document.chngpwd.newpassword.value!= document.chngpwd.confirmpassword.value)
{
alert("El campo nuevo password y la confirmación de password no coinciden !!");
document.chngpwd.confirmpassword.focus();
return false;
}
return true;
}
</script>
<script>
    function toggleIcon(event) {
        event.preventDefault();
        var icon = event.currentTarget.querySelector("#icon");
        icon.classList.toggle("fa-plus");
        icon.classList.toggle("fa-minus");
    }
</script>
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
							<div id="accordion">
								<div class="module-head">

									<h3>Cambiar Password Administración  							
								
                                            <a class="card-link" data-toggle="collapse" href="#collapseOne" onclick="toggleIcon(event)">
											<i class="menu-icon icon-plus"></i>
                                            </a>
											</h3>    
                                        <div id="collapseOne" class="collapse" data-parent="#accordion">
                                            <div class="card-body">

												<div class="module-body">

														<?php if(isset($_POST['submit']))
														{?>
															<div class="alert alert-success">
																<button type="button" class="close" data-dismiss="alert">×</button>
																<?php echo htmlentities($_SESSION['msg']);?><?php echo htmlentities($_SESSION['msg']="");?>
															</div>
														<?php } ?>
													<br />

													<form class="form-horizontal row-fluid" name="chngpwd" method="post" onSubmit="return valid();">
														<div class="control-group">
															<label class="control-label" for="basicinput">Password Actual</label>
															<div class="controls">
																<input type="password" placeholder="Ingrese Nuevo Password"  name="password" class="span8 tip" required>
															</div>
														</div>

														<div class="control-group">
															<label class="control-label" for="basicinput">Nuevo Password</label>
															<div class="controls">
																<input type="password" placeholder="Ingrese su Nuevo Password"  name="newpassword" class="span8 tip" required>
															</div>
														</div>

														<div class="control-group">
															<label class="control-label" for="basicinput">Confirmar Nuevo Password</label>
															<div class="controls">
																<input type="password" placeholder="Repita su Nuevo Password"  name="confirmpassword" class="span8 tip" required>
															</div>
														</div>

														<div class="control-group">
															<div class="controls">
																<button type="submit" name="submit" class="btn">Enviar</button>
															</div>
														</div>
													</form>
												</div>
											</div>
										</div>
								</div>
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
</body>
<?php } ?>