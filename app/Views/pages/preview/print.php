<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?></title>
    <link rel="shortcut icon" href="<?php echo base_url() ?>assets/images/LOGO.jpg" />
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/libs/bootstrap4/bootstrap.min.css">

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
        }

        img {
            margin: auto;
        }

        .heading,
        .heading2 {
            margin-bottom: 0px !important;
        }

        .table-total td {
            font-size: 14px;
            padding: 0px !important;
        }

        hr {
            margin: 0px !important;
        }

        img {
            width: 50%;
        }

        .table-claim-slip td,
        .table-claim-slip th {
            padding-top: 0px;
            padding-bottom: 5px;
            /* margin-top: 3px; */
            /* margin-bottom: 3px; */
        }

        .table-claim-slip p {
            line-height: 1.2 !important;
        }

        .hr {
            border-top: 1px solid black;
        }
    </style>

    <script src="<?php echo base_url() ?>assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="<?php echo base_url() ?>assets/libs/bootstrap4/popper.min.js"></script>
    <script src="<?php echo base_url() ?>assets/libs/bootstrap4/bootstrap.bundle.min.js"></script>
    <script src="<?php echo base_url() ?>assets/libs/qrcode/jquery.qrcode.js"></script>
    <script src="<?php echo base_url() ?>assets/libs/qrcode/qrcode.js"></script>
</head>

<body>
    <input type="hidden" name="url" id="url" value="<?php echo base_url('job_order_print/') ?>">
    <input type="hidden" name="qrCode" id="qrCode" value="<?php echo $customer->qrCode ?>">
    <input type="hidden" name="site_url" id="site_url" value="<?php echo site_url('claimSlip/') ?>">

    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-4">
                <div class="header">
                    <p class="text-center m-0"><strong>Laundry Shop Laundry Lounge</strong> </p>
                    <p class="text-center m-0">Narciso-Vasquez Street, Surigao City, Philippines</p>
                    <p class="text-center m-0">FB: Laundry Shop Laundry Lounge</p>
                    <p class="text-center">0918-547-1843</p>
                    <div class="d-flex justify-content-between">
                        <h5><strong><?php echo '#' . str_pad($customer->transID, 4, "0", STR_PAD_LEFT); ?></strong></h5>
                        <p><?php echo date('m/d/Y h:s A', strtotime($customer->dateCreated)); ?></p>
                    </div>
                    <p class="text-center m-1"><strong><?php echo $title ?></strong></p>
                    <?php if ($customer->tranType == 'Express Regular' || $customer->tranType == 'Express Student') { ?>
                        <p class="text-center m-1">RUSH</p>
                    <?php } ?>
                    <p class="m-0">Customer: <?php echo $customer->customer ?></p>
                    <p class="m-0"><?php echo $customer->mobile ?></p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-4">
                <div class="hr"></div>

                <table class="table table-sm table-borderless table-claim-slip">
                    <tbody>
                        <?php if ($customer->kiloQty != 0) { ?>
                            <tr>
                                <td>
                                    <p class="m-0">Clothes</p>
                                    <p class="m-0"><?php echo $customer->kiloQty ?> kg x<?php echo "₱ " . number_format($customer->kiloPrice, 2) ?></p>
                                </td>
                                <td style="vertical-align: middle;" class="text-right"><?php echo "₱ " . number_format($customer->kiloAmount, 2) ?> </td>
                            </tr>
                        <?php } ?>
                        <?php if ($customer->comforterLoad != 0) { ?>
                            <tr>
                                <td>
                                    <p class="m-0">Comforter</p>
                                    <?php
                                    $load = "";
                                    if ($customer->comforterLoad > 1) {
                                        $load = ' loads x ';
                                    } else {
                                        $load = ' load x ';
                                    } ?>
                                    <p class="m-0"> <?php echo $customer->comforterLoad . $load . "₱ " . number_format($customer->comforterPrice, 2) ?>
                                    </p>
                                </td>
                                <td style="vertical-align: middle;" class="text-right"><?php echo "₱ " . number_format($customer->comforterAmount, 2) ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($customer->detergentSet != 0) { ?>
                            <tr>
                                <td>
                                    <p class="m-0">Detergent & Softener</p>
                                    <?php
                                    $set = "";
                                    if ($customer->detergentSet > 1) {
                                        $set = ' sets x ';
                                    } else {
                                        $set = ' set x ';
                                    }
                                    ?>
                                    <p class="m-0"><?php echo $customer->detergentSet . $set . "₱ " . number_format($customer->detergentPrice, 2) ?></p>
                                </td>
                                <td style="vertical-align: middle;" class="text-right"> <?php echo "₱ " . number_format($customer->detergentAmount, 2) ?> </td>
                            </tr>
                        <?php } ?>
                        <?php if ($customer->bleachLoad != 0) { ?>
                            <tr>
                                <td>
                                    <p class="m-0">Bleach</p>
                                    <?php
                                    $set = "";
                                    if ($customer->bleachLoad > 1) {
                                        $set = ' loads x ';
                                    } else {
                                        $set = ' load x ';
                                    }
                                    ?>
                                    <p class="m-0"><?php echo $customer->bleachLoad . $set . "₱ " . number_format($customer->bleachPrice, 2) ?></p>
                                </td>
                                <td style="vertical-align: middle;" class="text-right"> <?php echo "₱ " . number_format($customer->bleachAmount, 2) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <div class="hr"></div>

                <table class="table table-borderless mb-0 table-total">
                    <tbody>
                        <tr>
                            <td>
                                <h3 class="fw-bold m-0">Total</h3>
                            </td>
                            <td>
                                <h3 class="fw-bold text-right m-0"><?php echo "₱ " . number_format($customer->totalAmount, 2) ?></h3>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p class="m-0">Amount Paid</p>
                            </td>
                            <td>
                                <p class="text-right m-0"><?php echo "₱ " . number_format($customer->amountPaid, 2) ?></p>
                            </td>
                        </tr>
                        <?php if ($customer->balance > 0) { ?>
                            <tr>
                                <td>
                                    <p class="font-weight-bold m-0" style="font-size: 15px;">Balance</p>
                                </td>
                                <td>
                                    <p class="text-right font-weight-bold m-0" style="font-size: 15px;"><?php echo "₱ " . number_format($customer->balance, 2) ?></p>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <div class="hr"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-4">
                <p class="heading2">Cashier: <?php echo $cashier->username ?></p>
                <div class="h5 text-center">Thank you!</div>
                <!-- qrcode -->
                <div class="text-center mb-2">
                    <div id="qrcode" class="qrcode"></div>
                </div>
                <div class="disclaimer">
                    <h6 class="mb-0"><strong>Disclaimer:</strong> </h6>
                    <p class="mb-1" style="padding-top: 5px; padding-left: 10px; line-height: 120%">LABACHINE will not be responsible for any loss or damage to valuable, branded, and/or luxury clothing that should have been personally hand-washed and carefully dried by its owner.</p>
                    <p style="padding-left: 30px; padding-top: 8px; line-height: 120%">I hereby certify that I have already checked all the clothes for any valuable items.</p>
                    <div class="signature" style="padding-top: 30px;">
                        <div class="hr" style="width: 200px; margin:auto"></div>
                        <p style="font-size: 12px; text-align:center">Signature over Printed Name</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let url = $('#url').val();
            let qrcode = $('#qrCode').val();

            // for the app
            var newUrl = $("#qrCode").val();

            // old
            // var newUrl = $('#site_url').val() + $("#qrCode").val();
            
            $("#qrcode").qrcode({
                width: 150,
                height: 150,
                text: newUrl,
            });

            window.print();
        });

        // function
        var beforePrint = function() {};

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