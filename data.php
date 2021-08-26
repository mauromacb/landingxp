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
//envio cotizacion auto
if ($_GET['p']=='aut'){
require('config.php');
$conn = mysqli_connect($servername, $username, $password, $database);
$sql = "SELECT id_user FROM empresas WHERE codigo = '".$_POST['cl']."' limit 1;";
	if (!$resultado = $conn->query($sql)) {
		echo "Lo sentimos, este sitio web está experimentando problemas.";
		die('Error');
	}else{
		$resultado = $resultado->fetch_assoc();
		if($resultado){	
			//ingreso el resultado del formulario
			if ($resultado){
				//variables
				$id_cliente=null;
				//inserto cliente
				$sql = "INSERT INTO `clientes`(`id_user`, `identificacion`, `nombres`, `telefono`, `correo`, `fecha_nacimiento`, `fecha_cotizacion`) 
				VALUES (".$resultado['id_user'].", '".$_POST['identificacion']."', '".$_POST['nombres']."', '".$_POST['telefono']."', '".$_POST['email']."', '".$_POST['fecha_nacimiento']."', '".date('Y-m-d H:i:s')."')";
				//echo $sql;
				$conn2 = mysqli_connect($servername, $username, $password, $database);
				if ($result = $conn2->query($sql)) {
				   $id_cliente=$conn2->insert_id;
				   //echo $id_cliente;
				   //inserto cotizaciones_datos_iniciales
				   $sql = "INSERT INTO `cotizaciones_datos_iniciales`(`id_cliente`, `placa_vehiculo`, `lugar_circulacion`, `created_at`) 
															VALUES (".$id_cliente.", '".$_POST['placa']."', '".$_POST['lugar']."','".date('Y-m-d H:i:s')."');";
					//echo $sql;
					if ($conn2->query($sql)) {
					   $id_cotizacion_datos_iniciales=$conn2->insert_id;
					   //echo $id_cotizacion_datos_iniciales;
					   //inserto cotizacion
					   $sql = "INSERT INTO `cotizaciones`(`id_cliente`, `id_user`, `id_tipo_seguro`, `id_cotizaciones_datos_iniciales`, `fecha_inicial_cotizacion`, `id_etapa_negociacion`, `created_at`) 
								VALUES (".$id_cotizacion_datos_iniciales.", '".$resultado['id_user']."', 1, '".$id_cotizacion_datos_iniciales."', '".date('Y-m-d H:i:s')."', 1, '".date('Y-m-d H:i:s')."');";
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

							if ($resultado['auth'] == 'S') {
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

							if (!$mail->Send()) {
								echo json_encode('Error: ' . $mail->ErrorInfo);
							} else {
								echo json_encode('Mail enviado exitosamente...!');
							}
							
						} catch (Exception $e) {
							echo $e->getMessage();
						}
					}
			}
		}
	}
}


 ?>