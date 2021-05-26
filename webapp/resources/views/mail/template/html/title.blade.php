<table class="row"
       style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;">
    <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
        <td class="wrapper last"
            style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 10px 0px 0px;"
            align="left" valign="top">

            <table class="twelve columns"
                   style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 580px; margin: 0 auto; padding: 0;">
                <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
                    <td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;"
                        align="left" valign="top">
                        <h1 style="color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 1.3; word-break: normal; font-size: 30px; margin: 0 0 4px 0; padding: 0;"
                            align="left">{{ trim($slot) }}</h1>

                        @if(isset($lead))
                            <p class="lead"
                               style="color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 21px; font-size: 18px; margin: 0 0 10px; padding: 0;"
                               align="left">{{ trim($lead) }}</p>
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
