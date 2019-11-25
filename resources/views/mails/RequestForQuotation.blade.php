<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Solicitud de cotizacion</title>
</head>
<body>
    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
    <ul>
        <li>Nombre: {{ $contact->name }}</li>
        <li>Teléfono: {{ $contact->phones }}</li>
    </ul>
    <p>Adjunto el link de la solicitud de cotizacion:</p>
    <ul>
        <li>Link: https://drive.google.com/open?id=1hUQnrzqm0R9iaOvcAhzdhP7HTaXPP0MK</li>
    </ul>
</body>
</html>
