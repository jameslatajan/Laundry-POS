<style>
    .select2-container {
        width: 100% !important;
        font-size: 12px !important;
        /* Adjust as needed */
    }

    .select2-container .select2-selection--single {
        height: 25px !important;
        /* Adjust the height as needed */
        display: flex;
        align-items: center;
    }

    .select2-container .select2-selection--multiple {
        height: auto !important;
        /* Ensure multiple selection container adapts */
        min-height: 25px !important;
        /* Minimum height for multiple selection */
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 12px !important;
        /* Adjust to match the height */
        margin-top: 2px;
        /* Adjust spacing if needed */
        padding: 2px;
        /* Adjust padding if needed */

        background-color: white;
    }
</style>

<div class="main-panel">
    <div class="container-fluid my-2">
        <div class="row">
            <div class="col-md-12 text-start d-flex">
                <h2 class="module-title"><?php echo $title ?></h2>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header p-1">
                        <form action="<?php echo site_url('unpaid_report') ?>" method="POST" class="d-flex justify-content-between">
                            <div class="d-flex w-50">
                                <h6 class="mt-1 mr-2">MONTH</h6>
                                <select name="month" id="month" class="form-control form-control-sm select2-default wx-100">
                                    <?php foreach ($months as $index => $mon) {
                                        $value = str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?>
                                        <option value="<?php echo $value ?>" <?php if ($value == $month) echo 'selected' ?>><?php echo $mon ?></option>
                                    <?php } ?>
                                </select>
                                <h6 class="mt-1 mx-2">YEAR</h6>
                                <select name="year" id="year" class="form-control form-control-sm select2-default">
                                    <?php foreach (YEARS as $yr) { ?>
                                        <option value="<?php echo $yr ?>" <?php if ($yr == $year) echo 'selected' ?>><?php echo $yr ?></option>
                                    <?php } ?>
                                </select>
                                <button class="btn btn-primary btn-sm rounded-pill btn-transactions mx-2">FILTER</button>
                            </div>
                            <div class="text-end">
                                <?php if ($totalJo) { ?>
                                    <p class="mt-1"><?php echo $totalJo ?> orders are still unpaid.</p>
                                <?php } ?>
                            </div>
                        </form>
                    </div>
                    <?php if ($records) { ?>
                        <div class="card-body p-0">
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary btn-sm rounded-pill my-2 mr-1" onclick="popUpWin('<?php echo site_url('/unpaid_report/printform') ?>', 'Unpaid Report')">Export PDF</button>
                            </div>
                            <div class="table-responsive" style="max-height: 400px;">
                                <table class="table table-bordered table-transactions table-sm">
                                    <thead class="header-blue">
                                        <tr>
                                            <?php foreach ($getOnlyThesDates as $date) { ?>
                                                <th class="text-center fw-bold text-black" style="font-size: 12px;">
                                                    <?php echo date('m/d/Y', strtotime($date->dateCreated)) ?> <br>
                                                    <?php echo date('l', strtotime($date->dateCreated)) ?>
                                                </th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <?php foreach ($getOnlyThesDates as $date) { ?>
                                                <td style="vertical-align: baseline;">
                                                    <?php
                                                    $total_balance = 0;
                                                    $current_date  = 0;
                                                    foreach ($records as $rec) {
                                                        $recDate      = date('m/d/Y', strtotime($rec->dateCreated));
                                                        $dateDate     = date('m/d/Y', strtotime($date->dateCreated));
                                                        $current_date = date('Y-m-d', strtotime($date->dateCreated));
                                                    ?>
                                                        <?php if ($recDate == $dateDate) { ?>
                                                            <div class="card mb-1 rounded-0" style="width: 160px" onclick="newTab('<?php echo site_url('transaction/view/' . $rec->qrCode) ?>')">
                                                                <div class="card-body p-1 text-black">
                                                                    <div>
                                                                        <!-- <p style="font-size: 12px !important; " class="mb-0"> <?php echo date('m/d/Y', strtotime($rec->dateCreated)) ?> </p> -->
                                                                        <p style="font-size: 12px !important; " class="mb-0"> <?php echo $rec->customer ?> </p>
                                                                        <p style="font-size: 12px !important; " class="mb-0">Balance: <strong><?php echo  number_format($rec->balance, 2) ?></strong> </p>
                                                                    </div>
                                                                    <p style="font-size: 12px !important;" class="mb-0">JO # <u style="color: blue;"><?php echo $rec->transID ?></u> </p>
                                                                </div>
                                                            </div>
                                                        <?php
                                                            $total_balance +=  $rec->balance;
                                                        } ?>
                                                    <?php } ?>
                                                    <div class="text">
                                                        <h5 style="font-size: 12px !important; " class="text-black mb-1">Total: <?php echo number_format($total_balance, 2) ?></h5>
                                                    </div>
                                                    <div class="text">
                                                        <?php if ($variance_result) { ?>
                                                            <?php
                                                            $ctr = 1;
                                                            foreach ($variance_result as $key => $var) { ?>
                                                                <?php
                                                                if (date('Y-m-d', strtotime($key)) == date('Y-m-d', strtotime($date->dateCreated))) { ?>
                                                                    <h5 style="font-size: 12px !important; " class="text-black mb-1">Variance <?php echo $ctr ?> : <?php echo  number_format($var, 2) ?></h5>
                                                                <?php
                                                                    $ctr++;
                                                                } ?>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </div>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="card-body text-center"><i> No data found</i></div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(".select2-default").select2({
        minimumResultsForSearch: -1,
    });

    flatpickr('.flatpickr-input', {});

    function newTab(url) {
        window.open(url);
    }

    function popUpWin(pageURL, pageTitle) {
        let width = 1000;
        let height = 1000;

        // Calculate window position for the center of the screen
        let left = (screen.width - width) / 2;
        let top = (screen.height - height) / 2;

        let myWindow = window.open(pageURL, pageTitle, 'resizable=yes, width=' + width + ', height=' + height + ', top=' + top + ', left=' + left);
    }
</script>