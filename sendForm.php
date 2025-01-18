<?php
// sendForm.php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Identificar cuál formulario es
    $formId = isset($_POST['form_id']) ? $_POST['form_id'] : '';

    // Para distinguir los formularios:
    if ($formId === 'form1') {
        // Formulario 1: "Invest With Us"
        $recipient   = "lucianozurlo@gmail.com";
        $formName    = "Invest With Us";
    } elseif ($formId === 'form2') {
        // Formulario 2: "Apply for Funding"
        $recipient   = "em24.teco@gmail.com";
        $formName    = "Apply for Funding";
    } else {
        echo json_encode([
            "status"  => "error",
            "message" => "Unknown form identifier."
        ]);
        exit;
    }

    // Recoger datos de ambos forms
    $fullname      = isset($_POST['fullname'])        ? trim($_POST['fullname'])        : '';
    $email         = isset($_POST['email'])           ? trim($_POST['email'])           : '';
    $companyName   = isset($_POST['company-name'])    ? trim($_POST['company-name'])    : '';
    $currentRound  = isset($_POST['current-round'])   ? trim($_POST['current-round'])   : '';
    $raiseAmount   = isset($_POST['raise-amount'])    ? trim($_POST['raise-amount'])    : '';
    $valuation     = isset($_POST['valuation'])       ? trim($_POST['valuation'])       : '';
    $revenue       = isset($_POST['revenue'])         ? trim($_POST['revenue'])         : '';
    $elevatorPitch = isset($_POST['elevator-pitch'])  ? trim($_POST['elevator-pitch'])  : '';

    // --------------------------------------
    // Construir asunto y cuerpo del mensaje
    // --------------------------------------
    $subject = "New message from netwrkventures.com - $formName";

    // Cuerpo del correo
    $message .= "You have received a new message from netwrkventures.com.\n\n";
    $message .= "=== $formName ===\n\n";  // Encabezado sin “FORM:”

    // Sección: Contact Information
    $message .= "Contact Information:\n";
    $message .= "--------------------\n";
    $message .= "Full Name: $fullname\n";
    $message .= "Email:     $email\n";

    // Sección: Company Information (si aplica o si hay datos)
    if (!empty($companyName) ||
        !empty($currentRound) ||
        !empty($raiseAmount) ||
        !empty($valuation)   ||
        !empty($revenue)     ||
        !empty($elevatorPitch)) {

        $message .= "\nCompany Information:\n";
        $message .= "--------------------\n";
        if ($companyName)   $message .= "Company Name:   $companyName\n";
        if ($currentRound)  $message .= "Current Round:  $currentRound\n";
        if ($raiseAmount)   $message .= "Raise Amount:   $raiseAmount\n";
        if ($valuation)     $message .= "Valuation:      $valuation\n";
        if ($revenue)       $message .= "Revenue:        $revenue\n";
        if ($elevatorPitch) $message .= "Elevator Pitch: $elevatorPitch\n";
    }

    // Cabeceras
    $headers  = "From: no-reply@netwrkventures.com\r\n"; 
    $headers .= "Reply-To: $email\r\n";

    // Enviar correo
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
