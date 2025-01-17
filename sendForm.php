<?php
// sendForm.php

// Permitir CORS / peticiones fetch desde el mismo dominio u otro (opcional, si trabajas local o en otras URLs)
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

// Verificamos que venga por método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Obtenemos los datos del formulario
    $fullname    = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
    $email       = isset($_POST['email']) ? trim($_POST['email']) : '';
    $companyName = isset($_POST['company-name']) ? trim($_POST['company-name']) : '';
    $currentRound= isset($_POST['current-round']) ? trim($_POST['current-round']) : '';
    $raiseAmount = isset($_POST['raise-amount']) ? trim($_POST['raise-amount']) : '';
    $valuation   = isset($_POST['valuation']) ? trim($_POST['valuation']) : '';
    $revenue     = isset($_POST['revenue']) ? trim($_POST['revenue']) : '';
    $elevator    = isset($_POST['elevator-pitch']) ? trim($_POST['elevator-pitch']) : '';
    // Si subieras archivos, tendrías que manejarlos con $_FILES['name'] y moverlos de carpeta.

    // Aquí armas el cuerpo del mensaje
    $mensaje = "Se ha recibido un nuevo formulario.\n\n";
    $mensaje .= "Full Name: $fullname\n";
    $mensaje .= "Email: $email\n";
    $mensaje .= "Company Name: $companyName\n";
    $mensaje .= "Current Round: $currentRound\n";
    $mensaje .= "Raise Amount: $raiseAmount\n";
    $mensaje .= "Valuation: $valuation\n";
    $mensaje .= "Revenue: $revenue\n";
    $mensaje .= "Elevator Pitch: $elevator\n\n";

    // Datos del correo
    $destinatario = "formtesting117@gmail.com";  // A dónde se enviará
    $asunto       = "Nuevo formulario de contacto - $fullname";

    // Cabeceras (puedes ajustar 'From', 'Reply-To', etc.)
    $headers = "From: no-reply@tudominio.com\r\n";
    $headers .= "Reply-To: $email\r\n";

    // Mandamos el correo
    $enviado = mail($destinatario, $asunto, $mensaje, $headers);

    // Preparamos la respuesta en JSON para el fetch (o lo que uses)
    if ($enviado) {
        echo json_encode([
            "status"  => "success",
            "message" => "Tu mensaje fue enviado correctamente."
        ]);
    } else {
        echo json_encode([
            "status"  => "error",
            "message" => "Hubo un error al enviar el mensaje. Intenta más tarde."
        ]);
    }

} else {
    // Si no viene por POST, devolvemos un error
    echo json_encode([
        "status"  => "error",
        "message" => "Método no permitido."
    ]);
}
