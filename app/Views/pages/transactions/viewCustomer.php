<input type="hidden" name="balance" id="balance" value="<?php echo $customer->balance ?>">
<input type="hidden" name="qrCode" id="qrCode" value="<?php echo $customer->qrCode ?>">
<input type="hidden" name="baseUrl" id="baseUrl" value="<?php echo base_url() ?>">

<style>
    /* .list:hover {
        cursor: pointer;
    } */

    .card .card-body {
        padding: 0.5rem 0.5rem;
    }

    .table-transactions td,
    th {
        padding-top: 5px !important;
        padding-bottom: 5px !important;
        padding-top: 5px !important;
        padding-left: 5px !important;
        padding-right: 5px !important;
        font-size: 15px !important;
    }

    .table-recHead td,
    th {
        padding-top: 5px !important;
        padding-bottom: 5px !important;
        padding-left: 5px !important;
        padding-right: 5px !important;
        font-size: 24px;
    }

    .table-record td,
    th {
        padding-top: 5px !important;
        padding-bottom: 5px !important;
        padding-top: 5px !important;
        padding-left: 5px !important;
        padding-right: 5px !important;
        font-size: 15px !important;
    }

    .process:hover {
        cursor: pointer;
    }

    .received {
        color: black;
    }

    .wash {
        color: #0331f2;
    }

    .dry {
        color: #0331f2;
    }

    .fold {
        color: #0331f2;
    }

    .ready {
        color: darkgreen;
    }

    .released {
        color: green;
    }

    .canceled {
        color: red;
    }

    input[type=radio] {
        width: 20px;
        height: 1em;
    }

    .card-receive {
        background-color: #0331f2;
    }

    .card-receive:hover {
        background-color: navy;
    }

    .card-ready {
        background-color: green;
    }

    .card-ready:hover {
        background-color: darkgreen;
    }
</style>
<div class="main-panel">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-md-4 d-flex">
                <a href="<?php echo site_url('transaction') ?>" class="module-title text-dark"> <i class="mdi mdi-arrow-left model-back"></i></a>
                <h2 class="module-title"><?php echo $title ?></h2>
            </div>
            <div class="col-md-8 d-flex justify-content-end">
                <?php if ($data['user']['userType'] == 'Admin' && $customer->status != 7 && $customer->status != 0) { ?>
                    <button type="button" class="btn btn-primary rounded-pill btn-sm  btn-transactions btn-transactions ml-2" id="resolve">
                        Resolve
                    </button>
                <?php } ?>
                <?php if (($customer->payment1Cash == 0 && $customer->payment1GCash == 0) && $customer->balance > 0 && $customer->status != 0 && $customer->status != 7) { ?>
                    <button type="button" class="btn btn-primary rounded-pill btn-sm btn-transactions btn-transactions ml-2" data-toggle="modal" data-target="#payment1modal">
                        <i class="mdi mdi-cash-multiple mdi-transactions"></i> Payment 1
                    </button>
                <?php } ?>
                <?php if (($customer->payment1Cash > 0 || $customer->payment1GCash > 0) && $customer->balance > 0 && $customer->status != 0 && $customer->status != 7) { ?>
                    <button type="button" class="btn btn-primary rounded-pill btn-sm btn-transactions btn-transactions ml-2" data-toggle="modal" data-target="#payment2modal">
                        <i class="mdi mdi-cash-multiple mdi-transactions"></i> Payment 2
                    </button>
                <?php } ?>
                <button type="button" class="btn btn-primary rounded-pill btn-sm btn-transactions ml-2" id="print_claimslip">
                    <i class="mdi mdi-printer mdi-transactions"></i> Print Claim Slip
                </button>
                <button type="button" class="btn btn-primary rounded-pill btn-sm btn-transactions ml-2" id="print_joborder">
                    <i class="mdi mdi-printer mdi-transactions"></i> Print Job Order
                </button>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-md-8 pr-1">
                <div class="card mb-2">
                    <div class="card-header p-1">
                        <?php
                        $status = "";
                        if ($customer->status == 1) {
                            $status = 'Received';
                        } else if ($customer->status == 3) {
                            $status = 'Wash';
                        } else if ($customer->status == 4) {
                            $status = 'Dry';
                        } else if ($customer->status == 5) {
                            $status = 'Fold';
                        } else if ($customer->status == 6) {
                            $status = 'Ready';
                        } else if ($customer->status == 7) {
                            $status = 'Released';
                        } else {
                            $status = 'Canceled';
                        } ?>

                        <table class="table table-borderless table-sm">
                            <tbody>
                                <tr>
                                    <td class="wx-100 text-left font-weight-bold " style="font-size: 20px;">SERIES #: <?php echo str_pad($customer->transID, 4, "0", STR_PAD_LEFT) ?></td>
                                    <td class="<?php echo strtolower($status) ?> wx-100 text-right font-weight-bold" style="font-size: 20px;"> STATUS: <?php echo strtoupper($status) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-borderless table-sm mb-2">
                            <tbody>
                                <tr>
                                    <td class="wx-50 text-right" style="font-size: 18px; vertical-align:top">CUSTOMER: </td>
                                    <td class="wx-250 font-weight-bold" style="font-size: 18px; vertical-align:top"><?php echo strtoupper($customer->customer) ?></td>
                                    <td class="wx-50 text-right" style="font-size: 18px; vertical-align:top">MOBILE: </td>
                                    <td class="wx-50 font-weight-bold" style="font-size: 18px; vertical-align:top"><?php echo $customer->mobile ?></td>
                                </tr>
                            </tbody>
                        </table>

                        <table class="table table-borderless table-transactions table-sm mb-2">
                            <tbody>
                                <tr>
                                    <td class="wx-100 text-right">TRANS DATE: </td>
                                    <td class="wx-150 text-left font-weight-bold"> <?php echo date("m/d/Y h:i A", strtotime($customer->dateCreated)); ?> </td>
                                    <td class="wx-100 text-right">RECEIVED BY: </td>
                                    <td class="wx-150 text-left font-weight-bold"> <?php echo $cashier->username ?> </td>
                                </tr>
                                <tr>
                                    <td class="text-right">P. METHOD: </td>
                                    <td class="font-weight-bold"><?php echo $customer->paymentMethod ?></td>
                                    <?php if ($customer->referenceNo) { ?>
                                        <td class="text-right">REF #: </td>
                                        <td class="font-weight-bold"><?php if ($customer->referenceNo) echo $customer->referenceNo ?></td>
                                    <?php } else { ?>
                                        <td class="text-right"></td>
                                        <td class="font-weight-bold"></td>
                                    <?php } ?>
                                </tr>
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-between">
                            <table class="table table-collapse table-transactions w-75">
                                <thead class="header-blue">
                                    <tr>
                                        <th class="wx-100">Particular</th>
                                        <th class="wx-80">Qty</th>
                                        <th class="wx-100">Price</th>
                                        <th class="wx-100">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($customer->kiloAmount) { ?>
                                        <tr>
                                            <td>Clothes</td>
                                            <td><?php echo $customer->kiloQty . ' kg' ?> </td>
                                            <td><?php if ($customer->kiloPrice) echo '₱ ' . number_format($customer->kiloPrice, 2) . ' / kilo' ?> </td>
                                            <td><?php if ($customer->kiloAmount) echo " ₱ " . number_format($customer->kiloAmount, 2) ?> </td>
                                        </tr>
                                    <?php } ?>
                                    <?php if ($customer->comforterAmount) { ?>
                                        <?php
                                        $confoLabel = "";
                                        if ($customer->comforterLoad > 1) {
                                            $confoLabel =  $customer->comforterLoad . ' loads';
                                        } else {
                                            $confoLabel = $customer->comforterLoad . ' load';
                                        }
                                        ?>
                                        <tr>
                                            <td>Comforter</td>
                                            <td><?php echo $confoLabel ?> </td>
                                            <td><?php if ($customer->comforterPrice) echo '₱ ' . number_format($customer->comforterPrice, 2) . ' / load' ?></td>
                                            <td><?php if ($customer->comforterAmount) echo " ₱ " . number_format($customer->comforterAmount, 2) ?> </td>
                                        </tr>
                                    <?php } ?>
                                    <?php if ($customer->detergentAmount) { ?>
                                        <?php
                                        $deterLabel = "";
                                        if ($customer->detergentSet > 1) {
                                            $deterLabel = $customer->detergentSet . ' sets';
                                        } else {
                                            $deterLabel = $customer->detergentSet . ' set';
                                        }
                                        ?>
                                        <tr>
                                            <td>Detergent & Softener</td>
                                            <td><?php echo $deterLabel ?></td>
                                            <td><?php if ($customer->detergentPrice) echo '₱ ' . number_format($customer->detergentPrice, 2) . ' / set' ?> </td>
                                            <td><?php if ($customer->detergentAmount) echo " ₱ " . number_format($customer->detergentAmount, 2) ?> </td>
                                        </tr>
                                    <?php } ?>
                                    <?php if ($customer->bleachAmount) { ?>
                                        <?php
                                        $bleachLabel = "";
                                        if ($customer->bleachLoad > 1) {
                                            $bleachLabel =  $customer->bleachLoad . ' loads';
                                        } else {
                                            $bleachLabel =  $customer->bleachLoad . ' load';
                                        }
                                        ?>
                                        <tr>
                                            <td>Bleach</td>
                                            <td></td>
                                            <td> <?php if ($customer->bleachPrice) echo '₱ ' . number_format($customer->bleachPrice, 2) . ' / load' ?> </td>
                                            <td> <?php if ($customer->bleachAmount) echo " ₱ " . number_format($customer->bleachAmount, 2) ?> </td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td> Job Order </td>
                                        <td><?php if ($customer->totalLoads) echo $customer->totalLoads ?> </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <?php if ($customer->remarks) { ?>
                                        <tr>
                                            <td> Remarks </td>
                                            <td> <?php if ($customer->remarks) echo $customer->remarks ?> </td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                            <div class="text-left w-25 py-2 px-0 text-center rounded-right" style="background-color: #8B0000;">
                                <?php
                                $raNo = '';
                                foreach ($rackNos as $rac) {
                                    $raNo =  $rac . ', ';
                                } ?>

                                <div class="table table-collapse table-transactions mt-2">
                                    <tbody>
                                        <tr>
                                            <td style="vertical-align: middle;">
                                                <h3 class="text-white">RACK #</h3>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle;">
                                                <?php if ($rackNos) { ?>
                                                    <h3 class="text-white"><?php echo rtrim($raNo, ', ')  ?></h3>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-3 mb-0 pr-1">
                        <div class="card mb-0 card-receive process" data-toggle="modal" data-target="#modalWash">
                            <div class="card-body text-center d-block">
                                <h3 class="mt-2 text-white">Wash</h3>
                                <?php
                                $washCount = 0;
                                foreach ($wash as $row) {
                                    if ($row->status >= 3) {
                                        $washCount++;
                                    }
                                }
                                ?>
                                <?php if ($washCount == $customer->totalLoads) { ?>
                                    <span class="text-white">Complete</span>
                                <?php } ?>
                                <span class="text-white"><?php echo $washCount . '/' . $customer->totalLoads ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-3 mb-0 px-1">
                        <div class="card mb-0 card-receive process" data-toggle="modal" data-target="#modalDry">
                            <div class="card-body text-center">
                                <h3 class="mt-2 text-white">Dry</h3>
                                <?php
                                $dryCount = 0;
                                foreach ($dry as $row) {
                                    if ($row->status >= 4) {
                                        $dryCount++;
                                    }
                                } ?>
                                <?php if ($dryCount == $customer->totalLoads) { ?>
                                    <span class="text-white">Complete</span>
                                <?php } ?>
                                <span class="text-white"><?php echo $dryCount . '/' . $customer->totalLoads ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-3 mb-0 px-1">
                        <div class="card mb-0 card-receive process">
                            <div class="card-body text-center" data-toggle="modal" data-target="#modalFold">
                                <h3 class="mt-2 text-white"> Fold</h3>
                                <?php
                                $foldCount = 0;
                                foreach ($fold as $row) {
                                    if ($row->status >= 5) {
                                        $foldCount++;
                                    }
                                } ?>
                                <?php if ($dryCount == $customer->totalLoads) { ?>
                                    <span class="text-white">Complete</span>
                                <?php } ?>
                                <span class="text-white"><?php echo $foldCount . '/' . $customer->totalLoads ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-3 mb-0 pl-1">
                        <div class="card mb-0 card-ready process">
                            <div class="card-body text-center" data-toggle="modal" data-target="#modalReady">
                                <h3 class="mt-2 text-white">Ready</h3>
                                <?php
                                $readyCount = 0;
                                foreach ($ready as $row) {
                                    if ($row->status == 6) {
                                        $readyCount++;
                                    }
                                } ?>
                                <?php if ($readyCount == $customer->totalLoads) { ?>
                                    <span class="text-white"> Complete</span>
                                <?php } ?>
                                <span class="text-white"><?php echo $readyCount . '/' . $customer->totalLoads ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex mt-2">
                    <?php if ($customer->payment1Cash > 0 || $customer->payment1GCash > 0) { ?>
                        <div class="col-12 p-0">
                            <div class="card w-50">
                                <div class="card-header p-1">PAYMENT 1</div>
                                <div class="card-body">
                                    <table class="table table-borderless table-record mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-right">Cash: </td>
                                                <td class="font-weight-bold">
                                                    <span><?php if ($customer->payment1Cash) echo "₱ " . number_format($customer->payment1Cash, 2) ?></span>
                                                    <span> GCash: <?php if ($customer->payment1GCash) echo "₱ " . number_format($customer->payment1GCash, 2) ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right">Date Paid: </td>
                                                <td class="font-weight-bold"><?php echo date('m/d/Y h:i A', strtotime($customer->payment1Date)); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-right">Cashier: </td>
                                                <td class="font-weight-bold"><?php echo $payment1Cashier->username ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php } ?>


                    <?php if ($customer->payment2Cash > 0 || $customer->payment2GCash > 0) { ?>
                        <div class="col-12 p-0">
                            <div class="card w-50">
                                <div class="card-header p-1">PAYMENT 2</div>
                                <div class="card-body">
                                    <table class="table table-borderless table-record mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-right">Cash: </td>
                                                <td class="font-weight-bold">
                                                    <span><?php if ($customer->payment2Cash) echo "₱ " . number_format($customer->payment2Cash, 2) ?></span>
                                                    <span>GCash</span>
                                                    <?php if ($customer->payment2GCash) echo "₱ " . number_format($customer->payment2GCash, 2) ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right">Date Paid: </td>
                                                <td class="font-weight-bold"><?php echo date('m/d/y h:i A', strtotime($customer->payment2Date)); ?> </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right">Cashier: </td>
                                                <td class="font-weight-bold"><?php echo $payment2Cashier->username ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="col-md-4 pl-1">
                <div class="row ">
                    <div class="col-12">
                        <div class="card mb-2">
                            <div class="card-body p-1">
                                <table class="table table-borderless table-recHead mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-right wx-150 font-weight-bold">TOTAL DUE </td>
                                            <td class="font-weight-bold">₱ <?php echo  number_format($customer->totalAmount, 2) ?> </td>
                                        </tr>
                                        <tr>
                                            <td class="text-right font-weight-bold">AMT PAID </td>
                                            <td class="font-weight-bold">₱ <?php echo number_format($customer->amountPaid, 2) ?> </td>
                                        </tr>
                                        <tr>
                                            <td class="text-right font-weight-bold">BALANCE </td>
                                            <td class="font-weight-bold">₱ <?php echo number_format($customer->balance, 2) ?> </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <?php if ($customer->isSms) {
                        $message = 'Sent';
                    ?>
                        <div class="col-12">
                            <div class="card mb-2">
                                <div class="card-body">
                                    <table class="table table-borderless table-transactions mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="wx-120 text-right">SMS STATUS : </td>
                                                <td style="color:green"><?php echo $message ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($customer->status == 0) { ?>
                        <div class="col-md-12">
                            <div class="card mb-2">
                                <div class="card-body p-1">
                                    <table class="table table-borderless table-transactions mb-1" style="color:red;">
                                        <tbody>
                                            <tr>
                                                <td class="wx-120 text-right">Cancelled By: </td>
                                                <td><?php echo  $canceledBy->firstName ?> </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right">Date Cancelled: </td>
                                                <td><?php echo date("m/d/Y h:i A", strtotime($customer->dateCanceled)) ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-right">Remarks: </td>
                                                <td class="text-left"><?php echo $customer->canceledRemarks ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($customer->status == 7) { ?>
                        <div class="col-md-12">
                            <div class="card mb-2">
                                <div class="card-body p-1">
                                    <table class="table table-borderless table-transactions mb-0" style="color:green">
                                        <tbody>
                                            <tr>
                                                <td class="wx-120 text-right">Released By: </td>
                                                <td><?php echo $releasedBy->username ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-right">Date Released: </td>
                                                <td><?php echo date('m/d/y h:i A', strtotime($customer->dateReleased)); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-md-12 d-flex justify-content-between">
                <?php if ($customer->status == 1) { ?>
                    <form action="">
                        <button type="button" class="btn btn-secondary btn-md rounded-pill btn-transactions text-white" data-toggle="modal" data-target="#cancelModal">Cancel Order</button>
                    </form>
                <?php } ?>
                <?php if ($customer->balance == 0 && $customer->status != 7 && $customer->status != 0) { ?>
                    <form action="<?php echo site_url('transaction/release') ?>" method="POST" id="frmRelease" class="d-flex justify-content-between">
                        <input type="hidden" name="qrCode" value="<?php echo $customer->qrCode ?>">
                        <button type="button" class="btn btn-primary btn-md rounded-pill btn-transactions" id="release">Release</button>
                    </form>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Modal Wash -->
    <div class="modal fade" id="modalWash" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header p-1">
                    <h3 class="modal-title" id="exampleModalLabel">Wash</h3>
                </div>
                <div class="modal-body p-0">
                    <table class="table table-hover table-borderless table-striped table-transactions">
                        <thead class="header-blue">
                            <tr>
                                <th class="wx-50">JO No.</th>
                                <th class="wx-150">Date Washed</th>
                                <th class="wx-100">Washer No.</th>
                                <th class="wx-100">Washed By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($wash) { ?>
                                <?php foreach ($wash as $row) { ?>
                                    <tr>
                                        <td><?php echo $row->joNo ?></td>
                                        <td><?php if ($row->washDate != '0000-00-00 00:00:00') echo date('m/d/Y h:i A', strtotime($row->washDate)) ?></td>
                                        <td><?php if ($row->washerNo) echo $row->washerNo ?></td>
                                        <td><?php echo $row->firstName ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary btn-sm text-white rounded-pill" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Dry -->
    <div class="modal fade" id="modalDry" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header p-1">
                    <h3 class="modal-title" id="exampleModalLabel">Dry</h3>
                </div>
                <div class="modal-body p-0">
                    <table class="table table-hover table-borderless table-striped table-transactions">
                        <thead class="header-blue">
                            <tr>
                                <th class="wx-50">JO No.</th>
                                <th class="wx-150">Date Dried</th>
                                <th class="wx-100">Dryer No</th>
                                <th class="wx-100">Dried By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($dry) { ?>
                                <?php foreach ($dry as $row) { ?>
                                    <tr>
                                        <td><?php echo $row->joNo ?></td>
                                        <td><?php if ($row->dryDate != '0000-00-00 00:00:00') echo date('m/d/Y h:i A', strtotime($row->dryDate)) ?></td>
                                        <td><?php if ($row->dryerNo) echo $row->dryerNo ?></td>
                                        <td><?php echo $row->firstName ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer p-1">
                    <button type="button" class="btn btn-secondary btn-sm text-white rounded-pill" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Fold -->
    <div class="modal fade" id="modalFold" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header p-1">
                    <h3 class="modal-title" id="exampleModalLabel">Fold</h3>
                </div>
                <div class="modal-body p-0">
                    <table class="table table-hover table-borderless table-striped table-transactions">
                        <thead class="header-blue">
                            <tr>
                                <th class="wx-50">JO No.</th>
                                <th class="wx-150">Date Folded</th>
                                <th class="wx-100"> Folded By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($fold) { ?>
                                <?php foreach ($fold as $row) { ?>

                                    <tr>
                                        <td><?php echo $row->joNo ?></td>
                                        <td><?php if ($row->foldDate != '0000-00-00 00:00:00') echo date('m/d/Y h:i A', strtotime($row->foldDate)) ?></td>
                                        <td><?php echo $row->firstName ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer p-1">
                    <button type="button" class="btn btn-secondary btn-sm text-white rounded-pill" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ready -->
    <div class="modal fade" id="modalReady" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header p-1">
                    <h3 class="modal-title" id="exampleModalLabel">Ready</h3>
                </div>
                <div class="modal-body p-0">
                    <table class="table table-hover table-borderless table-striped table-transactions">
                        <thead class="header-blue">
                            <tr>
                                <th class="wx-50">JO No.</th>
                                <th class="wx-150">Date Ready</th>
                                <th class="wx-100">Rack No</th>
                                <th class="wx-100">Ready By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($ready) { ?>
                                <?php foreach ($ready as $row) { ?>
                                    <tr>

                                        <td><?php echo $row->joNo ?></td>
                                        <td><?php if ($row->readyDate != '0000-00-00 00:00:00') echo date('m/d/Y h:i A', strtotime($row->readyDate)) ?></td>
                                        <td><?php if ($row->rackNo) echo  $row->rackNo ?></td>
                                        <td><?php echo $row->firstName ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer p-1">
                    <button type="button" class="btn btn-secondary btn-sm text-white rounded-pill" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cancel-->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content modal-xl">
                <div class="modal-header p-1">
                    <h3 class="modal-title fs-5" id="exampleModalLabel">Cancel Order</h3>
                </div>
                <div class="modal-body p-1">
                    <form action="<?php echo site_url('transaction/cancel') ?>" method="POST" id="frmCancel">
                        <input type="hidden" name="qrCode" value="<?php echo $customer->qrCode ?>">
                        <div class="row">
                            <div class="col-12">
                                <h6>Remarks</h6>
                                <textarea name="canceledRemarks" id="canceledRemarks" class="form-control text-uppercase p-1" cols="10" rows="5"></textarea>
                                <small class="errors" id="canceledRemarksErr"></small>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer p-1">
                    <button type="button" class="btn btn-secondary btn-sm text-white rounded-pill" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-sm rounded-pill" id="cancel">save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Payment 1-->
    <form action="<?php echo site_url('transaction/payment1') ?>" method="POST" id="frmPayment1">
        <div class="modal fade" id="payment1modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content modal-xl">
                    <div class="modal-header p-1">
                        <h3 class="modal-title fs-5" id="exampleModalLabel">Payment 1</h3>
                    </div>
                    <div class="modal-body p-1">
                        <input type="hidden" name="qrCode" value="<?php echo $customer->qrCode ?>">
                        <input type="hidden" name="payment1Date" value="<?php echo date('Y-m-d') ?>">
                        <div class="row">
                            <div class="col-8 mb-2">
                                <h3>Balance: <?php echo number_format($customer->balance, 2, '.', '') ?></h3>
                            </div>
                            <div class="col-12">
                                <div class="form-group row mb-4" id="CashForm">
                                    <div class="col-md-12">
                                        <h4>Cash</h4>
                                        <input type="number" class="form-control form-control-sm p-1" placeholder="Input amount" id="payment1Cash" name="payment1Cash" value="" required />
                                    </div>
                                </div>
                                <div class="form-group row mb-2 referenceNoDiv" id="CashForm">
                                    <div class="col-md-6 ">
                                        <h4>GCash</h4>
                                        <input type="number" class="form-control form-control-sm p-1" placeholder="Input amount" id="payment1GCash" name="payment1GCash" value="" required />
                                    </div>
                                    <div class="col-md-6 col-12" id="referenceNoDiv">
                                        <h4>Reference No.</h4>
                                        <input type="text" class="form-control form-control-sm p-1" placeholder="Ref no." id="payment1ReferenceNo" name="payment1ReferenceNo" value="" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer p-1">
                        <button type="button" class="btn btn-secondary btn-sm text-white rounded-pill" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary btn-sm rounded-pill" id="payment1button">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal Payment 2-->
    <div class="modal fade" id="payment2modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header p-1">
                    <h3 class="modal-title fs-5" id="exampleModalLabel">Payment 2</h3>
                </div>
                <div class="modal-body p-1">
                    <form action="<?php echo site_url('transaction/payment2') ?>" method="POST" id="frmPayment2">
                        <div class="row">
                            <div class="col-8 mb-2">
                                <h3 for="">Balance: <?php echo number_format($customer->balance, 2, '.', '') ?></h3>
                            </div>
                            <input type="hidden" name="qrCode" value="<?php echo $customer->qrCode ?>">
                            <input type="hidden" name="payment2Date" value="<?php echo date('Y-m-d') ?>">
                            <div class="col-12">
                                <small id="payment2err" class="errors"></small>
                                <div class="form-group row mb-2 " id="CashForm">
                                    <div class="col-md-12 ">
                                        <h4>Cash:</h4>
                                        <input type="number" class="form-control form-control-sm p-1" placeholder="Input Amount" id="payment2Cash" name="payment2Cash" value="" />
                                    </div>
                                </div>
                                <div class="form-group row mb-2 referenceNoDiv" id="CashForm">
                                    <div class="col-md-6 ">
                                        <h4>GCash:</h4>
                                        <input type="number" class="form-control form-control-sm p-1" placeholder="Input Amount" id="payment2GCash" name="payment2GCash" value="" />
                                    </div>
                                    <div class="col-md-6 col-12" id="referenceNoDiv">
                                        <h4>Reference No.:</h4>
                                        <input type="text" class="form-control form-control-sm p-1" placeholder="Ref no." id="payment2ReferenceNo" name="payment2ReferenceNo" value="" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer p-1">
                    <button type="button" class="btn btn-secondary btn-sm text-white rounded-pill" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-sm rounded-pill" id="payment2button">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#release").on("click", function() {
                var status = 7;
                var qrCode = $("#qrCode").val();
                var task = "release";

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You're trying to release this order. Do you wish to proceed?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#frmRelease').submit();
                    }
                })
            });

            $("#cancel").on("click", function() {
                var canceledRemarks = $('#canceledRemarks').val();
                if (canceledRemarks != "") {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You're trying to cancel this order. Do you wish to proceed?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#frmCancel').submit();
                        }
                    })

                } else {
                    Swal.fire({
                        title: 'Something went wrong',
                        text: 'Remarks is required',
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Ok'
                    });
                }
            });


            $("#payment1button").on("click", function() {
                var cash = $('#payment1Cash').val();
                var gcash = $('#payment1GCash').val();
                var reference = $('#payment1ReferenceNo').val();
                var balance = parseFloat('<?php echo $customer->balance ?>');
                var totalAmount = parseFloat(cash) + parseFloat(gcash);
                var isPayment = true;

                if (!cash && !gcash) {
                    Swal.fire({
                        title: 'Something went wrong',
                        text: 'Amount should not be empty',
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Ok'
                    })

                    isPayment = false;
                }

                if (cash && gcash) {
                    if (totalAmount > balance) {
                        Swal.fire({
                            title: 'Something went wrong',
                            text: 'Total amount should not be greater than balance',
                            icon: 'warning',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Ok'
                        })
                        isPayment = false;
                    }

                    if (!reference) {
                        Swal.fire({
                            title: 'Something went wrong',
                            text: 'Reference no. not set',
                            icon: 'warning',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Ok'
                        })
                        isPayment = false;
                    }
                } else {

                    if (cash) {
                        if (cash > balance) {
                            Swal.fire({
                                title: 'Something went wrong',
                                text: 'Amount should not be greater than balance',
                                icon: 'warning',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Ok'
                            })
                            isPayment = false;
                        }
                    }

                    if (gcash) {
                        if (!reference) {
                            Swal.fire({
                                title: 'Something went wrong',
                                text: 'Reference no. not set',
                                icon: 'warning',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Ok'
                            })
                            isPayment = false;
                        }

                        if (gcash > balance) {
                            Swal.fire({
                                title: 'Something went wrong',
                                text: 'Amount should not be greater than balance',
                                icon: 'warning',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Ok'
                            })
                            isPayment = false;
                        }

                    }
                }

                if (isPayment) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Would you like to save this payment?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#payment1button').attr('disabled', true);
                            $('#frmPayment1').submit();
                        }
                    })
                }
            });

            $("#payment2button").on("click", function() {
                var cash = $('#payment2Cash').val();
                var gcash = $('#payment2GCash').val();
                var reference = $('#payment2ReferenceNo').val();
                var balance = parseFloat('<?php echo $customer->balance ?>');
                var totalAmount = parseFloat(cash) + parseFloat(gcash);
                var isPayment = true;

                if (!cash && !gcash) {
                    Swal.fire({
                        title: 'Something went wrong',
                        text: 'Amount should not be empty',
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Ok'
                    })

                    isPayment = false;
                }

                if (cash && gcash) {
                    if (totalAmount != balance) {
                        Swal.fire({
                            title: 'Something went wrong',
                            text: 'Total amount should be equal to balance',
                            icon: 'warning',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Ok'
                        })

                        isPayment = false;
                    }

                    if (!reference) {
                        Swal.fire({
                            title: 'Something went wrong',
                            text: 'Reference no. not set',
                            icon: 'warning',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Ok'
                        })

                        isPayment = false;
                    }
                } else {
                    if (cash) {
                        if (cash != balance) {
                            Swal.fire({
                                title: 'Something went wrong',
                                text: 'Amount should be equal to balance',
                                icon: 'warning',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Ok'
                            })

                            isPayment = false;
                        }
                    }

                    if (gcash) {
                        if (!reference) {
                            Swal.fire({
                                title: 'Something went wrong',
                                text: 'Reference no. not set',
                                icon: 'warning',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Ok'
                            })

                            isPayment = false;
                        }

                        if (gcash != balance) {
                            Swal.fire({
                                title: 'Something went wrong',
                                text: 'Amount should be equal to balance',
                                icon: 'warning',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Ok'
                            })
                            isPayment = false;
                        }

                    }
                }

                if (isPayment) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Would you like to save this payment?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#payment2button').attr('disabled', true);
                            $('#frmPayment2').submit();
                        }
                    })
                }
            });

            $('input[type=number][max]:not([max=""])').on('input', function(ev) {
                var $this = $(this);
                var maxlength = $this.attr('max').length;
                var value = $this.val();
                if (value && value.length >= maxlength) {
                    $this.val(value.substr(0, maxlength));
                }
            });

            $('#print_claimslip').on('click', function() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Your are going print this data. Do you wish to proceed?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var width = 20;
                        var height = 20;
                        var left = 0;
                        var top = (window.innerHeight) - (height);
                        var url1 = '<?php echo site_url("print/" . $customer->qrCode) ?>';
                        var options = `width=${width},height=${height},top=${top},left=${left},resizable=0,fullscreen=0`;
                        var popup1 = window.open(url1, "Popup1", options);
                    }
                })
            });

            $('#print_joborder').on('click', function() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Your are going print this data. Do you wish to proceed?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var width = 20;
                        var height = 20;
                        var left = 0;
                        var top = (window.innerHeight) - (height);
                        var url2 = '<?php echo site_url("job_order_print/" . $customer->qrCode) ?>';
                        var options = `width=${width},height=${height},top=${top},left=${left},resizable=0,fullscreen=0`;
                        var popup1 = window.open(url2, "Popup1", options);
                    }
                })
            });

            $('#printOrder').on('click', function() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Your are going print this data. Do you wish to proceed?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let url = $('#baseUrl').val() + 'print/' + $("#qrCode").val();
                        window.location.href = url;
                    }
                })
            });


            $('#resolve').on('click', function() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You are going to save this data. Do you wish to proceed?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "POST",
                            url: "<?php echo site_url('/transaction/resolve') ?>",
                            data: {
                                transID: '<?php echo $customer->transID ?>'
                            },
                            dataType: "json",
                            beforeSend: function() {
                                $('#resolve').html('<span class="spinner-border spinner-border-sm"></span>');
                                $('#resolve').attr('disabled', true);
                            },
                            success: function(response) {
                                console.log(response)
                                if (response.status) {
                                    Swal.fire({
                                        title: 'Saved',
                                        text: 'Succesfully Saved',
                                        icon: 'success',
                                        confirmButtonColor: '#3085d6',
                                        confirmButtonText: 'Yes'
                                    }).then((result) => {
                                        window.location.reload();
                                    })
                                }
                            },
                            error: function() {
                                $('.spinner-border').remove()
                                $('#resolve').text('Resolve');
                                $('#resolve').attr('disabled', false);

                                Swal.fire({
                                    title: 'Error',
                                    text: 'Error Saving',
                                    icon: 'error',
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'Yes'
                                })
                            }
                        });
                    }
                })
            });
        });
    </script>