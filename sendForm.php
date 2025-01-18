<?php
// sendForm.php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formId = isset($_POST['form_id']) ? $_POST['form_id'] : '';

    // Campos comunes (pueden o no existir en el POST)
    $fullname       = isset($_POST['fullname'])        ? trim($_POST['fullname'])        : '';
    $email          = isset($_POST['email'])           ? trim($_POST['email'])           : '';
    $companyName    = isset($_POST['company-name'])    ? trim($_POST['company-name'])    : '';
    $currentRound   = isset($_POST['current-round'])   ? trim($_POST['current-round'])   : '';
    $raiseAmount    = isset($_POST['raise-amount'])    ? trim($_POST['raise-amount'])    : '';
    $valuation      = isset($_POST['valuation'])       ? trim($_POST['valuation'])       : '';
    $revenue        = isset($_POST['revenue'])         ? trim($_POST['revenue'])         : '';
    $elevatorPitch  = isset($_POST['elevator-pitch'])  ? trim($_POST['elevator-pitch'])  : '';

    // Definir destinatario según sea form1 o form2
    if ($formId === 'form1') {
        $recipient = "lucianozurlo@gmail.com";
        // Validación obligatoria del primer formulario
        if (empty($fullname) || empty($email)) {
            echo json_encode([
                "status"  => "error",
                "message" => "Full Name and Email are required fields."
            ]);
            exit; // detenemos ejecución
        }
    } else {
        // Asumimos que es form2, si solo hay esos dos formularios
        $recipient = "em24.teco@gmail.com";
        // Aquí podrías validar también si quieres campos obligatorios en form2
    }

    // Construir el mensaje
    $message  = "You have received a new form submission.\n\n";
    $message .= "Full Name: $fullname\n";
    $message .= "Email: $email\n";

    // Solo agregamos más campos si existen
    if ($companyName)   $message .= "Company Name: $companyName\n";
    if ($currentRound)  $message .= "Current Round: $currentRound\n";
    if ($raiseAmount)   $message .= "Raise Amount: $raiseAmount\n";
    if ($valuation)     $message .= "Valuation: $valuation\n";
    if ($revenue)       $message .= "Revenue: $revenue\n";
    if ($elevatorPitch) $message .= "Elevator Pitch: $elevatorPitch\n";

    // Asunto
    $subject = "New Submission from $fullname";

    // Cabeceras
    $headers = "From: no-reply@yourdomain.com\r\n";
    $headers .= "Reply-To: $email\r\n";

    // Intentar enviar
    $sent = mail($recipient, $subject, $message, $headers);

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
