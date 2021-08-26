<?php
require('config.php');
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
if ($_POST){
	$codigo=$_POST['cl'];
}else{
	$codigo=$_GET['cl'];
}
$onbody='';

$sql = "SELECT * FROM sexo;";
$sexo = $conn->query($sql);
$option='';
$option='<select name="sexo" id="sexo" class="form-control" required>';
while ($fila = mysqli_fetch_array($sexo)){
	$option=$option.'<option value="'.$fila[0].'">'.$fila[1].'</option>';
}
$option=$option.'</select>';

$sql = "SELECT * FROM empresas WHERE codigo = '$codigo' limit 1;";
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
			echo $sql;
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
			$onbody='onLoad="alerta()"';
		}
?>
<!DOCTYPE html>
<html lang="en">

  <head>

     <meta http-equiv=”Content-Type” content=”text/html; charset=ISO-8859-1″ />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">

    <title>Landing Page XP</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">


    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/tooplate-main.css">
    <link rel="stylesheet" href="assets/css/owl.css">

	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>

function sleep(milliseconds) {
 var start = new Date().getTime();
 for (var i = 0; i < 1e7; i++) {
  if ((new Date().getTime() - start) > milliseconds) {
   break;
  }
 }
}
function masDetalles() {
  $('#masDetalles').modal('show');
  //console.log('detalles');
  //setTimeout(console.log.bind(null, 'Two second later'), 2000);
  document.getElementById("form-submit").type="submit";
  //document.getElementById("form-submit").removeAttribute("data-toggle");
}

function alerta(){
	swal({
		title: 'Cotización solicitada exitosamente',
		icon: 'success',
	});
}

</script>
<style>
.banner {
	margin-top: -60px;
<?php 
if($resultado['banner']==0){
	$imagen='';if($resultado["banner_superior"]==''){$imagen=$resultado['url'].'/img/banner-sup.jpg';}else{$imagen=$resultado["url"].'/files/users/'.$resultado["id_user"].'/'.$resultado["banner_superior"];}
}else{
	$imagen=$resultado["url"].'/img/banners/'.$resultado["banner_superior"];
}?>
	background-image: url('<?php echo $imagen;?>');
	background-size: auto;
	background-repeat: no-repeat;
	/*padding: 150px 0px;*/
	background-position: center center;
}
.video-responsive {
	height: 0;
	overflow: hidden;
	padding-bottom: 56.25%;
	padding-top: 30px;
	position: relative;
	}
.video-responsive iframe, .video-responsive object, .video-responsive embed {
	height: 100%;
	left: 0;
	position: absolute;
	top: 0;
	width: 100%;
	}
	
	.cxpborder{
	  border: 1px solid <?php echo $resultado["color_web"];?>;
  }

.colorxp{
	color:#000;
}
.fuente-boton{
	font-size:18px;
}

 .button {
	cursor: pointer;
	background-color: <?php echo $resultado["color_web"];?>;
	outline: none;
	border-radius: 5px;
	padding: 10px 15px;
	display: inline-block;
	color: #fff;
	font-size: 14px;
	text-transform: uppercase;
	font-weight: 300;
	letter-spacing: 0.4px;
	text-decoration: none;
	transition: all 0.5s;
	box-shadow: none;
	border: none;
}

/* Featured Style */
.featured-items {
	margin-bottom: 20px;
}
.featured-item-negro {
	border-radius: 45px;
	border: 2px solid <?php echo $resultado["color_web"];?>;
	padding: 20px;
	transition: all 0.5s;
}
.featured-item {
	border-radius: 45px;
	border: 2px solid <?php echo $resultado["color_web"];?>;
	padding: 20px;
	transition: all 0.5s;
}
.featured-item-video {
	border-radius: 45px;
	border: 2px solid <?php echo $resultado["color_web"];?>;
	padding: 20px;
	transition: all 0.5s;
	height: 500px;
}
.featured-item:hover {
	opacity: 0.9;
}
.featured-item img {
	width: 100%;
}
.featured-item h4 {
	font-size: 17px;
	font-weight: 700;
	color: #1e1e1e;
	margin-top: 15px;
	transition: all 0.5s;
}
.featured-item:hover h4 {
	color: <?php echo $resultado["color_web"];?>;
}
.featured-item h6 {
	color: #3a8bcd;
	font-size: 15px;
	font-weight: 700;
	margin-bottom: 0px;
}
.owl-theme .owl-dots {
	text-align: center;
	margin-top: 30px;
}
.owl-theme .owl-dots .owl-dot {
	outline: none;
}
.owl-theme .owl-dots .active span {
	background-color: <?php echo $resultado["color_web"];?>!important;
}
.owl-theme .owl-dots .owl-dot span {
	background-color: #aaa;
	width: 8px;
	height: 8px;
	display: inline-block;
	margin: 0px 5px;
	outline: none;
}

.footer .social-icons a:hover {
	background-color: <?php echo $resultado["color_web"];?>;
}

h5{
	font-size: 18px;
}
	</style>
	
	<script type="text/javascript" src="assets/whatsapp/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="assets/whatsapp/floating-wpp.min.js"></script>
	<link rel="stylesheet" href="assets/whatsapp/floating-wpp.min.css">
	
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

  </head>

  <body <?php echo $onbody;?>>
	
<div id="WAButton" style="z-index:99"></div> 
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark static-top">
      <div class="container">
        <a class="navbar-brand" href="#" style="padding:0">
			<?php $imagen='';if($resultado["logo_superior"]==''){$imagen=$resultado['url'].'/img/logo-sup.jpg';}else{$imagen=$resultado['url'].'/files/users/'.$resultado["id_user"].'/'.$resultado["logo_superior"];}?>
			<img src="<?php echo $imagen;?>" alt="">
		</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <!-- Banner Starts Here -->
    <div class="banner" style="background-color:<?php echo $resultado["color_web"];?>;">
      <div class="col">
        <div class="row">
			<div class="col-md-6">
				<div  style="padding: 180px 0 0 100px;">
					<h1 style="color:#fff">
					<span style="font-size:35px"><?php echo utf8_encode($resultado["titulo"]);?></span><br>
					<span style="font-size:28px"><?php echo utf8_encode($resultado["titulob"]);?></span></h1>
				</div>
			  </div>
          <div class="col">
            <div class="caption" style="float:right">
              <form name="formularioFamiliar" id="formularioFamiliar" method="post">
                  <div class="row">
                    <div class="col-md-12">
					<h2 align="center"><b>Por favor ingresa la siguiente información</b></h2>
					<label>* Nombre y apellido</label>
                      <fieldset>
						<input name="cl" id="cl" type="hidden" value="<?php echo $resultado["codigo"];?>">
						<input name="us" id="us" type="hidden" value="<?php echo $resultado['id_user'];?>">
						<input name="emp" id="emp" type="hidden" value="<?php echo $resultado["id"];?>">
                        <input name="nombres" type="text" class="form-control cxpborder" id="name" placeholder="" required="">
                      </fieldset>
                    </div>
                    <div class="col-md-12">
					<label>* Documento de Identidad</label>
                      <fieldset>
                        <input name="identificacion" type="text" class="form-control cxpborder" id="identificacion" placeholder="1234567890" required="">
                      </fieldset>
                    </div>
                    <div class="col-md-12">
					<label>* Fecha de Nacimiento</label>
                      <fieldset>
                        <input name="fecha_nacimiento" type="date" class="form-control cxpborder" id="subject" required="">
                      </fieldset>
                    </div>
					<div class="col-md-12">
					<label>* Email</label>
                      <fieldset>
                        <input name="email" type="text" class="form-control cxpborder" id="email" placeholder="ejemplo@dominio.com" required="">
                      </fieldset>
                    </div>
					<div class="col-md-12">
					<label>* Teléfono Celular</label>
                      <fieldset>
                        <input name="telefono" type="text" class="form-control cxpborder" id="telefono" placeholder="xxx xxx" required="">
                      </fieldset>
                    </div>
					<div class="col-md-12">
					<label>* Sexo</label>
                      <fieldset>
                        <?php echo $option;?>
                      </fieldset>
                    </div>
					<div class="col-md-12">
					<label>EPS (Solo colombia)</label>
                      <fieldset>
                        <input name="eps" type="text" class="form-control cxpborder" id="eps" placeholder="">
                      </fieldset>
                    </div>
                    <div class="col-md-12" style="padding-top:20px">
						<div id="">
							<input type="hidden" name="masdetallesinput[]" id="masdetallesinput" value="">
						</div>
						<fieldset>
							<!-- Button trigger modal -->
							<div class="row">
								<span class="button btn btn-sm btn-info col-md-6" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#mas-detalles" style="background-color:#3a8bcd">
									<b class="fuente-boton">MÁS MIEMBROS</b>
								</span>
								<button type="submit" id="form-submit" class="button col-md-6" ><b class="fuente-boton">COTIZAR</b></button>
							</div>
						</fieldset>
					</div>
			
		  </div>
		</form>  
            
			  
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Banner Ends Here -->

    <!-- Featured Starts Here -->
    <div class="featured-items" style="margin-top:20px">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="">
              <div class="colorxp" align="center"><h2 class="colorxp"><?php echo utf8_encode($resultado["beneficios"]);?></h2></div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="owl-carousel owl-theme" id="owl-beneficios">
            
			<?php 
			$mysqli = new mysqli($servername, $username, $password, $database);
			/* check connection */
			if (mysqli_connect_errno()) {
				printf("Connect failed: %s\n", mysqli_connect_error());
				exit();
			}

			$query = "SELECT * FROM empresa_beneficios where id_empresa=".$resultado["id"];
			$result = $mysqli->query($query);

			while($row = $result->fetch_array())
			{
			$rows[] = $row;
			}
			if(isset($rows)){
				foreach($rows as $row)
				{
				?>
					<div class="featured-item" align="center">
					<?php if($row['default']==0){?>
					  <img src="<?php echo $resultado["url"];?>/files/users/<?php echo $resultado['id'];?>/<?php echo $row['imagen'];?>" alt="<?php echo utf8_encode($row['titulo']);?>">
					<?php }else{?>
					<img src="<?php echo $resultado["url"];?>/img/beneficio/<?php echo $row['imagen'];?>" alt="<?php echo utf8_encode($row['titulo']);?>">
					<?php }?>
					  <h4><?php echo utf8_encode($row['titulo']);?></h4>
					  <h5><?php echo utf8_encode($row['descripcion']);?></h5>
					</div>
				<?php
				}
			}
			/* free result set */
			$result->close();
			/* close connection */
			$mysqli->close();
			?>
            </div>
			<div class="offset-md-4 col-md-4" style="padding-top:20px">
			  <fieldset>
				<button type="submit" id="form-submit" class="button col-md-12" ><b class="fuente-boton">QUIERO MI COTIZACIÓN</b></button>
			  </fieldset>
			</div>
          </div>
        </div>
      </div>
    </div>
    <!-- Featred Ends Here -->
	
	<!-- Banner2 Starts Here -->
    <div class="banner2">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="">
				<div class="colorxp" align="center"><h2 class="colorxp"><?php echo utf8_encode($resultado["opiniones"]);?></h2></div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="owl-carousel owl-theme" id="owl-referidos">
              
                <?php 
			$mysqli = new mysqli($servername, $username, $password, $database);
			/* check connection */
			if (mysqli_connect_errno()) {
				printf("Connect failed: %s\n", mysqli_connect_error());
				exit();
			}

			$query = "SELECT * FROM empresa_recomendados where id_empresa=".$resultado["id"];
			$result = $mysqli->query($query);

			while($rowb = $result->fetch_array())
			{
			$rowsb[] = $rowb;
			}
			if(isset($rows)){
				foreach($rowsb as $rowb)
				{
				?>
					<div class="featured-item-negro" align="center">
					<?php if($rowb['default']==0){?>
					  <img src="<?php echo $resultado["url"];?>/files/users/<?php echo $resultado['id'];?>/<?php echo $rowb['imagen'];?>" alt="<?php echo utf8_encode($rowb['titulo']);?>">
					<?php }else{?>
					<img src="<?php echo $resultado["url"];?>/img/recomendado/<?php echo $rowb['imagen'];?>" alt="<?php echo utf8_encode($rowb['titulo']);?>">
					<?php }?>					  
					  <h4><?php echo utf8_encode($rowb['titulo']);?></h4>
					  <h5><?php echo utf8_encode($rowb['descripcion']);?></h5>
					</div>
				<?php
				}
			}
			/* free result set */
			$result->close();
			/* close connection */
			$mysqli->close();
			?>
            </div>
	
          </div>
        </div>
      
      </div>
    </div>
    <!-- Banner2 Ends Here -->
	
	<div class="featured-items">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="">
              
              <div class="colorxp" align="center" style="padding: 20px 0 10px 0;"><h2 class="colorxp"><?php echo $resultado['titulo_video'];?></h2></div>
            </div>
          </div>
          <div class="offset-md-2 col-md-8">              
                <div class="featured-item-video video-responsive" align="center">
                  <iframe width="560" height="315" src="https://www.youtube.com/embed/<?php echo $resultado['url_youtube'];?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
          </div>
		  <div class="offset-md-4 col-md-4" style="padding-top:20px">
			  <fieldset>
				<button type="submit" id="form-submit" class="button col-md-12" ><b class="fuente-boton">QUIERO MI COTIZACIÓN</b></button>
			  </fieldset>
		  </div>
		  
          </div>
        </div>
      </div>
    </div>
    
	


    <!-- Subscribe Form Starts Here -->
    <div class="subscribe-form" style="background: linear-gradient(to right, #fff, #fff, <?php echo $resultado["color_web"];?>, <?php echo $resultado["color_web"];?>);">
      <div class="container">
        <div class="row">
          <div class="col-md-3">
            <div class="section-heading">
			<?php $imagen='';if($resultado["logo_quienes_somos"]==''){$imagen=$resultado['url'].'/img/logo-quienes-somos.jpg';}else{$imagen=$resultado['url'].'/files/users/'.$resultado["id_user"].'/'.$resultado["logo_quienes_somos"];}?>
              <img src="<?php echo $imagen;?>" alt="">
            </div>
          </div>
          <div class="col-md-7 offset-md-2">
            <div class="main-content">
			<h3 style="color:#fff">¿QUIÉNES SOMOS?</h3>
              <p align="justify" style="color:#000;font-family:'Roboto';font-size:18px"><?php echo utf8_encode($resultado["quienes_somos"]);?></p>
            </div>
          </div>
		  
        </div>
      </div>
    </div>
	
	<div class="offset-md-4 col-md-4" style="padding-top:20px">
	  <fieldset>
		<button type="submit" id="form-submit" class="button col-md-12" ><b class="fuente-boton">QUIERO COMUNICARME CONTIGO</b></button>
	  </fieldset>
	</div>
    <!-- Subscribe Form Ends Here -->


    
    <!-- Footer Starts Here -->
    <div class="footer">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="logo">
			<?php $imagen='';if($resultado["logo_inferior"]==''){$imagen=$resultado['url'].'/img/logo-inferior.jpg';}else{$imagen=$resultado['url'].'/files/users/'.$resultado["id_user"].'/'.$resultado["logo_inferior"];}?>
              <img src="<?php echo $imagen;?>" alt="">
            </div>
          </div>
          <div class="col-md-12">
            <div class="social-icons">
              <ul>
                <li><a href="<?php echo $resultado["url_facebook"];?>"><i class="fa fa-facebook"></i></a></li>
                <li><a href="<?php echo $resultado["url_twitter"];?>"><i class="fa fa-twitter"></i></a></li>
                <li><a href="<?php echo $resultado["url_instagram"];?>"><i class="fa fa-instagram"></i></a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Footer Ends Here -->


    <!-- Sub Footer Starts Here -->
    <div class="sub-footer">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="copyright-text">
              <p>Copyright &copy; 2021 </p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Sub Footer Ends Here -->
 


    <!-- Additional Scripts -->
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/owl.js"></script>


    <script language = "text/Javascript"> 
      cleared[0] = cleared[1] = cleared[2] = 0; //set a cleared flag for each field
      function clearField(t){                   //declaring the array outside of the
      if(! cleared[t.id]){                      // function makes it static and global
          cleared[t.id] = 1;  // you could use true and false, but that's more typing
          t.value='';         // with more chance of typos
          t.style.color='#fff';
          }
      }
    </script>


<!-- Modal -->
<div class="modal fade" id="mas-detalles">
    <div class="modal-dialog modal-md">
        <div class="modal-content bg-default card card-dark card-outline">
            <div class="modal-header">
                <h5 class="modal-title">Ingresar más miembros</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
			<div class="modal-header">
                <div id="masdetalles" style="font-size:14px">
				</div>
            </div>

            <form name="formularioMasDetalles" id="formularioMasDetalles" enctype="multipart/form-data">
				<input type="hidden" name="cl" value="<?php echo $codigo;?>">
                <div class="modal-body row">
                    <div class="row col-sm-12">
						<div class="col-md-12">
						<label>* Nombre y apellido</label>
						  <fieldset>
							<input name="nombres" type="text" class="form-control cxpborder" id="nombres" placeholder="" required="">
						  </fieldset>
						</div>
						<div class="col-md-12">
						<label>Identificación</label>
						  <fieldset>
							<input name="identificacion" type="text" class="form-control cxpborder" id="identificacion" value="">
						  </fieldset>
						</div>
						<div class="col-md-12">
						<label>* Fecha de Nacimiento</label>
						  <fieldset>
							<input name="fecha_nacimiento" type="date" class="form-control cxpborder" id="fecha_nacimiento" required="">
						  </fieldset>
						</div>
						<div class="col-md-12">
						<label>* Sexo</label>
						  <fieldset>
							<?php echo $option;?>
						  </fieldset>
						</div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</button>
					<button type="submit" class="button btn btn-sm btn-info col-md-6" style="background-color:#3a8bcd">
						Agregar
					</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- Modal -->

  </body>
<script type="text/javascript">  
   $(function () 
   {
	   $('#WAButton').floatingWhatsApp({
		   phone: <?php echo "'".$resultado['whatsapp']."'";?>, //WhatsApp Business phone number
		   headerTitle: 'Chat', //Popup Title
		   popupMessage: 'En que puedo ayudarte?', //Popup Message
		   showPopup: true, //Enables popup display
		   buttonImage: '<img src="whatsapp.svg" />', //Button Image
		   //headerColor: 'crimson', //Custom header color
		   //backgroundColor: 'crimson', //Custom background button color
		   position: "right" //Position: left | right

	   });
   });

   var html='';//global variable  
   let arreglo=[];
   var i=0;
   $(function(){
	$("#formularioMasDetalles").on("submit", function(e) {
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			}
		});
		// Cancelamos el evento si se requiere
		e.preventDefault();
		var type = "POST";
		var ajaxurl = 'data.php?p=nm';
		var formData = new FormData(document.getElementById("formularioMasDetalles"));
		formData.append("masdetallesinput", document.getElementById("masdetallesinput").value);
			$.ajax({
				type: type,
				url: ajaxurl,
				dataType: 'json',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function () {
					document.getElementById("masdetalles").innerHTML = "cargando...";
				},
			}).done(function (data) {
				arreglo.push([data.nombres, data.identificacion, data.fecha_nacimiento, data.sexo]);
				html='';
				j=0;
				arregloinput='';
				for (var valor of arreglo) {
				  html=html+'<strong> - Nombres: </strong> '+valor[0]+' | <strong>Identicación: </strong> '+valor[1]+' | <strong>Fecha Nacimiento: </strong> '+valor[2]+'  | <strong>Sexo: </strong> '+valor[3]+' | <button class="badge badge-danger right" onclick="eliminarDetalle('+j+')"> <span clas="fa fa-remove">X</span> Eliminar</button><hr>';
				  j=j+1;
				}
				
				document.getElementById("masdetalles").innerHTML = html;
				document.getElementById("masdetallesinput").value = JSON.stringify(arreglo);
				
				$("#formularioMasDetalles").trigger("reset");
				i=i+1;

			}).fail(function (res) {
				$(".msg").html(res.b);
			});
		});
    });
	
	function eliminarDetalle(i){
		swal({
            title: "Seguro que desea eliminar?",
            text: "Presiona Ok para continuar.",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
		.then((willDelete) => {
			if (willDelete) {
				console.dir(arreglo);
				//delete arreglo[i];
				arreglo.splice(i,1);
				console.dir(arreglo);
				html='';
				j=0;
				for (var valor of arreglo) {
				  //console.dir(valor);
				  html=html+'<strong> - Nombres: </strong> '+valor[0]+' | <strong>Identicaciòn: </strong> '+valor[1]+' | <strong>Fecha Nacimiento: </strong> '+valor[2]+'  | <strong>Sexo: </strong> '+valor[3]+' | <button class="badge badge-danger right" onclick="eliminarDetalle('+j+')"> <span clas="fa fa-remove">X</span> Eliminar</button><hr>';
				  j=j+1;
				}
				document.getElementById("masdetalles").innerHTML = html;
				document.getElementById("masdetallesinput").value = JSON.stringify(arreglo);

			} else {
				//swal("No realizado");
			}
		});
	
	}
	
	$(function(){
	$("#formularioFamiliar").on("submit", function(e) {
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			}
		});
		// Cancelamos el evento si se requiere
		e.preventDefault();
		var type = "POST";
		var ajaxurl = 'data.php?p=fam';
		var formData = new FormData(document.getElementById("formularioFamiliar"));
			$.ajax({
				type: type,
				url: ajaxurl,
				dataType: 'json',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function () {
					swal({
						title: 'Cotización solicitada exitosamente...',
					});
				},
			}).done(function (data) {
				$('#modalempleados').modal('hide');
				swal({
					title: 'Cotización solicitada exitosamente',
					icon: 'success',
				});
				$("#formularioFamiliar").trigger("reset");
			}).fail(function (res) {
				$(".msg").html(res.b);
			});
		});
    });
</script> 
</html>
<?php
}else{echo 'No encontrado';}
}
 mysqli_close($conn); ?>