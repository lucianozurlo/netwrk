<?php
// sendForm.php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Identificar cuál formulario es
    $formId = isset($_POST['form_id']) ? $_POST['form_id'] : '';

    // 2. Definir destinatario, nombre interno y campos obligatorios según formulario
    if ($formId === 'form1') {
        // "Invest With Us"
        $recipient      = "lucianozurlo@gmail.com";
        $formName       = "Invest With Us";
        $requiredFields = ['fullname','email'];
    } elseif ($formId === 'form2') {
        // "Apply for Funding"
        $recipient      = "em24.teco@gmail.com";
        $formName       = "Apply for Funding";
        $requiredFields = ['fullname','email','company-name'];
    } else {
        // Formulario desconocido
        echo json_encode([
            "status"  => "error",
            "message" => "Unknown form identifier."
        ]);
        exit;
    }

    // 3. Recoger datos
    $fullname      = isset($_POST['fullname'])        ? trim($_POST['fullname'])        : '';
    $email         = isset($_POST['email'])           ? trim($_POST['email'])           : '';
    $companyName   = isset($_POST['company-name'])    ? trim($_POST['company-name'])    : '';
    $currentRound  = isset($_POST['current-round'])   ? trim($_POST['current-round'])   : '';
    $raiseAmount   = isset($_POST['raise-amount'])    ? trim($_POST['raise-amount'])    : '';
    $valuation     = isset($_POST['valuation'])       ? trim($_POST['valuation'])       : '';
    $revenue       = isset($_POST['revenue'])         ? trim($_POST['revenue'])         : '';
    $elevatorPitch = isset($_POST['elevator-pitch'])  ? trim($_POST['elevator-pitch'])  : '';

    // 4. Validación de los campos obligatorios y formato de email
    $errors = [];

    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            // Mensajes personalizados según el campo
            switch ($field) {
                case 'fullname':
                    $errors[] = "Full Name is required.";
                    break;
                case 'email':
                    $errors[] = "Email is required.";
                    break;
                case 'company-name':
                    $errors[] = "Company Name is required.";
                    break;
                default:
                    $errors[] = "Field '$field' is required.";
                    break;
            }
        }
    }

    // Validar formato de e-mail (si no está vacío)
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Si hay errores, devolvemos JSON y terminamos
    if (!empty($errors)) {
        $errorMsg = implode(" ", $errors);
        echo json_encode([
            "status"  => "error",
            "message" => $errorMsg
        ]);
        exit;
    }

    // 5. Construir el Asunto
    $subject = "New message from netwrkventures.com - $formName";

    // 6. Construir el Cuerpo del Mensaje (nuevo formato)
    $message = "You have received a new message from netwrkventures.com.\n\n";
    $message .= "=== $formName ===\n\n";  // Encabezado

    // Sección: Contact Information
    $message .= "Contact Information:\n";
    $message .= "--------------------\n";
    $message .= "Full Name: $fullname\n";
    $message .= "Email:     $email\n";

    // Sección: Company Information (solo si hay datos)
    if (!empty($companyName) ||
        !empty($currentRound) ||
        !empty($raiseAmount) ||
        !empty($valuation)   ||
        !empty($revenue)     ||
        !empty($elevatorPitch)
    ) {
        $message .= "\nCompany Information:\n";
        $message .= "--------------------\n";
        if ($companyName)   $message .= "Company Name:   $companyName\n";
        if ($currentRound)  $message .= "Current Round:  $currentRound\n";
        if ($raiseAmount)   $message .= "Raise Amount:   $raiseAmount\n";
        if ($valuation)     $message .= "Valuation:      $valuation\n";
        if ($revenue)       $message .= "Revenue:        $revenue\n";
        if ($elevatorPitch) $message .= "Elevator Pitch: $elevatorPitch\n";
    }

    // 7. Cabeceras
    $headers = "From: no-reply@netwrkventures.com\r\n";
    $headers .= "Reply-To: $email\r\n";

    // 8. Enviar correo
    $sent = mail($recipient, $subject, $message, $headers);

    // 9. Responder en JSON
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
