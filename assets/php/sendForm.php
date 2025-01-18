<?php
// sendForm.php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status"  => "error",
        "message" => "Method not allowed."
    ]);
    exit;
}

// 1. Identificar cuál formulario es
$formId = isset($_POST['form_id']) ? $_POST['form_id'] : '';

// 2. Definir variables según formulario
if ($formId === 'form1') {
    // "Invest With Us"
    $recipient = "repoarchivos@gmail.com";
    $formName  = "Invest With Us";
    $isForm2   = false; // para saber si requerimos "company-name"
} elseif ($formId === 'form2') {
    // "Apply for Funding"
    $recipient = "em24.teco@gmail.com";
    $formName  = "Apply for Funding";
    $isForm2   = true;  // requiere "company-name"
} else {
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

/*
  4. Validación en cadena
     - Primero chequeamos "fullname"
     - Luego chequeamos "email"
     - Si es form2, chequeamos "company-name"
     - Cualquier error => devolvemos JSON y detenemos ejecución.
*/

// 4.1 Validar fullname
if (empty($fullname)) {
    echo json_encode([
        "status"  => "error",
        "message" => "Full Name is required."
    ]);
    exit;
}

// 4.2 Validar email (que no esté vacío)
if (empty($email)) {
    echo json_encode([
        "status"  => "error",
        "message" => "Email is required."
    ]);
    exit;
}
// 4.3 Validar formato de email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "status"  => "error",
        "message" => "Invalid email format."
    ]);
    exit;
}

// 4.4 Si es el formulario 2, validar "company-name"
if ($isForm2) {
    if (empty($companyName)) {
        echo json_encode([
            "status"  => "error",
            "message" => "Company Name is required."
        ]);
        exit;
    }
}

// 5. Construir Asunto
$subject = "New message from netwrkventures.com - $formName";

// 6. Construir el Cuerpo del Mensaje (nuevo formato)
$message = "You have received a new message from netwrkventures.com.\n\n";
$message .= "=== $formName ===\n\n";  // Encabezado

// Sección: Contact Information
$message .= "Contact Information:\n";
$message .= "--------------------\n";
$message .= "Full Name: $fullname\n";
$message .= "Email:     $email\n";

// Sección: Company Information (solo si hay datos o si es form2 con datos)
if (
    !empty($companyName) ||
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
$headers  = "From: no-reply@netwrkventures.com\r\n";
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
