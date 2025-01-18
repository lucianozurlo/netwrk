<?php
// sendForm.php
header('Content-Type: application/json; charset=utf-8');

// Permitir solo solicitudes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status"  => "error",
        "message" => "Método no permitido."
    ]);
    exit;
}

// 1. Identificar qué formulario se está enviando
$formId = isset($_POST['form_id']) ? $_POST['form_id'] : '';

// 2. Configurar destinatarios y nombres de formulario según el ID
if ($formId === 'form1') {
    // "Invest With Us"
    $recipient = "lucianozurlo@gmail.com";
    $formName  = "Invest With Us";
    $isForm2   = false;
} elseif ($formId === 'form2') {
    // "Apply for Funding"
    $recipient = "em24.teco@gmail.com";
    $formName  = "Apply for Funding";
    $isForm2   = true;
} else {
    // Identificador de formulario desconocido
    echo json_encode([
        "status"  => "error",
        "message" => "Identificador de formulario desconocido."
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
        "message" => "El nombre completo es obligatorio."
    ]);
    exit;
}

// 4.2 Validar Email (no vacío)
if (empty($email)) {
    echo json_encode([
        "status"  => "error",
        "message" => "El correo electrónico es obligatorio."
    ]);
    exit;
}

// 4.3 Validar formato de Email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "status"  => "error",
        "message" => "Formato de correo electrónico inválido."
    ]);
    exit;
}

// 4.4 Si es Formulario 2, validar Company Name
if ($isForm2) {
    if (empty($companyName)) {
        echo json_encode([
            "status"  => "error",
            "message" => "El nombre de la empresa es obligatorio."
        ]);
        exit;
    }
}

// 5. Manejar la carga de archivos (solo para Formulario 2)
$fileContent = '';
$fileName = '';
$fileType = '';
$attachment = false;

if ($isForm2 && isset($_FILES['company-deck']) && $_FILES['company-deck']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['company-deck']['error'] === UPLOAD_ERR_OK) {
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
                "message" => "Tipo de archivo inválido. Solo se permiten PDF, DOC, DOCX, JPG y PNG."
            ]);
            exit;
        }

        // Opcional: Validar tamaño de archivo (ejemplo: máximo 5MB)
        if ($fileSize > 5 * 1024 * 1024) { // 5MB
            echo json_encode([
                "status"  => "error",
                "message" => "El archivo adjunto es demasiado grande. El tamaño máximo es de 5MB."
            ]);
            exit;
        }

        // Leer el contenido del archivo
        $fileContent = chunk_split(base64_encode(file_get_contents($fileTmpPath)));
        $attachment = true;
    } else {
        // Manejar otros errores de subida
        $error_messages = [
            UPLOAD_ERR_INI_SIZE   => "El archivo excede la directiva upload_max_filesize en php.ini.",
            UPLOAD_ERR_FORM_SIZE  => "El archivo excede la directiva MAX_FILE_SIZE especificada en el formulario HTML.",
            UPLOAD_ERR_PARTIAL    => "El archivo solo se subió parcialmente.",
            UPLOAD_ERR_NO_TMP_DIR => "Falta una carpeta temporal.",
            UPLOAD_ERR_CANT_WRITE => "No se pudo escribir el archivo en el disco.",
            UPLOAD_ERR_EXTENSION  => "Una extensión de PHP detuvo la subida del archivo."
        ];

        $error = isset($error_messages[$_FILES['company-deck']['error']]) ? $error_messages[$_FILES['company-deck']['error']] : "Error desconocido al subir el archivo.";

        echo json_encode([
            "status"  => "error",
            "message" => $error
        ]);
        exit;
    }
}

// 6. Construir el Asunto del Email
$subject = "Nuevo mensaje de netwrkventures.com - $formName";

// 7. Construir el Cuerpo del Mensaje
if ($attachment) {
    // Construir un email con adjunto (MIME multipart)
    $boundary = md5(time());

    // Cabeceras para email multipart
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "From: no-reply@netwrkventures.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n\r\n";

    // Cuerpo del email
    $message = "--{$boundary}\r\n";
    $message .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
    $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $message .= "Has recibido un nuevo mensaje desde netwrkventures.com.\n\n";
    $message .= "=== {$formName} ===\n\n";
    $message .= "Información de Contacto:\n";
    $message .= "------------------------\n";
    $message .= "Nombre Completo: {$fullname}\n";
    $message .= "Correo Electrónico: {$email}\n";

    if (
        !empty($companyName) ||
        !empty($currentRound) ||
        !empty($raiseAmount) ||
        !empty($valuation)   ||
        !empty($revenue)     ||
        !empty($elevatorPitch)
    ) {
        $message .= "\nInformación de la Empresa:\n";
        $message .= "--------------------------\n";
        if ($companyName)   $message .= "Nombre de la Empresa:   {$companyName}\n";
        if ($currentRound)  $message .= "Ronda Actual:          {$currentRound}\n";
        if ($raiseAmount)   $message .= "Monto a Levantar:      {$raiseAmount}\n";
        if ($valuation)     $message .= "Valoración:            {$valuation}\n";
        if ($revenue)       $message .= "Ingresos:              {$revenue}\n";
        if ($elevatorPitch) $message .= "Elevator Pitch:        {$elevatorPitch}\n";
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
    $headers .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n\r\n";

    // Cuerpo del email
    $message  = "Has recibido un nuevo mensaje desde netwrkventures.com.\n\n";
    $message .= "=== {$formName} ===\n\n";
    $message .= "Información de Contacto:\n";
    $message .= "------------------------\n";
    $message .= "Nombre Completo: {$fullname}\n";
    $message .= "Correo Electrónico: {$email}\n";

    if (
        !empty($companyName) ||
        !empty($currentRound) ||
        !empty($raiseAmount) ||
        !empty($valuation)   ||
        !empty($revenue)     ||
        !empty($elevatorPitch)
    ) {
        $message .= "\nInformación de la Empresa:\n";
        $message .= "--------------------------\n";
        if ($companyName)   $message .= "Nombre de la Empresa:   {$companyName}\n";
        if ($currentRound)  $message .= "Ronda Actual:          {$currentRound}\n";
        if ($raiseAmount)   $message .= "Monto a Levantar:      {$raiseAmount}\n";
        if ($valuation)     $message .= "Valoración:            {$valuation}\n";
        if ($revenue)       $message .= "Ingresos:              {$revenue}\n";
        if ($elevatorPitch) $message .= "Elevator Pitch:        {$elevatorPitch}\n";
    }
}

// 8. Enviar el Email
$sent = mail($recipient, $subject, $message, $headers);

// 9. Responder con JSON según el resultado
if ($sent) {
    if ($isForm2) {
        // Mensaje de éxito para "Apply for Funding"
        $successMessage = "Gracias por aplicar para financiamiento. Tu mensaje ha sido enviado.";
    } else {
        // Mensaje de éxito para "Invest With Us"
        $successMessage = "Gracias por tu interés en invertir con nosotros. Tu mensaje ha sido enviado.";
    }

    echo json_encode([
        "status"  => "success",
        "message" => $successMessage
    ]);
} else {
    echo json_encode([
        "status"  => "error",
        "message" => "Ocurrió un error al enviar el mensaje. Por favor, inténtalo de nuevo más tarde."
    ]);
}
?>
