<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require("class.smtp.php");
require("class.phpmailer.php");

if(!isset($_POST)) {
    die;
}

$name =  filter_var($_POST['Nombre'], FILTER_SANITIZE_STRING);
$place = filter_var($_POST['Lugar'], FILTER_SANITIZE_STRING);
$email = filter_var($_POST['Email'], FILTER_VALIDATE_EMAIL);
$celular = preg_replace('/[^0-9+-]/', '', filter_var($_POST['Celular'], FILTER_SANITIZE_STRING));
$pregunta = filter_var($_POST['Pregunta'], FILTER_SANITIZE_STRING);
// O Utilizar 
// $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);


/// CAPTCHA
$captcha = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
if(!$captcha){
	echo '<h2>Please check the the captcha form.</h2>';
	exit;
}

$secretKey = "6LehkpsUAAAAANvrBAKEEP2W6O4q8O2K4cwGLOVf";
$ip = $_SERVER['REMOTE_ADDR'];

// post request to server
  $url = 'https://www.google.com/recaptcha/api/siteverify';
  $data = array('secret' => $secretKey, 'response' => $captcha);

  $options = array(
    'http' => array(
      'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
      'method'  => 'POST',
      'content' => http_build_query($data)
    )
  );
  $context  = stream_context_create($options);
  $response = file_get_contents($url, false, $context);
  $responseKeys = json_decode($response,true);

// END CAPTCHA

header('Content-Type: application/json');

$data = [ 'code' => 0, 'message' => '' ];

// El captcha salio todo bien
IF($responseKeys["success"]) {

	// Valores enviados desde el formulario
	if (   !isset($name) || !isset($place) 
	    || !isset($email) || !isset($celular) || !isset($pregunta)) {
	    $data['code']    = 500;
	    $data['message'] = 'Campos incompletos';
	    echo json_encode( $data );
	    die;
	}

	$mail = new PHPMailer();

	$mail->IsSMTP();

	$mail->SMTPAuth = true;

	// SMTP a utilizar. Por ej. smtp.elserver.com
	//$mail->Host = "c0990242.ferozo.com"; 
	// // Correo completo a utilizar
	//$mail->Username = "comunicacion@massadirecto.com"; 
	//$mail->Password = "6*@S*WL5eZ1"; // Contraseña
	//$mail->Port = 465; // Puerto a utilizar
	//$mail->SMTPSecure = 'ssl';
	//$mail->CharSet = "utf-8";

	// //SMTP a utilizar. Por ej. smtp.elserver.com
	$mail->Host = "smtp.gmail.com"; 
	// Correo completo a utilizar
	$mail->Username = "sitesdemassa@gmail.com"; 
	$mail->Password = "Sitios?2019?"; // Contraseña
	$mail->Port = 465; // Puerto a utilizar
	$mail->SMTPSecure = 'ssl';
	$mail->CharSet = "utf-8";


	$mail->From = "sitesdemassa@gmail.com"; // Desde donde enviamos (Para mostrar)
	$mail->FromName = "Sergio Massa - Massa directo";
	$mail->AddAddress("sitesdemassa@gmail.com"); // Esta es la dirección a donde enviamos

	//$mail->AddCC("Copia@Copia.com");
	//$mail->AddBCC("CopiaOculta@CopiaOculta.com");

	// El correo se envía como HTML
	$mail->IsHTML(true); 

	// Este es el asunto
	$mail->Subject = "Massa directo -  " . $name;

	$body = "Han realizado una pregunta: " . $pregunta .
	        "<br/>Nombre: "    . $name    .
	        "<br/>Email: "     . $email   . 
	        "<br/>Provincia: " . $place   . 
	        "<br/>Celular: "   . $celular;

	$mail->Body = $body; // Mensaje a enviar
	//$mail->AltBody = "Hola mundo. Esta es la primer línean Acá continuo el mensaje"; // Texto sin html
	//$mail->AddAttachment("imagenes/imagen.jpg", "imagen.jpg");

	$exito = $mail->Send(); // Envía el correo.

	if($exito) {
	    $data['code']    = 200;
	    $data['message'] = '¡Muchas gracias! Pronto nos pondremos en contacto.';
	} else {
	    $data['code']    = 500;
	    $data['message'] = 'Hubo un error al guardar el mensaje, reintenta en unos minutos.';
	}

} 
else {
	//todo mal con el captcha
	$data['code']    = 500;
	$data['message'] = "Ha ocurrido un error";
}

echo json_encode( $data );
?>