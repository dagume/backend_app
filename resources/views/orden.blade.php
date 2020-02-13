<!DOCTYPE html>
<html lang="es_CO">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Document</title>
    <style>
        html {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'PingFang SC', 'Hiragino Sans GB',
            'Microsoft YaHei', 'Helvetica Neue', Helvetica, Arial, sans-serif, 'Apple Color Emoji',
            'Segoe UI Emoji', 'Segoe UI Symbol';
        }
        body{
            margin-top: 0.5 cm;
            margin-bottom: 1 cm;
            margin-left: 1.5 cm;
            margin-right: 1.5 cm;
            padding: 0;
        }
        #watermark {
            position: fixed;
            bottom: 0;
            left:0;
            width: 21 cm;
            height: 29.7 cm;
            z-index: -1000;
        }
        .page-header{
            height: 90px;
        }
        .page-header > h4 {
            float: left;
        }
        .page-header > table {
            float: right;
        }

        #page-content {
            padding-top: 40px;
            padding-left: 10px;
            padding-right: 10px;
        }
        #receiver > p,b {
            margin-bottom: 0;
        }
        #order {
            padding: 20px 0;
        }
        #order > table {
            width: 100%;
            text-align: center;
        }
        #order-table, #body-order-table >td {
            border: gray 1px solid;
            border-collapse: collapse;
            padding: 8px;
        }
        th {
            border: gray 1px solid;
            padding: 8px;
        }
        #page-footer {
            padding-top: 80px;
            padding-left: 10px;
            padding-right: 10px;
        }
        #page-footer > p {
            margin-bottom: 0;
            margin-top: 0;
        }
    </style>

</head>
<body>
    <div id="watermark">
        <img src="https://res.cloudinary.com/dqcyu2ism/image/upload/v1568132792/DOCUMENTOS_zlqusj.jpg" height="100%" widht="100%"/>
    </div>
    <main>
        <div class='page-header'>
            <h4>{{$title}}</h4>
            <table>
                <tbody>
                    <tr>
                    <td>Código:  {{$code}}</td>
                    </tr>
                    <tr>
                        <td>Página 1 de 1</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="page-content">
            <p>Tunja, {{date("d")}} de {{strftime("%B")}} de {{date("Y")}}</p>
            <br/>
            <div id="receiver">

                @if($provider->type === 1)
                    <p>SEÑOR(A)</p>
                @else
                    <p>SEÑORES</p>
                @endif
                
                <b>{{$provider->name}} {{$provider->lastname}}</b><br/>
                
                @if ($provider->identification_type == 1)
                    <b>CC. {{$provider->identification_number}}</b>
                @elseif ($provider->identification_type == 2)
                    <b>NIT. {{$provider->identification_number}}</b>
                @elseif ($provider->identification_type == 3)
                    <b>PAS. {{$provider->identification_number}}</b>
                @else
                    <b>CE. {{$provider->identification_number}}</b>
                @endif



            </div>
            <div id="order">
                <br/>
                <p>Referencia: {{$title}}</p>
                <br/>
                <p>Muy cordialmente me dirijo a ustedes para realizar la siguiente Orden de compra</p>
                <table id="order-table">
                    <tbody id="body-order-table">
                        <tr>
                            <th>ITEM</th>
                            <th>PRODUCTO</th>
                            <th>UNIDAD</th>
                            <th>CANTIDAD</th>
                            <th>PRECIO UNITARIO</th>
                            <th>COSTO TOTAL</th>
                        </tr>
                        @foreach ($details as  $key => $det)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $det->product_name }}</td>
                                <td>{{ $det->measure_name }}</td>
                                <td>{{ $det->quantity }}</td>
                                <td>{{ $det->value_product }}</td>
                                <td>{{ $det->subtotal_product }}</td>
                            </tr>
                        @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>SUBTOTAL</td>
                                <td>{{ $details[0]->subtotal_order }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>IVA **%</td>
                                <td>******</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>TOTAL</td>
                                <td>{{ $details[0]->total_order }}</td>
                            </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="page-footer">
                <p>Quedamos atentos a cualquier inquietud</p>
                <br/><br/>
                <p>Cordialmente,</p>
                <br/><br/>
                <p>{{$sender->name}} {{$sender->lastname}}</p>
                <p>{{$sender->email}}</p>
        </div>
    </main>
</body>
</html>
