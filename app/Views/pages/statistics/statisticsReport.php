<style>
    .table-transaction td {
        font-size: 15px;
    }

    .table-transaction th {
        font-size: 15px !important;
    }

    .table-transaction table {
        width: 100% !important;
        margin: 0 !important;
    }

    .table-transactions thead tr th {
        color: #fff;
        text-align: left;
        background-color: #000;
        position: relative;
    }

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
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header p-1">
                        <form action="<?php echo site_url('statistics/statreport') ?>" method="POST" class="d-flex w-25">
                            <h6 class="mt-2 mr-2">YEAR </h6>
                            <select name="year" id="year" class="form-control form-control-sm select2-default form-control-transactions wx-200">
                                <option value="">---</option>
                                <?php foreach (YEARS as $yr) { ?>
                                    <option value="<?php echo $yr ?>" <?php if ($yr == $year) echo 'selected' ?>><?php echo $yr ?></option>
                                <?php } ?>
                            </select>
                            <button class="btn btn-primary btn-sm rounded-pill btn-transactions ml-2">FILTER</button>
                        </form>
                    </div>

                    <?php if ($records) { ?>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="MVCGridTable_Grid" class="table table-hover table-sm table-bordered table-transaction">
                                    <thead class="header-blue">
                                        <tr>
                                            <th style="width: 150px;">Month</th>
                                            <th class="text-end" style="width: 150px;"># Customers</th>
                                            <th class="text-end" style="width: 150px;">Regular</th>
                                            <th class="text-end" style="width: 150px;">Student</th>
                                            <th class="text-end" style="width: 150px;">Express</th>
                                            <th class="text-end" style="width: 150px;">Total Loads</th>
                                            <th class="text-end" style="width: 150px;">Total JO</th>
                                            <th class="text-end" style="width: 150px;">Total Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $tot_customer = 0;
                                        $tot_regular  = 0;
                                        $tot_student  = 0;
                                        $tot_express  = 0;
                                        $tot_load     = 0;
                                        $tot_jo       = 0;
                                        $tot_amount   = 0;
                                        foreach ($records as $key => $rec) { ?>
                                            <tr>
                                                <td><?php if ($key) echo $key ?></td>
                                                <td class="text-end"><?php if ($rec['customers']) echo number_format($rec['customers']) ?></td>
                                                <td class="text-end"><?php if ($rec['regular_jo']) echo number_format($rec['regular_jo']) ?></td>
                                                <td class="text-end"><?php if ($rec['student_jo']) echo number_format($rec['student_jo']) ?></td>
                                                <td class="text-end"><?php if ($rec['express_jo']) echo number_format($rec['express_jo']) ?></td>
                                                <td class="text-end"><?php if ($rec['total_loads']) echo number_format($rec['total_loads']) ?></td>
                                                <td class="text-end"><?php if ($rec['total_jo']) echo number_format($rec['total_jo']) ?></td>
                                                <td class="text-end"><?php if ($rec['total_amount']) echo '₱ ' . number_format($rec['total_amount'], 2)  ?></td>
                                            </tr>
                                        <?php
                                            $tot_customer += $rec['customers'];
                                            $tot_regular  += $rec['regular_jo'];
                                            $tot_student  += $rec['student_jo'];
                                            $tot_express  += $rec['express_jo'];
                                            $tot_load     += $rec['total_loads'];
                                            $tot_jo       += $rec['total_jo'];
                                            $tot_amount   += $rec['total_amount'];
                                        } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td class="font-weight-bold">Total</td>
                                            <td class="text-end font-weight-bold"><?php if ($tot_customer) echo number_format($tot_customer) ?></td>
                                            <td class="text-end font-weight-bold"><?php if ($tot_regular) echo number_format($tot_regular)  ?></td>
                                            <td class="text-end font-weight-bold"><?php if ($tot_student) echo number_format($tot_student)  ?></td>
                                            <td class="text-end font-weight-bold"><?php if ($tot_express) echo number_format($tot_express) ?></td>
                                            <td class="text-end font-weight-bold"><?php if ($tot_load) echo number_format($tot_load)  ?></td>
                                            <td class="text-end font-weight-bold"><?php if ($tot_jo) echo number_format($tot_jo) ?></td>
                                            <td class="text-end font-weight-bold"><?php if ($tot_amount) echo '₱ ' . number_format($tot_amount, 2)  ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
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
</script>