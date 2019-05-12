<table class="row"
       style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;">
    <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
        <td class="wrapper last"
            style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 10px 0px 0px;"
            align="left" valign="top">
            <table class="twelve columns"
                   style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 580px; margin: 0 auto; padding: 0;">
                <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
                    <td class="container-text" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0px 0px 10px;"
                        align="left" valign="top">
                            {!! Illuminate\Mail\Markdown::parse(trim($slot)) !!}
                    </td>
                    <td class="expander"
                        style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;"
                        align="left" valign="top"></td>
                </tr>
            </table>
        </td>
    </tr>
</table>