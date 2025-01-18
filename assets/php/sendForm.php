<?php
// sendForm.php
header('Content-Type: application/json; charset=utf-8');

// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status"  => "error",
        "message" => "Method not allowed."
    ]);
    exit;
}

// 1. Identify which form is being submitted
$formId = isset($_POST['form_id']) ? $_POST['form_id'] : '';

// 2. Configure recipients and form names based on form ID
if ($formId === 'form1') {
    // "Invest With Us"
    $recipient = "repoarchivos@gmail.com";
    $formName  = "Invest With Us";
    $isForm2   = false; // Indicates whether additional fields are required
} elseif ($formId === 'form2') {
    // "Apply for Funding"
    $recipient = "em24.teco@gmail.com";
    $formName  = "Apply for Funding";
    $isForm2   = true;
} else {
    // Unknown form identifier
    echo json_encode([
        "status"  => "error",
        "message" => "Unknown form identifier."
    ]);
    exit;
}

// 3. Collect form data
$fullname      = isset($_POST['fullname'])        ? trim($_POST['fullname'])        : '';
$email         = isset($_POST['email'])           ? trim($_POST['email'])           : '';
$companyName   = isset($_POST['company-name'])    ? trim($_POST['company-name'])    : '';
$currentRound  = isset($_POST['current-round'])   ? trim($_POST['current-round'])   : '';
$raiseAmount   = isset($_POST['raise-amount'])    ? trim($_POST['raise-amount'])    : '';
$valuation     = isset($_POST['valuation'])       ? trim($_POST['valuation'])       : '';
$revenue       = isset($_POST['revenue'])         ? trim($_POST['revenue'])         : '';
$elevatorPitch = isset($_POST['elevator-pitch'])  ? trim($_POST['elevator-pitch'])  : '';

// 4. Sequential Field Validation

// 4.1 Validate Full Name
if (empty($fullname)) {
    echo json_encode([
        "status"  => "error",
        "message" => "Full Name is required."
    ]);
    exit;
}

// 4.2 Validate Email (not empty)
if (empty($email)) {
    echo json_encode([
        "status"  => "error",
        "message" => "Email is required."
    ]);
    exit;
}

// 4.3 Validate Email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "status"  => "error",
        "message" => "Invalid email format."
    ]);
    exit;
}

// 4.4 If Form 2, validate Company Name
if ($isForm2) {
    if (empty($companyName)) {
        echo json_encode([
            "status"  => "error",
            "message" => "Company Name is required."
        ]);
        exit;
    }
}

// 5. Handle File Upload (Only for Form 2)
if ($isForm2) {
    if (isset($_FILES['company-deck']) && $_FILES['company-deck']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['company-deck']['tmp_name'];
        $fileName    = $_FILES['company-deck']['name'];
        $fileSize    = $_FILES['company-deck']['size'];
        $fileType    = $_FILES['company-deck']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Sanitize file name
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

        // Check file size (e.g., max 5MB)
        if ($fileSize > 5 * 1024 * 1024) { // 5MB
            echo json_encode([
                "status"  => "error",
                "message" => "Uploaded file is too large. Maximum size is 5MB."
            ]);
            exit;
        }

        // Check allowed file extensions
        $allowedfileExtensions = ['pdf'];
        if (!in_array($fileExtension, $allowedfileExtensions)) {
            echo json_encode([
                "status"  => "error",
                "message" => "Invalid file type. Only PDF files are allowed."
            ]);
            exit;
        }

        // Read the file content
        $fileContent = file_get_contents($fileTmpPath);
        $encodedContent = chunk_split(base64_encode($fileContent));

        // Generate a boundary string
        $boundary = md5(time());

        // Headers for multipart email
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "From: no-reply@netwrkventures.com\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

        // Email subject
        $subject = "New message from netwrkventures.com - $formName";

        // Email body
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

        // Add attachment
        $message .= "\r\n--{$boundary}\r\n";
        $message .= "Content-Type: {$fileType}; name=\"{$fileName}\"\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n";
        $message .= "Content-Disposition: attachment; filename=\"{$fileName}\"\r\n\r\n";
        $message .= "{$encodedContent}\r\n";
        $message .= "--{$boundary}--";

    } else {
        // File upload error
        echo json_encode([
            "status"  => "error",
            "message" => "There was an error uploading the file."
        ]);
        exit;
    }
} else {
    // For Form 1, construct a simple email
    $headers  = "From: no-reply@netwrkventures.com\r\n";
    $headers .= "Reply-To: $email\r\n";

    // Subject
    $subject = "New message from netwrkventures.com - $formName";

    // Message
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

// 6. Send the email
if ($isForm2) {
    // Send multipart email with attachment
    $sent = mail($recipient, $subject, $message, $headers);
} else {
    // Send simple email
    $sent = mail($recipient, $subject, $message, $headers);
}

// 7. Respond with JSON based on Email Sending Result
if ($sent) {
    if ($isForm2) {
        // Success message for "Apply for Funding"
        $successMessage = "Thank you for applying for funding. Your message has been sent.";
    } else {
        // Success message for "Invest With Us"
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
