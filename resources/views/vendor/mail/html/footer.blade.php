<?php
// TODO: Define this
$message = Array(
    'bottom' => Array(
        'linkDashboard' => 'http://localhost/',
        'textDashboard' => 'Dashboard',
        'linkMyAccount' => 'http://localhost/',
        'textMyAccount' => 'My account',
        'balance' => 'Balance',
        'myBalance' => 'My balance',
        'balanceValue' => '&euro;1.23',
    ),
    'footer' => Array(
        'linkTerms' => 'http://localhost/',
        'textTerms' => 'Terms',
        'linkPrivacy' => 'http://localhost/',
        'textPrivacy' => 'Privacy',
        'linkEmailPreferences' => 'http://localhost/',
        'textEmailPreferences' => 'Email preferences'
    )
);
?>

<table class="row footer"
       style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;">
    <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
        <td class="wrapper"
            style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; background: #EBEBEB; margin: 0; padding: 10px 20px 0px 0px;"
            align="left" bgcolor="#ebebeb" valign="top">

            <table class="six columns"
                   style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 280px; margin: 0 auto; padding: 0;">
                <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
                    <td class="left-text-pad"
                        style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0px 0px 10px 10px;"
                        align="left" valign="top">

                        <h5 style="color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 1.3; word-break: normal; font-size: 20px; margin: 0; padding: 0 0 10px;"
                            align="left">{{ $message['bottom']['balance'] }}</h5>

                        <p style="color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;"
                           align="left">
                            {{ $message['bottom']['myBalance'] }}:
                            <span style="font-size: 125%;">
                                                                {{ $message['bottom']['balanceValue'] }}
                                                            </span>
                        </p>
                    </td>
                    <td class="expander"
                        style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;"
                        align="left" valign="top"></td>
                </tr>
            </table>
        </td>
        <td class="wrapper last"
            style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; background: #EBEBEB; margin: 0; padding: 10px 0px 0px;"
            align="left" bgcolor="#ebebeb" valign="top">

            <table class="six columns"
                   style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 280px; margin: 0 auto; padding: 0;">
                <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
                    <td class="last right-text-pad"
                        style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0 8px 10px 0;"
                        align="left" valign="top">
                        <h5 style="color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 1.3; word-break: normal; font-size: 20px; margin: 0; padding: 0 0 10px;"
                            align="left">{{ config('app.name') }}</h5>

                        <table class="tiny-button"
                               style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; overflow: hidden; padding: 0; margin-bottom: 6px;">
                            <tr style="vertical-align: top; text-align: left; padding: 0;"
                                align="left">
                                <td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: center; color: #FFF; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; display: block; width: auto !important; background: #3B5998; margin: 0; padding: 5px 0 4px; border: 1px solid #2D4473;"
                                    align="center" bgcolor="#3b5998" valign="top">
                                    <a href="{{ $message['bottom']['linkDashboard'] }}"
                                       style="color: #FFF; text-decoration: none; font-weight: normal; font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                        {{ $message['bottom']['textDashboard'] }}
                                    </a>
                                </td>
                            </tr>
                        </table>
                        <table class="tiny-button"
                               style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; overflow: hidden; padding: 0;">
                            <tr style="vertical-align: top; text-align: left; padding: 0;"
                                align="left">
                                <td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: center; color: #FFF; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; display: block; width: auto !important; background: #3B5998; margin: 0; padding: 5px 0 4px; border: 1px solid #2D4473;"
                                    align="center" bgcolor="#3b5998" valign="top">
                                    <a href="{{ $message['bottom']['linkMyAccount'] }}"
                                       style="color: #FFF; text-decoration: none; font-weight: normal; font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                        {{ $message['bottom']['textMyAccount'] }}
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="expander"
                        style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;"
                        align="left" valign="top"></td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table class="row"
       style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;">
    <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
        <td class="wrapper last"
            style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 10px 0px 0px;"
            align="left" valign="top">

            <table class="twelve columns"
                   style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 580px; margin: 0 auto; padding: 0;">
                <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
                    <td align="center"
                        style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0px 0px 10px;"
                        valign="top">
                        <center style="width: 100%; min-width: 580px;">
                            <p style="text-align: center; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;"
                               align="center">
                                <a href="{{ $message['footer']['linkTerms'] }}" style="color: #2BA6CB; text-decoration: none;">
                                    {{ $message['footer']['textTerms'] }}
                                </a>
                                &nbsp;&nbsp;&middot;&nbsp;&nbsp;
                                <a href="{{ $message['footer']['linkPrivacy'] }}" style="color: #2BA6CB; text-decoration: none;">
                                    {{ $message['footer']['textPrivacy'] }}
                                </a>
                                &nbsp;&nbsp;&middot;&nbsp;&nbsp;
                                <a href="{{ $message['footer']['linkEmailPreferences'] }}" style="color: #2BA6CB; text-decoration: none;">
                                    {{ $message['footer']['textEmailPreferences'] }}
                                </a>
                            </p>
                        </center>
                    </td>
                    <td class="expander"
                        style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;"
                        align="left" valign="top"></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
