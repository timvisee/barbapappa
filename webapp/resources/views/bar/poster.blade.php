<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style>
        @page {
            margin: 15px;
            margin-bottom: 0 !important;
        }

        body {
            font-family: Ubuntu, DejaVu Sans, Helvetica, sans-serif;
            font-size: 20px;
            text-align: center;
        }

        h1 {
            font-size: 30px;
        }

        .description {
            line-height: 25px;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .logo {
            width: 300px;
        }

        .footer {
            font-size: 20px;
        }

        table {
            margin-left: auto;
            margin-right: auto;
        }

        table .left {
            text-align: right;
            padding-right: 15px;
        }

        table .right {
            text-align: left;
            font-family: Courier, Helvetica, sans-serif;
        }
    </style>
</head>
<body>
    <h1>This bar uses</h1>

    <img class="logo" src="{{ asset('img/logo/logo_nowrap_600dpi.png') }}" />

    <p class="description">
        to digitally manage payments and inventory for consumptions
        <br>
        scan the QR code below to join and make a purcahse
    </p>

    <img src="data:image/png;base64,{{ base64_encode(
        QrCode::format('png')
            ->size(700)
            ->margin(1)
            ->errorCorrection('Q')
            ->generate($qr_url)
        ) }}">

    <div class="footer">
        <table>
            <tr>
                <td class="left">Or visit:</td>
                <td class="right">{{ $bar_url }}</td>
            </tr>
            @if(!empty($code))
                <tr>
                    <td class="left">Code:</td>
                    <td class="right">{{ $code }}</td>
                </tr>
            @endif
        </table>
    </div>
</body>
</html>
