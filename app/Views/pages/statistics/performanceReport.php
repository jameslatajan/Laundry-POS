<style>
    .table-transactions td,
    th {
        padding-top: 5px !important;
        padding-bottom: 5px !important;
        padding-top: 5px !important;
        padding-left: 5px !important;
        padding-right: 5px !important;
        font-size: 15px !important;
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
                        <form action="<?php echo site_url('/statistics/performanceReport') ?>" method="POST" id="frmFilter" class="d-flex button w-50">
                            <h6 class="mt-2 mr-2">DATE: </h6>
                            <input type="date" name="date" class="form-control form-control-sm form-control-transactions flatpickr-input py-3 wx-100" value="<?php echo date('Y-m-d', strtotime($date)) ?>" style="width: 150px;">
                            <button type="button" class="btn btn-primary btn-sm ms-2 rounded-pill ml-1" id="filter">Filter</button>
                        </form>
                    </div>
                    <?php if ($records) { ?>
                        <div class="card-body p-0">
                            <table class="table table-sm table-bordered table-transaction">
                                <thead class="header-blue">
                                    <tr>
                                        <th class="wx-100">Time</th>
                                        <th class="wx-100">Received</th>
                                        <th class="wx-100">Wash</th>
                                        <th class="wx-100">Dry</th>
                                        <th class="wx-100">Fold</th>
                                        <th class="wx-100">Released</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $totalRecieve = 0;
                                    $totalWash    = 0;
                                    $totalDry     = 0;
                                    $totalFold    = 0;
                                    $totalRelease = 0;
                                    foreach ($timelist as $time) { ?>
                                        <tr>
                                            <td style="font-size: 15px"><?php echo $time ?></td>
                                            <?php foreach ($records as $rec) { ?>
                                                <?php if (strcasecmp(trim($rec['time']), trim($time)) == 0) { ?>
                                                    <td><?php if ($rec['recieve']) echo number_format($rec['recieve'])  ?></td>
                                                    <td><?php if ($rec['wash']) echo number_format($rec['wash'])   ?></td>
                                                    <td><?php if ($rec['dry']) echo number_format($rec['dry'])  ?></td>
                                                    <td><?php if ($rec['fold']) echo number_format($rec['fold'])  ?></td>
                                                    <td><?php if ($rec['release']) echo number_format($rec['release'])  ?></td>
                                                <?php
                                                    $totalRecieve += $rec['recieve'];
                                                    $totalWash    += $rec['wash'];
                                                    $totalDry     += $rec['dry'];
                                                    $totalFold    += $rec['fold'];
                                                    $totalRelease += $rec['release'];
                                                } ?>
                                            <?php } ?>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td class="font-weight-bold">Total</td>
                                        <td class="font-weight-bold"><?php if ($totalRecieve) echo number_format($totalRecieve) ?></td>
                                        <td class="font-weight-bold"><?php if ($totalWash)  echo number_format($totalWash)  ?></td>
                                        <td class="font-weight-bold"><?php if ($totalDry) echo number_format($totalDry)  ?></td>
                                        <td class="font-weight-bold"><?php if ($totalFold) echo number_format($totalFold) ?></td>
                                        <td class="font-weight-bold"><?php if ($totalRelease) echo number_format($totalRelease)  ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    flatpickr('.flatpickr-input', {});

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