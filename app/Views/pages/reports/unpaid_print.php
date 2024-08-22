<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry Shop</title>
    <link rel="shortcut icon" href="<?php echo base_url() ?>images/LOGO.jpg" />
    <link rel="stylesheet" href="<?php echo base_url() ?>vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="<?php echo base_url() ?>vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="<?php echo base_url() ?>css/style.css">
    <style>
        html,
        body {
            margin: 0;
            height: 100%;
            overflow: hidden
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
        }

        h5,
        td {
            font-size: 14px;
        }

        img {
            margin: auto;
        }

        .heading,
        .heading2 {
            margin-bottom: 0px !important;
        }

        td {
            padding: 5px 2px 0px !important;
        }

        hr {
            margin: 0px !important;
        }

        img {
            width: 50%;
        }

        .hr {
            border-top: dashed 1px;
        }
    </style>
</head>

<body>
    <div class="row justify-content-center px-3">
        <div class="col-md-12 text-center mt-4">
            <h5 class="mb-4">LABACHINE LAUNDRY LOUNGE </h5>
            <h5>UNPAID LIST</h5>
        </div>
        <div class="col-md-12">
            <?php if (!empty($records)) { ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <table class="table table-borderless mb-2">
                                <thead>
                                    <tr>
                                        <td style="font-size: 10px; font-weight:bold">DATE</td>
                                        <td style="font-size: 10px; font-weight:bold">CUSTOMER</td>
                                        <td style="font-size: 10px; font-weight:bold">BAL</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($records)) { ?>
                                        <?php foreach ($records as $rec) { ?>
                                            <tr>
                                                <td style="font-size: 7px;"><?php echo date('d/m/Y', strtotime($rec->dateCreated)) ?></td>
                                                <td style="font-size: 7px;"><?php echo $rec->customer ?></td>
                                                <td style="font-size: 7px;"><?php echo number_format($rec->balance, 2)  ?></td>
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <div class="hr my-0"></div>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr style="height: 20px;">
                                <td style="font-size: 10px;">Date Printed</td>
                                <td colspan="2" style="font-size: 10px;">: <?php echo date('m/d/Y h:i A') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.print()
        var beforePrint = function() {
            console.log("before print");
        };
        var afterPrint = function() {
            window.close();
        };

        if (window.matchMedia) {
            var mediaQueryList = window.matchMedia("print");
            mediaQueryList.addListener(function(mql) {
                if (mql.matches) {
                    beforePrint();
                } else {
                    afterPrint();
                }
            });
        }

        window.onbeforeprint = beforePrint;
        window.onafterprint = afterPrint;
    </script>

</body>

</html>