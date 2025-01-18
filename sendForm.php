<?php
// sendForm.php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Identificar cuál formulario es
    $formId = isset($_POST['form_id']) ? $_POST['form_id'] : '';

    // Definir a quién enviamos y qué campos son obligatorios, según el formulario
    if ($formId === 'form1') {
        // Enviar a lucianozurlo@gmail.com, y requerir fullname + email
        $recipient = "lucianozurlo@gmail.com";
        $requiredFields = ['fullname','email'];

    } elseif ($formId === 'form2') {
        // Enviar a em24.teco@gmail.com, y requerir fullname + email + company-name
        $recipient = "em24.teco@gmail.com";
        $requiredFields = ['fullname','email','company-name'];

    } else {
        // Por si llega un form_id inesperado
        echo json_encode([
            "status"  => "error",
            "message" => "Unknown form identifier."
        ]);
        exit;
    }

    // Recoger datos (comunes a ambos forms)
    $fullname      = isset($_POST['fullname'])        ? trim($_POST['fullname'])        : '';
    $email         = isset($_POST['email'])           ? trim($_POST['email'])           : '';
    $companyName   = isset($_POST['company-name'])    ? trim($_POST['company-name'])    : '';
    $currentRound  = isset($_POST['current-round'])   ? trim($_POST['current-round'])   : '';
    $raiseAmount   = isset($_POST['raise-amount'])    ? trim($_POST['raise-amount'])    : '';
    $valuation     = isset($_POST['valuation'])       ? trim($_POST['valuation'])       : '';
    $revenue       = isset($_POST['revenue'])         ? trim($_POST['revenue'])         : '';
    $elevatorPitch = isset($_POST['elevator-pitch'])  ? trim($_POST['elevator-pitch'])  : '';

    // 1) Validación de los campos obligatorios en servidor
    $errors = [];
    foreach ($requiredFields as $field) {
        // Verificamos cada campo según su 'key' en $_POST
        if (empty($_POST[$field])) {
            // Añadir un mensaje de error por cada campo vacío
            $errors[] = "Field '$field' is required.";
        }
    }

    // Si hay errores, retornamos respuesta JSON con status=error
    if (!empty($errors)) {
        // Podrías concatenar todos los errores en un solo string
        $errorMsg = implode(" ", $errors);
        echo json_encode([
            "status"  => "error",
            "message" => $errorMsg
        ]);
        exit;
    }

    // 2) Construir el mensaje
    $message = "You have received a new form submission.\n\n";
    $message .= "Full Name: $fullname\n";
    $message .= "Email: $email\n";
    if ($companyName)   $message .= "Company Name: $companyName\n";
    if ($currentRound)  $message .= "Current Round: $currentRound\n";
    if ($raiseAmount)   $message .= "Raise Amount: $raiseAmount\n";
    if ($valuation)     $message .= "Valuation: $valuation\n";
    if ($revenue)       $message .= "Revenue: $revenue\n";
    if ($elevatorPitch) $message .= "Elevator Pitch: $elevatorPitch\n";

    // 3) Asunto del correo
    $subject = "New Submission from $fullname";

    // 4) Cabeceras
    $headers = "From: no-reply@yourdomain.com\r\n";
    $headers .= "Reply-To: $email\r\n";

    // 5) Enviar correo
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
