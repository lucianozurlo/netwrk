<?php
// testMail.php
$to = 'em24.teco@gmail.com'; // Reemplaza con tu correo
$subject = 'Correo de Prueba con Adjuntos';
$message = 'Este es un correo de prueba con un archivo adjunto.';
$from = 'no-reply@netwrkventures.com'; // Asegúrate de que este correo esté configurado correctamente

$file = 'test.pdf'; // Reemplaza con la ruta a un archivo de prueba en tu servidor

// Leer el contenido del archivo
$fileContent = chunk_split(base64_encode(file_get_contents($file)));
$uid = md5(uniqid(time()));
$fileName = basename($file);
$fileType = mime_content_type($file);

// Cabeceras
$headers = "From: ".$from."\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";

// Cuerpo del email
$body = "--".$uid."\r\n";
$body .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
$body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$body .= $message."\r\n\r\n";
$body .= "--".$uid."\r\n";
$body .= "Content-Type: ".$fileType."; name=\"".$fileName."\"\r\n";
$body .= "Content-Transfer-Encoding: base64\r\n";
$body .= "Content-Disposition: attachment; filename=\"".$fileName."\"\r\n\r\n";
$body .= $fileContent."\r\n\r\n";
$body .= "--".$uid."--";

// Enviar el correo
if (mail($to, $subject, $body, $headers)) {
    echo "Correo enviado exitosamente.";
} else {
    echo "Fallo al enviar el correo.";
}
?>
