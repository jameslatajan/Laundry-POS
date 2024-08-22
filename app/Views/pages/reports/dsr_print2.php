<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry Shop</title>
    <link rel="shortcut icon" href="<?php echo base_url() ?>assets/images/LOGO.jpg" />
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/libs/bootstrap4/bootstrap.min.css">

    <script src="<?php echo base_url() ?>assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="<?php echo base_url() ?>assets/js/jquery.cookie.js"></script>
    <script src="<?php echo base_url() ?>assets/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/popper.min.js"></script>
    <style>
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
                "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif,
                "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol",
                "Noto Color Emoji";
            font-size: 11px;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            text-align: left;
            background-color: #fff;
            padding-right: 10px !important;
            padding-left: 10px;
        }

        .container {
            min-width: 992px !important;
        }

        .container,
        .container-fluid,
        .container-lg,
        .container-md,
        .container-sm,
        .container-xl {
            width: 100%;
            padding-right: 10px;
            padding-left: 10px;
            margin-right: auto;
            margin-left: auto;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }

        .table td,
        .table th {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody+tbody {
            border-top: 2px solid #dee2e6;
        }

        .table-sm td,
        .table-sm th {
            padding: 1px;
        }

        .table-borderless {
            border: none;
        }

        .table-borderless td,
        .table-borderless th {
            border: none;
        }

        .table-borderless thead td,
        .table-borderless thead th {
            border-bottom-width: 2px;
        }

        .table-borderless tbody+tbody,
        .table-borderless td,
        .table-borderless th,
        .table-borderless thead th {
            border: 0;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .table-hover tbody tr:hover {
            color: #212529;
            background-color: rgba(0, 0, 0, 0.075);
        }

        .table-primary,
        .table-primary>td,
        .table-primary>th {
            background-color: #b8daff;
        }

        .table-primary tbody+tbody,
        .table-primary td,
        .table-primary th,
        .table-primary thead th {
            border-color: #7abaff;
        }

        .table-hover .table-primary:hover {
            background-color: #9fcdff;
        }

        .table-hover .table-primary:hover>td,
        .table-hover .table-primary:hover>th {
            background-color: #9fcdff;
        }

        .table-secondary,
        .table-secondary>td,
        .table-secondary>th {
            background-color: #d6d8db;
        }

        .table-secondary tbody+tbody,
        .table-secondary td,
        .table-secondary th,
        .table-secondary thead th {
            border-color: #b3b7bb;
        }

        .table-hover .table-secondary:hover {
            background-color: #c8cbcf;
        }

        .table-hover .table-secondary:hover>td,
        .table-hover .table-secondary:hover>th {
            background-color: #c8cbcf;
        }

        .table-success,
        .table-success>td,
        .table-success>th {
            background-color: #c3e6cb;
        }

        .table-success tbody+tbody,
        .table-success td,
        .table-success th,
        .table-success thead th {
            border-color: #8fd19e;
        }

        .table-hover .table-success:hover {
            background-color: #b1dfbb;
        }

        .table-hover .table-success:hover>td,
        .table-hover .table-success:hover>th {
            background-color: #b1dfbb;
        }

        .table-info,
        .table-info>td,
        .table-info>th {
            background-color: #bee5eb;
        }

        .table-info tbody+tbody,
        .table-info td,
        .table-info th,
        .table-info thead th {
            border-color: #86cfda;
        }

        .table-hover .table-info:hover {
            background-color: #abdde5;
        }

        .table-hover .table-info:hover>td,
        .table-hover .table-info:hover>th {
            background-color: #abdde5;
        }

        .table-warning,
        .table-warning>td,
        .table-warning>th {
            background-color: #ffeeba;
        }

        .table-warning tbody+tbody,
        .table-warning td,
        .table-warning th,
        .table-warning thead th {
            border-color: #ffdf7e;
        }

        .table-hover .table-warning:hover {
            background-color: #ffe8a1;
        }

        .table-hover .table-warning:hover>td,
        .table-hover .table-warning:hover>th {
            background-color: #ffe8a1;
        }

        .table-danger,
        .table-danger>td,
        .table-danger>th {
            background-color: #f5c6cb;
        }

        .table-danger tbody+tbody,
        .table-danger td,
        .table-danger th,
        .table-danger thead th {
            border-color: #ed969e;
        }

        .table-hover .table-danger:hover {
            background-color: #f1b0b7;
        }

        .table-hover .table-danger:hover>td,
        .table-hover .table-danger:hover>th {
            background-color: #f1b0b7;
        }

        .table-light,
        .table-light>td,
        .table-light>th {
            background-color: #fdfdfe;
        }

        .table-light tbody+tbody,
        .table-light td,
        .table-light th,
        .table-light thead th {
            border-color: #fbfcfc;
        }

        .table-hover .table-light:hover {
            background-color: #ececf6;
        }

        .table-hover .table-light:hover>td,
        .table-hover .table-light:hover>th {
            background-color: #ececf6;
        }

        .table-dark,
        .table-dark>td,
        .table-dark>th {
            background-color: #c6c8ca;
        }

        .table-dark tbody+tbody,
        .table-dark td,
        .table-dark th,
        .table-dark thead th {
            border-color: #95999c;
        }

        .table-hover .table-dark:hover {
            background-color: #b9bbbe;
        }

        .table-hover .table-dark:hover>td,
        .table-hover .table-dark:hover>th {
            background-color: #b9bbbe;
        }

        .table-active,
        .table-active>td,
        .table-active>th {
            background-color: rgba(0, 0, 0, 0.075);
        }

        .table-hover .table-active:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }

        .table-hover .table-active:hover>td,
        .table-hover .table-active:hover>th {
            background-color: rgba(0, 0, 0, 0.075);
        }

        .table .thead-dark th {
            color: #fff;
            background-color: #343a40;
            border-color: #454d55;
        }

        .table .thead-light th {
            color: #495057;
            background-color: #e9ecef;
            border-color: #dee2e6;
        }

        .table-dark {
            color: #fff;
            background-color: #343a40;
        }

        .table-dark td,
        .table-dark th,
        .table-dark thead th {
            border-color: #454d55;
        }

        .table-dark.table-borderless {
            border: 0;
        }

        .table-dark.table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .table-dark.table-hover tbody tr:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.075);
        }

        .table-thick {
            border-collapse: collapse;
        }

        .table-thick th,
        td {
            border: 1px solid black;
        }

        .underline {
            text-align: center;
            font-weight: bold;
        }


        .hr {
            border-top: 1px solid black;
            border-bottom: 1px solid black;
        }

        .table td {
            font-size: 12px;
        }
    </style>

    <script src="<?php echo base_url() ?>assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="<?php echo base_url() ?>assets/libs/bootstrap4/popper.min.js"></script>
    <script src="<?php echo base_url() ?>assets/libs/bootstrap4/bootstrap.bundle.min.js"></script>
</head>

<body>

    <div class="container-fluid p-y">
        <div class="row">
            <div class="col-4">
                <!-- start header -->
                <table class="table table-borderless table-sm" style="text-align: center;">
                    <tbody>
                        <tr>
                            <td style="font-weight: bold;">LABACHINE LAUNDRY LOUNGE</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">DAILY SALES REPORT</td>
                        </tr>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($sales_date)) ?></td>
                        </tr>
                    </tbody>
                </table>
                <!-- end header -->

                <!-- start sales -->
                <?php if ($ds_cash > 0  ||  $ds_gcash > 0 || $ds_unpaid > 0) { ?>
                    <table class="table table-borderless table-sm" style="margin-bottom: 10px;">
                        <tbody>
                            <tr style="text-align: center;  border-bottom: 1px dashed black; font-weight:bold">
                                <td colspan="2"> SALES</td>
                            </tr>
                            <tr>
                                <td>CASH</td>
                                <td style="text-align: right;"><?php echo number_format($ds_cash, 2)  ?></td>
                            </tr>
                            <tr>
                                <td>GCASH</td>
                                <td style="text-align: right;"><?php echo number_format($ds_gcash, 2)  ?></td>
                            </tr>
                            <tr>
                                <td>UNPAID</td>
                                <td style="text-align: right;"><?php echo number_format($ds_unpaid, 2) ?></td>
                            </tr>
                            <tr style="border-top: 1px dashed black; border-bottom: 1px dashed black;font-weight:bold">
                                <td>TOTAL</td>
                                <td style="text-align: right;"><?php echo number_format($ds_total, 2)  ?></td>
                            </tr>
                        </tbody>
                    </table>
                <?php } ?>
                <!-- end sales -->

                <!-- start collection -->
                <?php if ($col_cash > 0 || $col_gcash > 0) { ?>
                    <table class="table table-borderless table-sm" style="margin-bottom: 10px;">
                        <tbody>
                            <tr style="text-align: center; font-weight:bold ">
                                <td colspan="2">COLLECTIONS</td>
                            </tr>
                            <?php if ($collection['cash']) { ?>
                                <tr style="font-weight:bold; border-bottom: 1px dashed black;font-weight:bold">
                                    <td colspan="2">CASH</td>
                                </tr>
                                <?php
                                foreach ($collection['cash'] as $date => $details) {
                                    $payment = $details['payment1Total'] + $details['payment2Total'];
                                ?>
                                    <tr>
                                        <td style="margin-left: 10px;"><?php echo date("d/m/Y", strtotime($details['dateCreated']))  ?></td>
                                        <td style="text-align: right;"><?php echo number_format($payment, 2)  ?></td>
                                    </tr>
                                <?php } ?>
                                <tr style="border-top: 1px dashed black; border-bottom: 1px dashed black;font-weight:bold">
                                    <td>TOTAL</td>
                                    <td style="text-align: right;"><?php echo number_format($col_cash, 2)  ?></td>
                                </tr>
                            <?php } ?>

                            <?php if ($collection['gcash']) { ?>
                                <tr style="font-weight:bold; border-bottom: 1px dashed black;font-weight:bold;">
                                    <td colspan="2" style="padding-top:10px ;">GCASH</td>
                                </tr>
                                <?php foreach ($collection['gcash'] as $date => $details) {
                                    $payment = $details['payment1Total'] + $details['payment2Total'];
                                ?>
                                    <tr>
                                        <td style="margin-left: 10px;"><?php echo date("d/m/Y", strtotime($details['dateCreated']))  ?></td>
                                        <td style="text-align: right;"><?php echo number_format($payment, 2)  ?></td>
                                    </tr>
                                <?php } ?>
                                <tr style="border-top: 1px dashed black; border-bottom: 1px dashed black;font-weight:bold">
                                    <td>TOTAL</td>
                                    <td style="text-align: right;"><?php echo number_format($col_gcash, 2)  ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } ?>
                <!-- end collection -->

                <!-- start items -->
                <?php if ($item_cash > 0 || $item_gcash > 0) { ?>
                    <table class="table table-borderless table-sm" style="margin-bottom: 10px;">
                        <tbody>
                            <tr style="text-align: center;  border-bottom: 1px dashed black; font-weight:bold">
                                <td colspan="2"> ITEMS</td>
                            </tr>
                            <tr>
                                <td>CASH</td>
                                <td style="text-align: right;"><?php echo number_format($item_cash, 2)  ?></td>
                            </tr>
                            <tr>
                                <td>GCASH</td>
                                <td style="text-align: right;"><?php echo number_format($item_gcash, 2)  ?></td>
                            </tr>
                            <tr style="border-top: 1px dashed black; border-bottom: 1px dashed black;font-weight:bold">
                                <td>TOTAL</td>
                                <td style="text-align: right;"><?php echo number_format($item_total, 2)  ?></td>
                            </tr>
                        </tbody>
                    </table>
                <?php } ?>
                <!-- end items -->

                <?php if ($total_expenses > 0) { ?>
                    <!-- start sales -->
                    <table class="table table-borderless table-sm" style="margin-bottom: 10px;">
                        <tbody>
                            <tr style="text-align: center;  border-bottom: 1px dashed black;font-weight:bold">
                                <td class="hr" colspan="2"> EXPENSES</td>
                            </tr>
                            <?php foreach ($expenses as $exp) { ?>
                                <tr>
                                    <td><?php echo ucwords($exp->particular) ?></td>
                                    <td style="text-align: right;"><?php echo number_format($exp->amount, 2) ?></td>
                                </tr>
                            <?php } ?>

                            <tr style="border-top: 1px dashed black; border-bottom: 1px dashed black;font-weight:bold">
                                <td>TOTAL</td>
                                <td style="text-align: right;"><?php echo number_format($total_expenses, 2) ?></td>
                            </tr>
                        </tbody>
                    </table>
                <?php } ?>
                <!-- end sales -->

                <!-- start Summary -->
                <table class="table table-borderless table-sm" style="margin-bottom: 1px;">
                    <tbody>
                        <tr style="text-align: center;  border-bottom: 1px dashed black;font-weight:bold">
                            <td class="hr" colspan="2"> CASH SUMMARY</td>
                        </tr>
                        <tr>
                            <td>CASH</td>
                            <td style="text-align: right;"><?php echo number_format($total_cash, 2) ?></td>
                        </tr>
                        <tr>
                            <td>GCASH</td>
                            <td style="text-align: right;"><?php echo number_format($total_gcash, 2) ?></td>
                        </tr>
                        <tr style="font-weight:bold;font-size:12px">
                            <td>SYSTEM CASH</td>
                            <td style="text-align: right;"><?php echo number_format($total_cash, 2) ?></td>
                        </tr>
                        <tr style="font-weight:bold;font-size:12px">
                            <td>ACTUAL CASH</td>
                            <td style="text-align: right;"><?php echo number_format($remittance, 2) ?></td>
                        </tr>
                        <tr style="border-bottom: 1px dashed black;font-weight:bold;font-size:12px">
                            <td>VARIANCE</td>
                            <td style="text-align: right;"><?php echo number_format($variance, 2) ?></td>
                        </tr>
                    </tbody>
                </table>
                <!-- end Summary -->

                <!-- start Summary -->
                <?php if ($canceled) { ?>
                    <table class="table table-borderless table-sm" style="margin-bottom: 10px;">
                        <tbody>
                            <tr style="font-size:12px">
                                <td>NO. OF CANCELED = <?php echo $canceled->count ?></td>
                                <td style="text-align: right;">TOTAL = <?php echo number_format($canceled->total, 2) ?></td>
                            </tr>
                        </tbody>
                    </table>
                <?php } ?>
                <!-- end Summary -->

                <!-- start sales -->
                <?php if ($unpaid) { ?>
                    <table class="table table-borderless table-sm mt-2" style="margin-bottom: 10px;">
                        <tbody>
                            <tr style="text-align: center; border-bottom: 1px dashed black;font-weight:bold">
                                <td class="hr" colspan="3">UNPAID LIST</td>
                            </tr>
                            <tr>
                                <td>NAME</td>
                                <td style="text-align: center;">DATE</td>
                                <td style="text-align: right;">BAL</td>
                            </tr>
                            <?php foreach ($unpaid as $un) { ?>
                                <tr>
                                    <td>[ ]<?php echo ucwords($un->customer) ?></td>
                                    <td style="text-align: center;"><?php echo date('m/d', strtotime($un->dateCreated)) ?></td>
                                    <td style="text-align: right;"><?php echo number_format($un->balance, 2) ?></td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td colspan="3" style="border-bottom: 1px dashed black;"></td>
                            </tr>
                        </tbody>
                    </table>
                <?php } ?>
                <!-- end sales -->

                <table class="table table-borderless table-sm" style="margin-bottom: 10px;">
                    <tbody>
                        <tr>
                            <td style="width: 100px;">CASHIER: </td>
                            <td style="text-align: left;"><?php echo $cashier ?></td>
                        </tr>
                        <tr>
                            <td>DATE PRINTED: </td>
                            <td style="text-align: left;"><?php echo date('m/d/Y h:i A', strtotime($date_created)) ?></td>
                        </tr>
                        <tr>
                            <td>CHECKED BY: </td>
                            <td style="border-bottom:1px solid black;"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        window.print();
        var url = '<?php echo site_url() ?>';
        console.log(url)

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