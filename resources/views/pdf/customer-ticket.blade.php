<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <style>

        body{
            font-family: Arial, sans-serif;
            font-size:14px;
            color:#333;
        }

        .container{
            border:1px solid #ddd;
            padding:25px;
            border-radius:10px;
        }

        .header{
            text-align:center;
            margin-bottom:25px;
        }

        .title{
            font-size:22px;
            font-weight:bold;
        }

        .code{
            color: #000000;
            font-size:20px;
            font-weight:bold;
        }

        table{
            width:100%;
            border-collapse: collapse;
        }

        td{
            padding:10px;
            border-bottom:1px solid #eee;
        }

        .footer{
            margin-top:30px;
            text-align:center;
            font-size:12px;
            color:#777;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="header">

        <div class="title">
            Martina’s Closets
        </div>

        <br>

        <div>
            COMPROBANTE DE REGISTRO
        </div>

        <br>

        <div class="code">
            {{ $customer->CodigoCustomer }}
        </div>

    </div>

    <table>

        <tr>
            <td><strong>Nombre</strong></td>
            <td>{{ $customer->Nombre }}</td>
        </tr>

        <tr>
            <td><strong>Teléfono</strong></td>
            <td>{{ $customer->Telefono }}</td>
        </tr>

        <tr>
            <td><strong>Sexo</strong></td>
            <td>{{ $customer->Sexo }}</td>
        </tr>

        <tr>
            <td><strong>Tipo</strong></td>
            <td>{{ $customer->Tipo }}</td>
        </tr>

        <tr>
            <td><strong>Estado</strong></td>
            <td>{{ $customer->EstadoCustomer }}</td>
        </tr>

        <tr>
            <td><strong>Fecha Registro</strong></td>
            <td>{{ $customer->created_at->format('d/m/Y H:i') }}</td>
        </tr>

    </table>

    <div style="text-align:center">
        <p>Escanea el código QR para realizar tu agendamiento</p>

        <img src="data:image/svg+xml;base64, {!! $qrCode !!}" alt="QR Code" width="200" height="200">
    </div>
    
    <div class="footer">
        Documento generado automáticamente por Martina’s Closets
    </div>

</div>

</body>
</html>