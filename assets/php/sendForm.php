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

// 1. Identify which form is being submitted
$formId = isset($_POST['form_id']) ? $_POST['form_id'] : '';

// 2. Configure recipients and form names
if ($formId === 'form1') {
    // "Invest With Us"
    $recipient = "repoarchivos@gmail.com";
    $formName  = "Invest With Us";
} elseif ($formId === 'form2') {
    // "Apply for Funding"
    $recipient = "em24.teco@gmail.com";
    $formName  = "Apply for Funding";
} else {
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

// 4. Field-by-field validation in sequence
//    (first Full Name, then Email, etc.)
//    Adjust if you need different required fields for each form.

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

// If it's form2, we might also require a Company Name:
if ($formId === 'form2') {
    if (empty($companyName)) {
        echo json_encode([
            "status"  => "error",
            "message" => "Company Name is required."
        ]);
        exit;
    }
}

// 5. Build the Subject
$subject = "New message from netwrkventures.com - $formName";

// 6. Construct the Email Body
// (removed "Hello," as requested)
$message  = "You have received a new message from netwrkventures.com.\n\n";
$message .= "=== $formName ===\n\n";  

// Contact Information
$message .= "Contact Information:\n";
$message .= "--------------------\n";
$message .= "Full Name: $fullname\n";
$message .= "Email:     $email\n";

// Company Information (only if data is present)
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

// 7. Headers
$headers  = "From: no-reply@netwrkventures.com\r\n";
$headers .= "Reply-To: $email\r\n";

// 8. Send the email
$sent = mail($recipient, $subject, $message, $headers);

// 9. Custom success messages for each form
if ($sent) {
    if ($formId === 'form1') {
        // Custom success for "Invest With Us"
        $successMessage = "Thank you for your interest in investing with us. Your message has been sent.";
    } else {
        // Custom success for "Apply for Funding"
        $successMessage = "Thank you for applying for funding. Your message has been sent.";
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
