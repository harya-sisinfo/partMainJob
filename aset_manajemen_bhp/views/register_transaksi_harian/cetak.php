<html></html>

<head>
    <style>
        .page {
            size: A4;
            margin: 0.5cm;
            margin-left: 1cm;
            margin-right: 1cm;
            font-family: verdana, sans-serif;
            font-size: small;

        }

        .font-small {
            font-size: x-small;
        }


        table {
            border-collapse: collapse;
            font-size: 14px;
            border-spacing: 2px;
        }

        th {
            text-align: center;
            font-weight: bold;
            padding: 3px;
        }

        td {
            padding: 3px;
        }
    </style>
</head>

<body onload="window.print()">
    <div class="page">
        <table class="font-small" width="100%" border="0" cellspacing="0" cellpadding="1px">
            <tbody>
                <tr>
                    <td width="60px" valign="top">
                        <img src="<?php echo $logo ?>" height="50" width="50" />
                    </td>
                    <td width="60%" valign="top">
                        Universitas Gadjah Mada
                        <br />Bulaksumur Caturtunggal,Depok Kab.Sleman
                        <br />Daerah Istimewa Yogyakarta

                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <hr>
                    </td>
                </tr>
            </tbody>
        </table>
        <h2 style="text-align: center;"><strong><?php echo $header ?></strong></h2>
        <table class="font-small" border="0" cellspacing="0" cellpadding="1px">
            <tr>
                <td colspan="2" valign="top">
                    <b>Register Transaksi Harian</b>
                </td>
            </tr>
        </table>

    </div>
</body>

</html>