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

// 1. Identificamos cuál formulario es
$formId = isset($_POST['form_id']) ? $_POST['form_id'] : '';

// 2. Configuramos los datos según el formulario
switch ($formId) {
    case 'form1':
        // "Invest With Us"
        $recipient       = "lucianozurlo@gmail.com";
        $formName        = "Invest With Us";
        // Campos obligatorios para form1
        $requiredFields  = ['fullname', 'email'];
        break;

    case 'form2':
        // "Apply for Funding"
        $recipient       = "em24.teco@gmail.com";
        $formName        = "Apply for Funding";
        // Campos obligatorios para form2
        $requiredFields  = ['fullname', 'email', 'company-name'];
        break;

    default:
        echo json_encode([
            "status"  => "error",
            "message" => "Unknown form identifier."
        ]);
        exit;
}

// 3. Recogemos los campos comunes
$fullname      = isset($_POST['fullname'])        ? trim($_POST['fullname'])        : '';
$email         = isset($_POST['email'])           ? trim($_POST['email'])           : '';
$companyName   = isset($_POST['company-name'])    ? trim($_POST['company-name'])    : '';
$currentRound  = isset($_POST['current-round'])   ? trim($_POST['current-round'])   : '';
$raiseAmount   = isset($_POST['raise-amount'])    ? trim($_POST['raise-amount'])    : '';
$valuation     = isset($_POST['valuation'])       ? trim($_POST['valuation'])       : '';
$revenue       = isset($_POST['revenue'])         ? trim($_POST['revenue'])         : '';
$elevatorPitch = isset($_POST['elevator-pitch'])  ? trim($_POST['elevator-pitch'])  : '';

// 4. Validaciones campo a campo
$errors = [];

// Validar campos obligatorios
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        // Si quieres, puedes personalizar el mensaje según el campo.
        // Ejemplo: 'fullname' => 'Full Name is required.'
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
            // Agrega más si tienes más campos obligatorios
            default:
                $errors[] = "Field '$field' is required.";
                break;
        }
    }
}

// Validar formato de e-mail si no está vacío (y si 'email' es un campo requerido)
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
}

// Si hay errores, paramos y enviamos mensaje
if (!empty($errors)) {
    $errorMsg = implode(" ", $errors); 
    // Podrías concatenar con salto de línea, si prefieres:
    // $errorMsg = implode("\n", $errors);
    echo json_encode([
        "status"  => "error",
        "message" => $errorMsg
    ]);
    exit;
}

// 5. Construir asunto y cuerpo del mensaje con mejor formato/redacción
$subject = "New message from netwrkventures.com - $formName";

$message  = "Hello,\n\n";
$message .= "You have received a new message from netwrkventures.com.\n\n";
$message .= "=== $formName ===\n\n";  

// Sección: Contact Information
$message .= "Contact Information:\n";
$message .= "--------------------\n";
$message .= "Full Name: $fullname\n";
$message .= "Email:     $email\n";

// Sección: Company Information (solo si hay datos o es un formulario que use esos campos)
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

// 6. Cabeceras
$headers  = "From: no-reply@netwrkventures.com\r\n";
$headers .= "Reply-To: $email\r\n";

// 7. Enviar el correo con mail()
$sent = mail($recipient, $subject, $message, $headers);

// 8. Responder en formato JSON
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
