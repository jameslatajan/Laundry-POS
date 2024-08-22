<style>
    .select2-container {
        width: 100% !important;
        font-size: 12px !important;
        /* Adjust as needed */
    }

    .select2-container .select2-selection--single {
        height: 30px !important;
        /* Adjust the height as needed */
        display: flex;
        align-items: center;
    }

    .select2-container .select2-selection--multiple {
        height: auto !important;
        /* Ensure multiple selection container adapts */
        min-height: 30px !important;
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
    <div class="container-fluid mt-2">
        <div class="row">
            <div class="col-12">
                <h2 class="module-title"><?php echo $title ?></h2>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header p-1">
                        <form action="<?php echo site_url('statistics/dsrsummary') ?>" method="POST" class="d-flex w-50">
                            <h6 class="mt-2 mr-2">MONTH</h6>
                            <select name="month" id="month" class="selectpicker form-control form-control-sm form-control-transactions select2-default ">
                                <option value="">Choose</option>
                                <?php foreach ($months as $index => $month) {
                                    $value = str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?>
                                    <option value="<?php echo $value ?>" <?php if ($value == $mon) echo 'selected' ?>><?php echo $month ?></option>
                                <?php } ?>
                            </select>
                            <h6 class="mt-2 mx-2 ">YEAR</h6>
                            <select name="year" id="year" class="selectpicker form-control form-control-sm form-control-transactions select2-default">
                                <option value="">Choose</option>
                                <?php foreach (YEARS as $yr) { ?>
                                    <option value="<?php echo $yr ?>" <?php if ($yr == $year) echo 'selected' ?>><?php echo $yr ?></option>
                                <?php } ?>
                            </select>
                            <button class="btn btn-primary btn-sm rounded-pill btn-transactions ml-2">FILTER</button>
                        </form>
                    </div>
                    <div class="card-body p-0">
                        <div class="button m-1 d-flex justify-content-end">
                            <a href="<?php echo site_url('/statistics/dsrsummary/exportlist') ?>" class="btn btn-primary btn-sm btn-transactions rounded-pill">Export</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-transactions table-sm " style="width: 120%;">
                                <thead class="header-blue">
                                    <tr>
                                        <th rowspan="2">SALES DATE</th>
                                        <th rowspan="2">CASHIER</th>
                                        <th colspan="4" class="text-center">SALES</th>
                                        <th colspan="3" class="text-center">COLLECTION</th>
                                        <th colspan="3" class="text-center">ITEMS</th>
                                        <th rowspan="2">TOTAL CASH</th>
                                        <th rowspan="2">TOTAL GCASH</th>
                                        <th rowspan="2">EXPENSES</th>
                                        <th rowspan="2">REMITTANCE</th>
                                        <th rowspan="2">VARIANCE</th>
                                        <th rowspan="2">SETTLE AMOUNT</th>
                                        <th rowspan="2">DATE SETTLED</th>
                                    </tr>
                                    <tr>
                                        <th>CASH</th>
                                        <th>GCASH</th>
                                        <th>UNPAID</th>
                                        <th>TOTAL</th>
                                        <th>CASH</th>
                                        <th>GCASH</th>
                                        <th>TOTAL</th>
                                        <th>CASH</th>
                                        <th>GCASH</th>
                                        <th>TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($records) { ?>
                                        <?php foreach ($records as $rec) { ?>
                                            <tr>
                                                <td><?php echo date('m/d/Y', strtotime($rec->sales_date)) ?></td>
                                                <td><?php echo $rec->username ?></td>
                                                <td style="text-align: right;"><?php if ($rec->ds_cash) echo number_format($rec->ds_cash) ?></td>
                                                <td style="text-align: right;"><?php if ($rec->ds_gcash) echo number_format($rec->ds_gcash) ?></td>
                                                <td style="text-align: right;"><?php if ($rec->ds_unpaid) echo number_format($rec->ds_unpaid) ?></td>
                                                <td style="text-align: right;"><?php if ($rec->ds_total) echo number_format($rec->ds_total) ?></td>
                                                <td style="text-align: right;"><?php if ($rec->col_cash) echo number_format($rec->col_cash) ?></td>
                                                <td style="text-align: right;"><?php if ($rec->col_gcash) echo number_format($rec->col_gcash) ?></td>
                                                <td style="text-align: right;"><?php if ($rec->col_total)  echo number_format($rec->col_total) ?></td>
                                                <td style="text-align: right;"><?php if ($rec->item_cash)  echo number_format($rec->item_cash) ?></td>
                                                <td style="text-align: right;"><?php if ($rec->item_gcash) echo number_format($rec->item_gcash) ?></td>
                                                <td style="text-align: right;"><?php if ($rec->item_total)  echo number_format($rec->item_total) ?></td>
                                                <td style="text-align: right;"><?php if ($rec->total_cash)  echo number_format($rec->total_cash) ?></td>
                                                <td style="text-align: right;"><?php if ($rec->total_gcash)   echo number_format($rec->total_gcash) ?></td>
                                                <td style="text-align: right;"><?php if ($rec->total_expenses)  echo number_format($rec->total_expenses) ?></td>
                                                <td style="text-align: right;"><?php if ($rec->remittance)  echo number_format($rec->remittance) ?></td>
                                                <td style="text-align: right;"><?php if ($rec->variance) echo number_format($rec->variance) ?></td>
                                                <td style="text-align: right;"><?php if ($rec->varSettledAmt) echo number_format($rec->varSettledAmt) ?></td>
                                                <td style="text-align: right;"><?php if ($rec->dateSettled != "0000-00-00 00:00:00") echo date('m/d/Y', strtotime($rec->dateSettled)) ?></td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr>
                                            <td colspan="19">No Data Found</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(".select2-default").select2({
        minimumResultsForSearch: -1,
    });
</script>