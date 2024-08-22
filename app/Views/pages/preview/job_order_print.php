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
            padding: 0px !important;
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

        .hr2 {
            border-top: 1px solid black;
        }

        .square {
            height: 60px;
            width: 60px;
            background-color: white;
            border-style: solid;
            border-width: thin;
            margin-left: 90%;
        }
    </style>


    <script src="<?php echo base_url() ?>assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="<?php echo base_url() ?>assets/libs/bootstrap4/popper.min.js"></script>
    <script src="<?php echo base_url() ?>assets/libs/bootstrap4/bootstrap.bundle.min.js"></script>
    <script src="<?php echo base_url() ?>assets/libs/qrcode/jquery.qrcode.js"></script>
    <script src="<?php echo base_url() ?>assets/libs/qrcode/qrcode.js"></script>
</head>

<body>
    <div class="container-fluid py-5">
        <input type="hidden" name="transID" id="transID" value="<?php echo $customer->transID ?>">
        <input type="hidden" name="site_url" id="site_url" value="<?php echo site_url('check/') ?>">
        <input type="hidden" name="url" id="url" value="<?php echo site_url('') ?>">

        <?php foreach ($joborders as $jo) { ?>
            <div class="row">
                <div class="col-4 text-center px-1">
                    <?php if ($customer->tranType == 'Express Regular' || $customer->tranType == 'Express Student') { ?>
                        <h1 class="heading2 text-uppercase"><strong>RUSH</strong></h1>
                    <?php } ?>
                    <h2 class="heading2 text-uppercase mb-3"><strong>Job Order</strong></h2>
                    <h3 class="mb-0" id="customer"><?php echo strtoupper($customer->customer) ?></h3>
                    <p style="font-size: 15px;" class="mb-0">
                        <?php
                        if ($customer->kiloQty > 0) {
                            echo $customer->kiloQty . ' kg /';
                        }
                        if ($customer->comforterLoad > 0) {
                            echo $customer->comforterLoad  . ' loads /';
                        }

                        echo " ₱ " . number_format($customer->totalAmount, 2);
                        ?></p>
                    <p class="" style="font-size: 15px;"><?php echo date("m/d/Y h:i A", strtotime($customer->dateCreated)); ?></p>
                </div>
            </div>

            <div class="row">
                <?php if ($customer->balance > 0) { ?>
                    <div class="col-4 text-center px-1">
                        <h1 class="mb-2">UNPAID</h1>
                        <div id="code_<?php echo $jo->joNo; ?>">
                            <div class="qrcode"></div>
                        </div>
                        <h4 class="mt-1"><?php echo 'Balance: ' .  "₱ " .  number_format($customer->balance, 2) ?></h4>
                    </div>
                <?php } else { ?>
                    <div class="col-4 mb-2 text-center px-1">
                        <h1 class="mb-2">PAID</h1>
                        <div id="code_<?php echo $jo->joNo; ?>">
                            <div class="qrcode"></div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <?php if ($customer->remarks) { ?>
                <div class="row">
                    <div class="col-4 mt-2 px-1">
                        <h4 class="">Remarks: <?php echo $customer->remarks ?></h4>
                    </div>
                </div>
            <?php } ?>

            <div class="row">
                <div class="col-4 d-flex justify-content-between px-1">
                    <h6><?php echo '#' . str_pad($customer->transID, 4, "0", STR_PAD_LEFT); ?></h6>
                    <span><?php echo $jo->joNo . ' / ' . $customer->totalLoads ?></span>
                </div>
            </div>

            <?php if ($jo->joNo == 1) { ?>
                <div class="row">
                    <div class="col-4 px-1">
                        <div class="disclaimer">
                            <h6 class="mb-0"><strong>Disclaimer:</strong> </h6>
                            <p class="mb-1" style="padding-top: 5px; padding-left: 10px; line-height: 120%">LABACHINE will not be responsible for any loss or damage to valuable, branded, and/or luxury clothing that should have been personally hand-washed and carefully dried by its owner.</p>
                            <p style="padding-left: 30px; padding-top: 10px; line-height: 120%">I hereby certify that I have already checked all the clothes for any valuable items.</p>
                            <div class="signature" style="padding-top: 30px;">
                                <div class="hr2" style="width: 200px; margin:auto"></div>
                                <p style="font-size: 12px; text-align:center">Signature over Printed Name</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php if ($jo->joNo != $customer->totalLoads) { ?>
                <div class="row">
                    <div class="col-4 px-1">
                        <div class="hr mb-3  my-4"></div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>

        <div class="row">
            <div class="col-4 mt-2 px-1">
                <div class="hr mb-3  my-4"></div>
                <h3 class="text-center"><strong>BAG</strong></h3>
                <h3 class="text-center"><?php echo strtoupper($customer->customer) ?></h3>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            if ($(".customerName").text().length > 20) {
                $(".customerName").css("font-size", "15px");
            }

            let url = $('#url').val();
            <?php foreach ($joborders as $jo) { ?>
                // for app
                var qrCodeContent<?php echo $jo->joNo; ?> = "<?php echo $jo->qrCode; ?>";

                // old
                // var qrCodeContent<?php echo $jo->joNo; ?> = "<?php echo site_url() . 'check/' . $customer->qrCode . '/' . $jo->qrCode; ?>";


                $("#code_<?php echo $jo->joNo; ?> .qrcode").qrcode({
                    width: 170,
                    height: 170,
                    text: qrCodeContent<?php echo $jo->joNo; ?>
                });
            <?php } ?>

            window.print();
        });

        //before print
        var beforePrint = function() {
            // window.close();
        };
        //after print
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