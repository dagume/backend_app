
<!DOCTYPE html>
<html>


<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        html, body {
            font-family: 'Lato', sans-serif;
            font-size: 12px;
            height: 100vh;
            margin: 0;
        }

        .PDF {
            background-color: white;
            height: 100vh;
        }

        .ProductsTable, .ProductsTable > th, .ProductsTable > td, .ProductsTable > tr {
            border: 1px solid gray;
        }

        table {
            border-collapse: collapse;
            background-color: aqua;
        }

        .PDF table {
            min-width: 150px;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.5);
        }

        .PDFHeader {
            background-color: rgba(50, 168, 145, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 50px 0 50px;
            height: 12%;
        }

        .PDFBody {
            background-color: rgba(152, 50, 168, 0.2);
            height: 60%;
            display: flex;
            flex-direction: column;
            padding: 0 50px 0 50px;
        }

        .PDFFooter {
           background-color: rgba(149, 201, 34, 0.3); 
            height: 28%;
            padding: 0 50px;
        }

        .PDFFooter>p {
            margin: 0;
        }
    </style>
</head>

<body>
    <div class='PDF'>
        <div class='PDFHeader'>
            <h5>Solicitud de cotización</h5>
            <table>
                <tr>
                    <td>Código: RG1</td>
                </tr>
                <tr>
                    <td>Página 1 de 1</td>
                </tr>
            </table>
        </div>
        <div class='PDFBody'>
            <p>Tunja, 06 de septiembre</p>
            <br />
            <p>SEÑORES:</p> <!-- Si el proveedor es una persona debe poner: SEÑOR(A); si es empresa, dejar SEÑORES -->
            <b>MEXICHEM COLOMBIA S.A.S</b> <!-- Cambia según el destinatario  -->
            <b>NIT: 860.005.050-1</b>
            <br /><br />
            <p>Referencia: Solicitud de cotización</p>
            <p>Muy cordialmente me dirijo a ustedes para realizar la siguiente solicitud de cotización</p>
            <table class='ProductsTable'>
                <tr>
                    <th>ITEM</th>
                    <th>PRODUCTO</th>
                    <th>UNIDAD</th>
                    <th>CANTIDAD</th>
                </tr>
               @foreach ($user as $use)
              <tr>
                <td>{{$title}}</td>
                <td>{{$title}}</td>
                <td>{{$title}}</td>
                <td>{{$title}}</td>
              </tr>
               @endforeach
                <!--
                        Aquí van los datos de la tabla
                        {
                        products.map(p =>
                            <tr>
                                <td>{p.id}</td>
                                <td>{p.name}</td>
                                <td>{p.measure}</td>
                                <td>{p.amount}</td>
                            </tr>
                            )
                        }
                    -->
            </table>
        </div>
        <div class='PDFFooter'>
            <p>Quedamos atentos a cualquier inquietud</p>
            <br />
            <p>cordialmente,</p>
            <br />
            <p>Juan Carlos Molina</p> <!-- Cambian los datos del remitente (usuario logeado) -->
            <p>Cel: 314-278-9672</p>
            <p>juancamolina@gmail.com</p>
        </div>
    </div>
</body>

</html>

