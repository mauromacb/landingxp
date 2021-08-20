<?php

$servername = "localhost";
$database = "cotizasalud";
$username = "root";
$password = "";
// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$conn2 = mysqli_connect($servername, $username, $password, $database);
// Check connection
if (!$conn2) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST)){
	//var_dump($_POST);
	//echo "entro por post";
	$myObj->nombres = $_POST['nombres'];
	$myObj->fecha_nacimiento = $_POST['fecha_nacimiento'];
	$myObj->sexo = $_POST['sexo'];

	$myJSON = json_encode($myObj);

	echo $myJSON;
}
if (isset($_GET['md'])==1){
	//var_dump($_GET);
	//echo "entro por get";
}
if ($_POST){
	$codigo=$_POST['cl'];
}else{
	$codigo=$_GET['cl'];
}
		
$onbody='';
$sql = "SELECT * FROM empresas WHERE codigo = '$codigo' limit 1";
if (!$resultado = $conn->query($sql)) {
    // ¡Oh, no! La consulta falló. 
    echo "Lo sentimos, este sitio web está experimentando problemas.";
	die('Error');
}else{
	$resultado = $resultado->fetch_assoc();
	if($resultado){	
		//ingreso el resultado del formulario
		
	}else{echo 'No encontrado';}
}
 mysqli_close($conn);


 ?>