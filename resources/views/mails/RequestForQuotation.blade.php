<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Solicitud de cotizacion</title>
</head>
<body>
    <p>Muy cordialmente me dirijo a ustedes para realizar la siguiente solicitud de cotización</p>
    <ul>
        <a href="https://drive.google.com/open?id={{$document_reference->drive_id}}">Descargar solicitud de cotizacion</a>
    </ul>
    <p>Favor enviar su cotizacion en el siguente link:</p>
    <ul>
        <a href="http://3.18.104.218:3000/suppliers/{{$quotation->hash_id}}">Cargar Cotizacion, aquí</a>
    </ul>
    <br>
    <p>Gracias,</p>
    
    <p>APP CONTROL INGENIERIA</p>
</body>
</html>
