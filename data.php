<?php
include_once('class.phpmailer.php');
//agregar personas para el plan familiar
if ($_GET['p']=='nm'){
	if (isset($_POST)){
		//var_dump($_POST);
		//echo "entro por post";
		
		$myObj = new \stdClass();
		$myObj->nombres = $_POST['nombres'];
		$myObj->identificacion = $_POST['identificacion'];
		$myObj->fecha_nacimiento = $_POST['fecha_nacimiento'];
		$myObj->sexo = $_POST['sexo'];
		$myObj->datos = $_POST['masdetallesinput'];

		$myJSON = json_encode($myObj);
		echo $myJSON;
	}
}
//envio cotizacion empresarial
if ($_GET['p']=='emp'){
require('config.php');
	if (isset($_POST)){
	//var_dump($_POST);
	//echo "entro por post";
	$conn = mysqli_connect($servername, $username, $password, $database);
	// Check connection
		if (!$conn) {
			die("Connection failed: " . mysqli_connect_error());
		}
		$sql = "SELECT * FROM user_parametros where id_user=".$_POST['us']." limit 1;";
		if (!$resultado = $conn->query($sql)) {
		echo "Lo sentimos, este sitio web está experimentando problemas.";
		die('Error');
		}else{
			$resultado = $resultado->fetch_assoc();
			//var_dump($resultado);
				
			try{

				//genera envio corrreo
				$mail = new PHPMailer();
				$mail->IsSMTP();
				$mail->Mailer = "smtp";

				if ($resultado['smtpauth'] == 'S') {
					$mail->SMTPAuth = true;
				}

				if ($resultado['ssltls'] == 'ssl') {
					$mail->SMTPSecure = "ssl";
				}elseif ($resultado['ssltls'] == 'tls') {
					$mail->SMTPSecure = "tls";
				}
				

				$mail->Host 	= $resultado['server'];
				$mail->Port 	= $resultado['port'];
				$mail->Username = $resultado['user'];
				$mail->Password = $resultado['password'];
				$mail->From 	= $resultado['email_remitente'];
				$mail->FromName = $resultado['email_remitente'];
				$mail->Subject 	= "Cotización Plan Empresarial";
				//$mail->AltBody 	= "Bienvenidos.....";

				$body = "<strong>Prueba de Envio, correo generado existosamente...</strong><br /><br />";
				$body .= "<font color='red'>Saludos</font>";

				$mail->Body =  $resultado['plantilla_correo'];

				$mail->IsHTML(true);

				$mail->AddAddress('tuportalec@hotmail.com');

				/*if (!$mail->Send()) {
					echo 'Error: ' . $mail->ErrorInfo;
				} else {
					echo json_encode('Mail enviado exitosamente...!');
				}*/
				echo json_encode('Mail enviado exitosamente...!');
				
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		}
	}
}
//envio cotizacion familiar
if ($_GET['p']=='fam'){
require('config.php');
$conn = mysqli_connect($servername, $username, $password, $database);
$sql = "SELECT * FROM empresas WHERE codigo = '".$_POST['cl']."' limit 1;";
	if (!$resultado = $conn->query($sql)) {
		echo "Lo sentimos, este sitio web está experimentando problemas.";
		die('Error');
	}else{
		$resultado = $resultado->fetch_assoc();
		if($resultado){	
			//ingreso el resultado del formulario
			if ($_POST){
				//var_dump($_POST);exit;
				//variables
				$id_cliente=null;
				//inserto cliente
				$sql = "INSERT INTO clientes(`id_user`, `id_sexo`, `identificacion`, `nombres`, `telefono`, `correo`, `fecha_nacimiento`, `fecha_cotizacion`, `eps`) 
				VALUES (".$resultado['id_user'].", ".$_POST['sexo'].", '".$_POST['identificacion']."', '".$_POST['nombres']."', '".$_POST['telefono']."', '".$_POST['email']."', '".$_POST['fecha_nacimiento']."', '".date('Y-m-d H:i:s')."', '".$_POST['eps']."');";
				$conn2 = mysqli_connect($servername, $username, $password, $database);
				if ($result = $conn2->query($sql)) {
				   $id_cliente=$conn2->insert_id;
				   //echo $id_cliente;
				   //var_dump($_POST);
					//echo "<br><br>";
					$detalles=json_decode(json_decode(json_encode($_POST["masdetallesinput"][0])));
					foreach($detalles as $detalle){
						/*var_dump($detalle[0]);
						var_dump($detalle[1]);
						var_dump($detalle[2]);
						var_dump($detalle[3]);
						echo "<br><br>";*/
			
						$sql="INSERT INTO clientes_adicionales(`id_cliente`, `id_sexo`, `identificacion`, `nombres`, `fecha_nacimiento`, `created_at`) 
														VALUES (".$id_cliente.", ".$detalle[3].", '".$detalle[1]."', '".$detalle[0]."', '".$detalle[2]."', '".date('Y-m-d H:i:s')."');";
						if (!$conn2->query($sql)) {
							echo "Lo sentimos, este sitio web está experimentando problemas.";
							die('Error al insertar clientes adicionales');
						}
					}
				   //inserto cotizaciones_datos_iniciales
				   $sql = "INSERT INTO `cotizaciones_datos_iniciales`(`id_cliente`, `created_at`) 
															VALUES (".$id_cliente.", '".date('Y-m-d H:i:s')."');";
					//echo $sql;
					if ($conn2->query($sql)) {
					   $id_cotizacion_datos_iniciales=$conn2->insert_id;
					   //echo $id_cotizacion_datos_iniciales;
					   //inserto cotizacion
					   $sql = "INSERT INTO cotizaciones(`id_cliente`, `id_user`, `id_tipo_seguro`, `id_cotizaciones_datos_iniciales`, `fecha_inicial_cotizacion`, `id_etapa_negociacion`, `created_at`) 
								VALUES (".$id_cliente.", ".$resultado['id_user'].", 3	, ".$id_cotizacion_datos_iniciales.", '".date('Y-m-d H:i:s')."', 1, '".date('Y-m-d H:i:s')."');";
					   if (!$conn2->query($sql)) {
							echo "Lo sentimos, este sitio web está experimentando problemas.";
							die('Error al insertar cotizacion');
					   }
					   //echo $sql;
					}else{
						echo "Lo sentimos, este sitio web está experimentando problemas.";
						die('Error al insertar datos iniciales cotizacion');
					}
				}else{
					echo "Lo sentimos, este sitio web está experimentando problemas.";
					die('Error al insertar cliente');
				}
				//var_dump($_POST);		
				
				
				
				$conn = mysqli_connect($servername, $username, $password, $database);
				// Check connection
					if (!$conn) {
						die("Connection failed: " . mysqli_connect_error());
					}
					$sql = "SELECT * FROM user_parametros where id_user=".$_POST['us']." limit 1;";
					if (!$resultado = $conn->query($sql)) {
					echo "Lo sentimos, este sitio web está experimentando problemas.";
					die('Error');
					}else{
						$resultado = $resultado->fetch_assoc();
						//var_dump($resultado);
							
						try{

							//genera envio corrreo
							$mail = new PHPMailer();
							$mail->IsSMTP();
							$mail->Mailer = "smtp";

							if ($resultado['smtpauth'] == 'S') {
								$mail->SMTPAuth = true;
							}

							if ($resultado['ssltls'] == 'ssl') {
								$mail->SMTPSecure = "ssl";
							}elseif ($resultado['ssltls'] == 'tls') {
								$mail->SMTPSecure = "tls";
							}
							

							$mail->Host 	= $resultado['server'];
							$mail->Port 	= $resultado['port'];
							$mail->Username = $resultado['user'];
							$mail->Password = $resultado['password'];
							$mail->From 	= $resultado['email_remitente'];
							$mail->FromName = $resultado['email_remitente'];
							$mail->Subject 	= "Cotización Plan Familiar";
							//$mail->AltBody 	= "Bienvenidos.....";

							$body = "<strong>Prueba de Envio, correo generado existosamente...</strong><br /><br />";
							$body .= "<font color='red'>Saludos</font>";

							$mail->Body =  $resultado['plantilla_correo'];

							$mail->IsHTML(true);

							$mail->AddAddress('tuportalec@hotmail.com');

							/*if (!$mail->Send()) {
								echo 'Error: ' . $mail->ErrorInfo;
							} else {
								echo json_encode('Mail enviado exitosamente...!');
							}*/
							echo json_encode('Mail enviado exitosamente...!');
							
						} catch (Exception $e) {
							echo $e->getMessage();
						}
					}
			}
		}
	}
}



//envio cotizacion personal
if ($_GET['p']=='per'){
require('config.php');
$conn = mysqli_connect($servername, $username, $password, $database);
$sql = "SELECT * FROM empresas WHERE codigo = '".$_POST['cl']."' limit 1;";
	if (!$resultado = $conn->query($sql)) {
		echo "Lo sentimos, este sitio web está experimentando problemas.";
		die('Error');
	}else{
		$resultado = $resultado->fetch_assoc();
		if($resultado){	
			//ingreso el resultado del formulario
			if ($_POST){
				//variables
				$id_cliente=null;
				//inserto cliente
				$sql = "INSERT INTO `clientes`(`id_user`, `id_sexo`, `identificacion`, `nombres`, `telefono`, `correo`, `fecha_nacimiento`, `fecha_cotizacion`, `eps`) 
				VALUES (".$resultado['id_user'].", ".$_POST['sexo'].", '".$_POST['identificacion']."', '".$_POST['nombres']."', '".$_POST['telefono']."', '".$_POST['email']."', '".$_POST['fecha_nacimiento']."', '".date('Y-m-d H:i:s')."', '".$_POST['eps']."');";
				//echo $sql;
				$conn2 = mysqli_connect($servername, $username, $password, $database);
				if ($result = $conn2->query($sql)) {
				   $id_cliente=$conn2->insert_id;
				   //echo $id_cliente;
				   //inserto cotizaciones_datos_iniciales
				   $sql = "INSERT INTO `cotizaciones_datos_iniciales`(`id_cliente`, `created_at`) 
															VALUES (".$id_cliente.", '".date('Y-m-d H:i:s')."');";
					//echo $sql;
					if ($conn2->query($sql)) {
					   $id_cotizacion_datos_iniciales=$conn2->insert_id;
					   //echo $id_cotizacion_datos_iniciales;
					   //inserto cotizacion
					   $sql = "INSERT INTO `cotizaciones`(`id_cliente`, `id_user`, `id_tipo_seguro`, `id_cotizaciones_datos_iniciales`, `fecha_inicial_cotizacion`, `id_etapa_negociacion`, `created_at`) 
								VALUES (".$id_cliente.", ".$resultado['id_user'].", 3	, ".$id_cotizacion_datos_iniciales.", '".date('Y-m-d H:i:s')."', 1, '".date('Y-m-d H:i:s')."');";
					   $conn2->query($sql);
					   //echo $sql;
					}
				}
				
				
				
				$conn = mysqli_connect($servername, $username, $password, $database);
				// Check connection
					if (!$conn) {
						die("Connection failed: " . mysqli_connect_error());
					}
					$sql = "SELECT * FROM user_parametros where id_user=".$resultado['id_user']." limit 1;";
					if (!$resultado = $conn->query($sql)) {
					echo "Lo sentimos, este sitio web está experimentando problemas.";
					die('Error');
					}else{
						$resultado = $resultado->fetch_assoc();
						//var_dump($resultado);
							
						try{

							//genera envio corrreo
							$mail = new PHPMailer();
							$mail->IsSMTP();
							$mail->Mailer = "smtp";

							if ($resultado['smtpauth'] == 'S') {
								$mail->SMTPAuth = true;
							}

							if ($resultado['ssltls'] == 'ssl') {
								$mail->SMTPSecure = "ssl";
							}elseif ($resultado['ssltls'] == 'tls') {
								$mail->SMTPSecure = "tls";
							}
							

							$mail->Host 	= $resultado['server'];
							$mail->Port 	= $resultado['port'];
							$mail->Username = $resultado['user'];
							$mail->Password = $resultado['password'];
							$mail->From 	= $resultado['email_remitente'];
							$mail->FromName = $resultado['email_remitente'];
							$mail->Subject 	= "Cotización Plan Familiar";
							//$mail->AltBody 	= "Bienvenidos.....";

							$body = "<strong>Prueba de Envio, correo generado existosamente...</strong><br /><br />";
							$body .= "<font color='red'>Saludos</font>";

							$mail->Body =  $resultado['plantilla_correo'];

							$mail->IsHTML(true);

							$mail->AddAddress('tuportalec@hotmail.com');

							/*if (!$mail->Send()) {
								echo 'Error: ' . $mail->ErrorInfo;
							} else {
								echo json_encode('Mail enviado exitosamente...!');
							}*/
							echo json_encode('Mail enviado exitosamente...!');
							
						} catch (Exception $e) {
							echo $e->getMessage();
						}
					}
			}
		}
	}
}
 ?>