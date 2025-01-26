<?php
// sendForm.php
header('Content-Type: application/json; charset=utf-8');

// Permitir solo solicitudes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status"  => "error",
        "message" => "Method not allowed."
    ]);
    exit;
}

// 1. Identificar qué formulario se está enviando
$formId = isset($_POST['form_id']) ? $_POST['form_id'] : '';

if ($formId === 'form1') {
    // "Invest With Us"
    $recipient = "ir@netwrkventures.com"; 
    $formName  = "Invest With Us";
    $isForm2   = false;
} elseif ($formId === 'form2') {
    // "Apply for Funding"
    $recipient = "funding@netwrkventures.com"; 
    $formName  = "Apply for Funding";
    $isForm2   = true;
} else {
    echo json_encode([
        "status"  => "error",
        "message" => "Unknown form identifier."
    ]);
    exit;
}

// 3. Recoger datos del formulario
$fullname      = isset($_POST['fullname'])        ? trim($_POST['fullname'])        : '';
$email         = isset($_POST['email'])           ? trim($_POST['email'])           : '';
$companyName   = isset($_POST['company-name'])    ? trim($_POST['company-name'])    : '';
$currentRound  = isset($_POST['current-round'])   ? trim($_POST['current-round'])   : '';
$raiseAmount   = isset($_POST['raise-amount'])    ? trim($_POST['raise-amount'])    : '';
$valuation     = isset($_POST['valuation'])       ? trim($_POST['valuation'])       : '';
$revenue       = isset($_POST['revenue'])         ? trim($_POST['revenue'])         : '';
$elevatorPitch = isset($_POST['elevator-pitch'])  ? trim($_POST['elevator-pitch'])  : '';

// 4. Validación secuencial de campos

// 4.1 Validar Full Name
if (empty($fullname)) {
    echo json_encode([
        "status"  => "error",
        "message" => "Full Name is required."
    ]);
    exit;
}

// 4.2 Validar Email (no vacío)
if (empty($email)) {
    echo json_encode([
        "status"  => "error",
        "message" => "Email is required."
    ]);
    exit;
}

// 4.3 Validar formato de Email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "status"  => "error",
        "message" => "Invalid email format."
    ]);
    exit;
}

// 4.4 Si es Formulario 2, validar Company Name
if ($isForm2) {
    if (empty($companyName)) {
        echo json_encode([
            "status"  => "error",
            "message" => "Company Name is required."
        ]);
        exit;
    }
}

// 5. Manejar la carga de archivos (solo para Formulario 2)
$fileContent = '';
$fileName = '';
$fileType = '';
$attachment = false;

if ($isForm2 && isset($_FILES['company-deck']) && $_FILES['company-deck']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['company-deck']['tmp_name'];
    $fileName    = $_FILES['company-deck']['name'];
    $fileSize    = $_FILES['company-deck']['size'];
    $fileType    = $_FILES['company-deck']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    // Validar extensión de archivo
    $allowedfileExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    if (!in_array($fileExtension, $allowedfileExtensions)) {
        echo json_encode([
            "status"  => "error",
            "message" => "Invalid file type. Only PDF, DOC, DOCX, JPG, and PNG files are allowed."
        ]);
        exit;
    }

    // Opcional: Validar tamaño de archivo (ejemplo: máximo 5MB)
    if ($fileSize > 5 * 1024 * 1024) { // 5MB
        echo json_encode([
            "status"  => "error",
            "message" => "Uploaded file is too large. Maximum size is 5MB."
        ]);
        exit;
    }

    // Leer el contenido del archivo
    $fileContent = chunk_split(base64_encode(file_get_contents($fileTmpPath)));
    $attachment = true;
}

// 6. Construir el Asunto del Email
$subject = "New message from netwrkventures.com - $formName";

// 7. Construir el Cuerpo del Mensaje
if ($attachment) {
    // Construir un email con adjunto (MIME multipart)
    $boundary = md5(time());

    // Cabeceras para email multipart
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "From: no-reply@netwrkventures.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

    // Cuerpo del email
    $message = "--{$boundary}\r\n";
    $message .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
    $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $message .= "You have received a new message from netwrkventures.com.\n\n";
    $message .= "=== {$formName} ===\n\n";
    $message .= "Contact Information:\n";
    $message .= "--------------------\n";
    $message .= "Full Name: {$fullname}\n";
    $message .= "Email:     {$email}\n";

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
        if ($companyName)   $message .= "Company Name:   {$companyName}\n";
        if ($currentRound)  $message .= "Current Round:  {$currentRound}\n";
        if ($raiseAmount)   $message .= "Raise Amount:   {$raiseAmount}\n";
        if ($valuation)     $message .= "Valuation:      {$valuation}\n";
        if ($revenue)       $message .= "Revenue:        {$revenue}\n";
        if ($elevatorPitch) $message .= "Elevator Pitch: {$elevatorPitch}\n";
    }

    // Adjuntar el archivo
    $message .= "\r\n--{$boundary}\r\n";
    $message .= "Content-Type: {$fileType}; name=\"{$fileName}\"\r\n";
    $message .= "Content-Transfer-Encoding: base64\r\n";
    $message .= "Content-Disposition: attachment; filename=\"{$fileName}\"\r\n\r\n";
    $message .= "{$fileContent}\r\n";
    $message .= "--{$boundary}--";
} else {
    // Construir un email sin adjunto
    $headers  = "From: no-reply@netwrkventures.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";

    // Cuerpo del email
    $message  = "You have received a new message from netwrkventures.com.\n\n";
    $message .= "=== {$formName} ===\n\n";
    $message .= "Contact Information:\n";
    $message .= "--------------------\n";
    $message .= "Full Name: {$fullname}\n";
    $message .= "Email:     {$email}\n";

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
        if ($companyName)   $message .= "Company Name:   {$companyName}\n";
        if ($currentRound)  $message .= "Current Round:  {$currentRound}\n";
        if ($raiseAmount)   $message .= "Raise Amount:   {$raiseAmount}\n";
        if ($valuation)     $message .= "Valuation:      {$valuation}\n";
        if ($revenue)       $message .= "Revenue:        {$revenue}\n";
        if ($elevatorPitch) $message .= "Elevator Pitch: {$elevatorPitch}\n";
    }
}

// 8. Enviar el Email
$sent = mail($recipient, $subject, $message, $headers);

// 9. Responder con JSON según el resultado
if ($sent) {
    if ($isForm2) {
        // Mensaje de éxito para "Apply for Funding"
        $successMessage = "Thank you for applying for funding. Your message has been sent.";
    } else {
        // Mensaje de éxito para "Invest With Us"
        $successMessage = "Thank you for your interest in investing with us. Your message has been sent.";
    }

    echo json_encode([
        "status"  => "success",
        "message" => $successMessage
    ]);
} else {
    echo json_encode([
        "status"  => "error",
        "message" => "An error occurred while sending the message. Please try again later."
    ]);
}
?>
