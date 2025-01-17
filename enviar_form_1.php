<?php
// Verificamos que el formulario haya sido enviado por método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recogemos los datos con sus nombres
    $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
    $email    = isset($_POST['email'])    ? trim($_POST['email'])    : '';

    // Dirección de destino
    $destinatario = "formtesting117@gmail.com";

    // Asunto del correo
    $asunto = "Nuevo contacto desde Formulario 1";

    // Cuerpo del mensaje
    $mensaje = "Se ha recibido un nuevo contacto.\n\n";
    $mensaje .= "Nombre: $fullname\n";
    $mensaje .= "Email: $email\n";

    // Cabecera (quien envía, puede ser un no-reply)
    $headers = "From: no-reply@tusitio.com\r\n";

    // Enviamos el mail
    $enviado = mail($destinatario, $asunto, $mensaje, $headers);

    // Mostramos el resultado directamente en pantalla
    if ($enviado) {
        echo "<h3>¡El correo se envió correctamente!</h3>";
        echo "<p>Gracias por contactarte, $fullname. Te responderemos pronto.</p>";
        // Si quieres volver al inicio o a otra página:
        // echo "<p><a href='index.html'>Volver</a></p>";
    } else {
        echo "<h3>Hubo un problema al enviar el correo.</h3>";
        echo "<p>Por favor intenta de nuevo o contáctanos directamente.</p>";
        // echo "<p><a href='index.html'>Volver</a></p>";
    }

} else {
    // Si alguien entra a este script sin usar POST (por ejemplo, directo en la barra de direcciones)
    echo "<h3>Acceso no válido.</h3>";
}
?>
