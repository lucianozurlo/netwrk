<?php
// sendForm.php

// Enviamos cabecera para indicar que devolveremos JSON
header('Content-Type: application/json; charset=utf-8');

// Verificamos que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Identificar cuál formulario es
    //    (viene de <input type="hidden" name="form_id" value="...">)
    $formId = isset($_POST['form_id']) ? $_POST['form_id'] : '';

    // Según sea form1 o form2 definimos el destinatario
    if ($formId === 'form1') {
        $recipient = "lucianozurlo@gmail.com";
    } else {
        $recipient = "em24.teco@gmail.com";
    }

    // 2. Recoger datos comunes
    $fullname       = isset($_POST['fullname'])        ? trim($_POST['fullname'])        : '';
    $email          = isset($_POST['email'])           ? trim($_POST['email'])           : '';
    $companyName    = isset($_POST['company-name'])    ? trim($_POST['company-name'])    : '';
    $currentRound   = isset($_POST['current-round'])   ? trim($_POST['current-round'])   : '';
    $raiseAmount    = isset($_POST['raise-amount'])    ? trim($_POST['raise-amount'])    : '';
    $valuation      = isset($_POST['valuation'])       ? trim($_POST['valuation'])       : '';
    $revenue        = isset($_POST['revenue'])         ? trim($_POST['revenue'])         : '';
    $elevatorPitch  = isset($_POST['elevator-pitch'])  ? trim($_POST['elevator-pitch'])  : '';

    // 3. Construir el mensaje
    $message  = "You have received a new form submission.\n\n";
    $message .= "Full Name: $fullname\n";
    $message .= "Email: $email\n";
    if ($companyName)   $message .= "Company Name: $companyName\n";
    if ($currentRound)  $message .= "Current Round: $currentRound\n";
    if ($raiseAmount)   $message .= "Raise Amount: $raiseAmount\n";
    if ($valuation)     $message .= "Valuation: $valuation\n";
    if ($revenue)       $message .= "Revenue: $revenue\n";
    if ($elevatorPitch) $message .= "Elevator Pitch: $elevatorPitch\n";

    // 4. Asunto del correo (puedes personalizarlo más)
    $subject = "New Submission from $fullname";

    // 5. Cabeceras (From, Reply-to, etc.)
    $headers = "From: no-reply@yourdomain.com\r\n";
    $headers .= "Reply-To: $email\r\n";

    // 6. Envío del correo con la función nativa de PHP
    //    (OJO: en algunos servidores locales/hospedajes hace falta configuración adicional)
    $sent = mail($recipient, $subject, $message, $headers);

    // 7. Preparar respuesta en JSON
    if ($sent) {
        echo json_encode([
            "status"  => "success",
            "message" => "Your message was successfully sent."
        ]);
    } else {
        echo json_encode([
            "status"  => "error",
            "message" => "An error occurred while sending the message. Please try again later."
        ]);
    }

} else {
    // Método no permitido
    echo json_encode([
        "status"  => "error",
        "message" => "Method not allowed."
    ]);
}
