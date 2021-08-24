{{-- View for a community/bar poster to render as PDF --}}

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

            /* hack to align footer text, we should find a better method */
            padding-top: 7px;
        }
    </style>
</head>
<body>
    <h1>@lang('pages.' . $type . '.poster.this' . ucfirst($type) . 'Uses')</h1>

    <p>
        <img class="logo" src="data:image/png;base64,{{
            base64_encode(file_get_contents(base_path() . '/public/img/logo/logo_nowrap_600dpi.png'))
        }}" />
    </p>

    <p class="description">
        @lang('pages.' . $type . '.poster.toDigitallyManage')<br>
        @lang('pages.' . $type . '.poster.scanQr')
    </p>

    <p>
        <img src="data:image/png;base64,{{ base64_encode(
            QrCode::format('png')
                ->size(700)
                ->margin(1)
                ->errorCorrection('Q')
                ->generate($qr_url)
            ) }}">
    </p>

    <div class="footer">
        <table>
            <tr>
                <td class="left">@lang('pages.' . $type . '.poster.orVisit'):</td>
                <td class="right">{{ $plain_url }}</td>
            </tr>
            @if(!empty($code))
                <tr>
                    <td class="left">@lang('misc.code'):</td>
                    <td class="right">{{ $code }}</td>
                </tr>
            @endif
        </table>
    </div>
</body>
</html>
