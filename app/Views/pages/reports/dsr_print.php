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
            padding: 0px 2px 0px !important;
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
    <?php if ($rec) { ?>
        <div class="container-fluid mt-3">
            <div class="row">
                <div class="col-4">
                    <table class="table table-borderless table-sm table-sales" style="text-align: center;">
                        <tbody>
                            <tr>
                                <td style="font-weight: bold;">LABACHINE LAUNDRYLOUNGE</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">DAILY SALES REPORT</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- start sales -->
                    <table class="table table-borderless table-md table-sales" style="margin-bottom: 10px;">
                        <tbody>
                            <tr style="text-align: center;  border-bottom: 1px dashed black; font-weight:bold">
                                <td colspan="2"> SALES</td>
                            </tr>
                            <tr>
                                <td>CASH</td>
                                <td style="text-align: right;"><?php echo number_format($rec->ds_cash, 2)  ?></td>
                            </tr>
                            <tr>
                                <td>GCASH</td>
                                <td style="text-align: right;"><?php echo number_format($rec->ds_gcash, 2)  ?></td>
                            </tr>
                            <tr>
                                <td>UNPAID</td>
                                <td style="text-align: right;"><?php echo number_format($rec->ds_unpaid, 2) ?></td>
                            </tr>
                            <tr style="border-top: 1px dashed black; border-bottom: 1px dashed black;font-weight:bold">
                                <td>TOTAL</td>
                                <td style="text-align: right;"><?php echo number_format($rec->ds_total, 2)  ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- end sales -->

                    <!-- start collection -->
                    <table class="table table-borderless table-md table-sales" style="margin-bottom: 10px;">
                        <tbody>
                            <tr style="text-align: center; font-weight:bold; border-bottom: 1px dashed black">
                                <td colspan="3">COLLECTIONS</td>
                            </tr>
                            <tr>
                                <td colspan="2">CASH</td>
                                <td style="text-align: right;"><?php echo number_format($rec->col_cash, 2)  ?></td>
                            </tr>
                            <tr style="border-bottom: 1px dashed black;">
                                <td colspan="2">GCASH</td>
                                <td style="text-align: right;"><?php echo number_format($rec->col_gcash, 2)  ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- end collection -->
                    <!-- start items -->
                    <table class="table table-borderless table-md table-sales" style="margin-bottom: 10px;">
                        <tbody>
                            <tr style="text-align: center;  border-bottom: 1px dashed black; font-weight:bold">
                                <td colspan="2"> ITEMS</td>
                            </tr>
                            <tr>
                                <td>CASH</td>
                                <td style="text-align: right;"><?php echo number_format($rec->item_cash, 2)  ?></td>
                            </tr>
                            <tr>
                                <td>GCASH</td>
                                <td style="text-align: right;"><?php echo number_format($rec->item_gcash, 2)  ?></td>
                            </tr>
                            <tr style="border-top: 1px dashed black; border-bottom: 1px dashed black;font-weight:bold">
                                <td>TOTAL</td>
                                <td style="text-align: right;"><?php echo number_format($rec->item_total, 2)  ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- end items -->

                    <!-- start sales -->
                    <table class="table table-borderless table-md table-sales" style="margin-bottom: 10px;">
                        <tbody>
                            <tr style="text-align: center;  border-bottom: 1px dashed black;font-weight:bold">
                                <td class="hr" colspan="2"> EXPENSES</td>
                            </tr>
                            <tr style="border-top: 1px dashed black; border-bottom: 1px dashed black;font-weight:bold">
                                <td>TOTAL</td>
                                <td style="text-align: right;"><?php echo number_format($rec->total_expenses, 2) ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- end sales -->

                    <!-- start Summary -->
                    <table class="table table-borderless table-md table-sales" style="margin-bottom: 1px;border-bottom: 1px dashed black;">
                        <tbody>
                            <tr style="text-align: center;  border-bottom: 1px dashed black;font-weight:bold">
                                <td class="hr" colspan="2"> CASH SUMMARY</td>
                            </tr>
                            <tr>
                                <td>CASH</td>
                                <td style="text-align: right;"><?php echo number_format($rec->total_cash, 2) ?></td>
                            </tr>
                            <tr>
                                <td>GCASH</td>
                                <td style="text-align: right;"><?php echo number_format($rec->total_gcash, 2) ?></td>
                            </tr>
                            <tr style="font-weight:bold;font-size:12px">
                                <td>ACTUAL CASH</td>
                                <td style="text-align: right;"><?php echo number_format($rec->remittance, 2) ?></td>
                            </tr>
                            <tr style="border-bottom: 1px dashed black;font-weight:bold;font-size:12px">
                                <td>VARIANCE</td>
                                <td style="text-align: right;"><?php echo number_format($rec->variance, 2) ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="table table-borderless table-sm table-sales">
                        <tbody>
                            <tr>
                                <th class="py-1" style="font-size: 10px; width:80px">SALES DATE </th>
                                <td style="font-size: 10px;"><?php echo date('m/d/Y h:i A', strtotime($rec->sales_date)) ?></td>
                            </tr>
                            <tr style="font-weight:bold">
                                <th class="py-1" style="font-size: 10px;">CASHIER</th>
                                <td style="font-size: 10px;"><?php echo $curr_cashier->username ?></td>
                            </tr>
                            <?php if ($rec->varSettledAmt) { ?>
                                <tr style="font-weight:bold">
                                    <th class="pt-3 pb-1" style="font-size: 10px;width:80px ">SETTLED DATE </th>
                                    <td style="font-size: 10px;"><?php echo date('m/d/Y h:i A', strtotime($rec->dateSettled)) ?></td>
                                </tr>
                                <tr style="font-weight:bold">
                                    <th class="pt-3 pb-1" style="font-size: 10px;">AMOUNT</th>
                                    <td style="font-size: 10px;"><?php echo number_format($rec->varSettledAmt, 2) ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <table class="table table-borderless table-sm table-sales" style="text-align: center;">
            <tbody>
                <tr>
                    <td style="font-weight: bold;">NO DATA FOUND</td>
                </tr>
            </tbody>
        </table>
    <?php } ?>

    <script>
        // window.print();
        // var url = '<?php echo site_url() ?>';
        // console.log(url)

        // var beforePrint = function() {
        //     console.log("before print");
        // };

        // var afterPrint = function() {
        //     window.close();
        // };

        // if (window.matchMedia) {
        //     var mediaQueryList = window.matchMedia("print");
        //     mediaQueryList.addListener(function(mql) {
        //         if (mql.matches) {
        //             beforePrint();
        //         } else {
        //             afterPrint();
        //         }
        //     });
        // }

        // window.onbeforeprint = beforePrint;
        // window.onafterprint = afterPrint;
    </script>
</body>
</html>