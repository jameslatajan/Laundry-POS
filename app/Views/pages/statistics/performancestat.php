<style>
    .sticky-body {
        position: sticky;
        left: 0;
        z-index: 2;
        border: 1px solid black;
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
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header p-1">
                        <form action="<?php echo site_url('/statistics/performacestat') ?>" method="POST" id="frmFilter" class="d-flex w-25">
                            <h6 class="mt-2 mr-2">YEAR: </h6>
                            <select name="year" id="year" class="selectpicker form-control form-control-sm form-control-transactions select2-default mr-1">
                                <option value="">---</option>
                                <?php foreach (YEARS as $yr) { ?>
                                    <option value="<?php echo $yr ?>" <?php if ($yr == $year) echo 'selected' ?>><?php echo $yr ?></option>
                                <?php } ?>
                            </select>
                            <button type="button" class="btn btn-primary btn-sm btn-transactions rounded-pill ml-1" id="filter">FILTER</button>
                        </form>
                    </div>
                    <?php if ($personel) { ?>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-transactions table-bordered">
                                    <thead class="bg-blue">
                                        <tr>
                                            <th rowspan="2" class="sticky-body bg-blue">PERSONNEL</th>
                                            <?php
                                            // Dynamically generate month names based on the current year
                                            $months = array(
                                                'JANUARY',
                                                'FEBRUARY',
                                                'MARCH',
                                                'APRIL',
                                                'MAY',
                                                'JUNE',
                                                'JULY',
                                                'AUGUST',
                                                'SEPTEMBER',
                                                'OCTOBER',
                                                'NOVEMBER',
                                                'DECEMBER'
                                            );
                                            ?>
                                            <?php foreach ($months as $monthName) { ?>
                                                <th colspan="3" class="text-center"><?php echo $monthName ?> </th>
                                            <?php } ?>
                                            <th colspan="3" class="text-center  ">TOTAL</th>
                                        </tr>
                                        <tr>
                                            <?php foreach ($months as $monthName) { ?>
                                                <th>WASH</th>
                                                <th>DRY</th>
                                                <th>FOLD</th>
                                            <?php } ?>
                                            <th>WASH</th>
                                            <th>DRY</th>
                                            <th>FOLD</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($personel as $person => $value) { ?>
                                            <tr>
                                                <td class="sticky-body bg-blue"><?php echo $person ?></td>
                                                <?php
                                                $totalWash = 0;
                                                $totalDry  = 0;
                                                $totalFold = 0;
                                                foreach ($value as $val) { ?>
                                                    <td><?php if ($val['wash']) echo number_format($val['wash'])   ?></td>
                                                    <td><?php if ($val['dry']) echo number_format($val['dry'])   ?></td>
                                                    <td><?php if ($val['fold']) echo number_format($val['fold'])  ?></td>
                                                <?php
                                                    $totalWash += $val['wash'];
                                                    $totalDry  += $val['dry'];
                                                    $totalFold += $val['fold'];
                                                } ?>
                                                <td style="font-weight: bold;"><?php if ($totalWash) echo number_format($totalWash) ?></td>
                                                <td style="font-weight: bold;"><?php if ($totalDry) echo number_format($totalDry) ?></td>
                                                <td style="font-weight: bold;"><?php if ($totalFold) echo number_format($totalFold)  ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
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

    var records = <?php echo json_encode($personel) ?>

    $('#filter').on('click', function() {
        $('#frmFilter').submit();

        Swal.fire({
            title: 'Displaying Result',
            html: 'It will take a while, please wait',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
            },
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.timer) {
                console.log('Authentication timed out');
            } else if (result.dismiss === Swal.DismissReason.backdrop) {
                console.log('Authentication canceled');
            }
        });
    });
</script>