<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recogemos los datos
    $fullname      = isset($_POST['fullname2'])       ? trim($_POST['fullname2'])       : '';
    $email         = isset($_POST['email2'])          ? trim($_POST['email2'])          : '';
    $companyName   = isset($_POST['company_name'])     ? trim($_POST['company_name'])    : '';
    $currentRound  = isset($_POST['current_round'])    ? trim($_POST['current_round'])   : '';
    $raiseAmount   = isset($_POST['raise_amount'])     ? trim($_POST['raise_amount'])    : '';
    $valuation     = isset($_POST['valuation'])        ? trim($_POST['valuation'])       : '';
    $revenue       = isset($_POST['revenue'])          ? trim($_POST['revenue'])         : '';
    $elevatorPitch = isset($_POST['elevator_pitch'])   ? trim($_POST['elevator_pitch'])  : '';

    // Destinatario
    $destinatario = "formtesting117@gmail.com";

    // Asunto
    $asunto = "Nuevo contacto desde Formulario 2";

    // Armamos el mensaje
    $mensaje  = "Se ha recibido una nueva consulta.\n\n";
    $mensaje .= "Nombre: $fullname\n";
    $mensaje .= "Email: $email\n";
    $mensaje .= "Company Name: $companyName\n";
    $mensaje .= "Current Round: $currentRound\n";
    $mensaje .= "Raise Amount: $raiseAmount\n";
    $mensaje .= "Valuation: $valuation\n";
    $mensaje .= "Revenue: $revenue\n\n";
    $mensaje .= "Elevator Pitch:\n$elevatorPitch\n";

    // Cabeceras
    $headers = "From: no-reply@tusitio.com\r\n";

    // Enviamos el correo
    $enviado = mail($destinatario, $asunto, $mensaje, $headers);

    if ($enviado) {
        echo "<h3>¡Correo enviado con éxito!</h3>";
        echo "<p>Gracias por tu información, $fullname. Estaremos en contacto.</p>";
    } else {
        echo "<h3>Ocurrió un error al enviar el correo.</h3>";
        echo "<p>Por favor, inténtalo de nuevo más tarde.</p>";
    }

} else {
    echo "<h3>Acceso no válido.</h3>";
}
?>
