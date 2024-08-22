<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry Shop</title>

    <?php if (isset($css)) { ?>
        <?php foreach ($css as $row) { ?>
            <link rel="stylesheet" href="<?php echo $row . '?ver=2' ?>" />
        <?php } ?>
    <?php } ?>


    <?php if (isset($js)) { ?>
        <?php foreach ($js as $row) { ?>
            <script src="<?php echo $row . '?ver=2' ?>"></script>
        <?php } ?>
    <?php } ?>

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
            padding-bottom: 3px;
            margin-top: 3px;
            margin-bottom: 3px;
        }

        .table-claim-slip p {
            line-height: 1.2 !important;
        }

        .hr {
            border-top: dashed 1px;
        }

        /* 
        td,
        th {
            border: 1px solid black !important;
        }

        table {
            border-collapse: collapse !important;
        } */
    </style>

    <link rel="shortcut icon" href="<?php echo base_url() ?>images/LOGO.jpg" />
</head>

<body>
    <input type="hidden" name="url" id="url" value="<?php echo base_url('unpaid') ?>">
    <div class="container-fluid justify-content-center content py-2 mx-0">
        <div class="row">
            <div class="col-md-12">
                <h3 class="text-center">LABACHINE LAUNDRY LOUNGE</h3>
                <h3 class="text-center">UNPAID REPORT</h3>
            </div>
        </div>
        <?php if (isset($records) && $records != Null) { ?>
            <div class="row">
                <div class="col-md-12">
                    <table>
                        <thead border="1" cellpadding="0" cellspacing="0" width="200px" style="border-collapse:collapse;">
                            <tr style="border: 1px solid black">
                                <th style="border: 1px solid black">Series No.</th>
                                <th style="border: 1px solid black">Date Created</th>
                                <th style="border: 1px solid black">Customer</th>
                                <th style="border: 1px solid black">Mobile</th>
                                <th style="border: 1px solid black">Amount Paid</th>
                                <th style="border: 1px solid black">Balance</th>
                                <th style="border: 1px solid black">Amount Due</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $amountPaid = 0;
                            $balance    = 0;
                            $amountDue  = 0;
                            ?>
                            <?php foreach ($records['records'] as $row) { ?>
                                <tr style="border: 1px solid black">
                                    <td style="border: 1px solid black"><?php echo str_pad($row['transID'], 4, '0', STR_PAD_LEFT) ?></td>
                                    <td style="border: 1px solid black"><?php
                                                                        $dateCreate = date_create($row['dateCreated']);
                                                                        $dateFormat = date_format($dateCreate, 'm/d/Y');
                                                                        echo $dateFormat; ?></td>
                                    <td style="border: 1px solid black"><?php echo $row['customer'] ?></td>
                                    <td style="border: 1px solid black"><?php echo $row['mobile'] ?></td>
                                    <td style="border: 1px solid black"><?php echo number_format($row['amountPaid'], 2, '.', ',') ?></td>
                                    <td style="border: 1px solid black"><?php echo number_format($row['balance'], 2, '.', ',') ?></td>
                                    <td style="border: 1px solid black"><?php echo number_format($row['totalAmount'], 2, '.', ',') ?></td>
                                </tr>
                        </tbody>
                        <?php
                                $amountPaid += $row['amountPaid'];
                                $balance    += $row['balance'];
                                $amountDue  += $row['totalAmount'];
                        ?>
                    <?php } ?>
                    <tfoot>
                        <tr style="border: 1px solid black">
                            <th style="border: 1px solid black"></th>
                            <th style="border: 1px solid black"></th>
                            <th style="border: 1px solid black"></th>
                            <th style="border: 1px solid black">Total</th>
                            <th style="border: 1px solid black"><?php echo number_format($amountPaid, 2, '.', ',') ?></th>
                            <th style="border: 1px solid black"><?php echo number_format($balance, 2, '.', ',') ?></th>
                            <th style="border: 1px solid black"><?php echo number_format($amountDue, 2, '.', ',') ?></th>
                        </tr>
                    </tfoot>
                    </table>
                </div>
            </div>
        <?php } else { ?>
            <div class="row">
                <div class="col-md-12">
                    <hr>
                    <h6>No data available</h6>
                    <hr>
                </div>
            </div>
        <?php } ?>
    </div>
</body>

</html>