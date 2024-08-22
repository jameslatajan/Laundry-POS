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
            <div class="col-12 d-flex">
                <h2 class="module-title"><?php echo $title ?></h2>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12">
                <form action="<?php echo $controller_page ?>" method="POST" id="frmFilter">
                    <input type="hidden" id="sortby" name="sortby" value="<?php echo $sortby ?>">
                    <input type="hidden" id="sortorder" name="sortorder" value="<?php echo $sortorder ?>">
                    <div class="card">
                        <div class="card-header p-1">
                            <div class="d-flex justify-content-between">
                                <div class="date d-flex">
                                    <span class="mt-1">Date: </span>
                                    <input type="date" name="startDate" id="startDate" class="form-control form-control-sm form-control-transactions flatpickr-input" value="<?php echo date('Y-m-d', strtotime($startDate)) ?>" style="width:100px">
                                    <span class="ml-2 mr-2 mt-1">-</span>
                                    <input type="date" name="endDate" id="endDate" class="form-control form-control-sm form-control-transactions flatpickr-input" value="<?php echo date('Y-m-d', strtotime($endDate)) ?>" style="width:100px">
                                </div>
                                <div class="buttons">
                                    <button class="btn btn-primary btn-sm rounded-pill btn-transactions" id="filter"><i class="mdi mdi-filter mdi-transactions"></i> Filter </button>
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill btn-transactions" id="clear"><i class="mdi mdi-window-close mdi-transactions"></i>Clear</button>
                                    <?php if ($data['user']['userType'] == 'Admin') { ?>
                                        <button type="button" class="btn btn-primary btn-sm rounded-pill btn-transactions" id="exportlist"><i class="mdi mdi-file-excel mdi-transactions"></i> Export</button>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-transactions" id="myTable" style="width: 120%;">
                                    <thead>
                                        <tr class="header-blue">
                                            <?php
                                            $headers = array(
                                                array('column_header' => 'DATE', 'column_field' => 'dateCreated', 'width' => 'wx-80', 'align' => 'center'),
                                                array('column_header' => 'SERIES NO.', 'column_field' => 'transID', 'width' => 'wx-80 ', 'align' => 'center'),
                                                array('column_header' => 'CUSTOMER', 'column_field' => 'customer', 'width' => 'wx-200 ', 'align' => 'center'),
                                                array('column_header' => 'MOBILE', 'column_field' => 'mobile', 'width' => 'wx-100 ', 'align' => 'center'),
                                                array('column_header' => 'CLOTH', 'column_field' => 'kiloQty', 'width' => 'wx-20 ', 'align' => 'center'),
                                                array('column_header' => 'COMF', 'column_field' => 'comforterLoad', 'width' => 'wx-20 ', 'align' => 'center'),
                                                array('column_header' => 'CASH', 'column_field' => 'cash', 'width' => 'wx-50 ', 'align' => 'center'),
                                                array('column_header' => 'GCASH', 'column_field' => 'gcash', 'width' => 'wx-50 ', 'align' => 'center'),
                                                array('column_header' => 'PAID', 'column_field' => 'amountPaid', 'width' => 'wx-50 ', 'align' => 'center'),
                                                array('column_header' => 'UNPAID', 'column_field' => 'balance', 'width' => 'wx-50 ', 'align' => 'center'),
                                                array('column_header' => 'AMT DUE', 'column_field' => 'totalAmount', 'width' => 'wx-50', 'align' => 'center'),
                                                array('column_header' => 'P. MTHD', 'column_field' => 'paymentMethod', 'width' => 'wx-80 ', 'align' => 'center'),
                                                array('column_header' => 'STATUS', 'column_field' => 'status', 'width' => 'wx-100 ', 'align' => 'center'),
                                                array('column_header' => 'CASHIER', 'column_field' => 'userID', 'width' => 'wx-100 ', 'align' => 'center'),
                                            );

                                            echo $HtmlHelper->tabular_header($headers, $sortby, $sortorder);
                                            ?>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th><input type="text" class="form-control form-control-sm form-control-transactions" name="transID" id="transID" value="<?php if ($transID) echo str_pad($transID, 2) ?>"></th>
                                            <th><input type="text" class="form-control form-control-sm form-control-transactions" name="customer" id="customer" value="<?php echo $customer ?>"></th>
                                            <th><input type="text" class="form-control form-control-sm form-control-transactions" name="mobile" id="mobile" value="<?php echo $mobile ?>"></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th>
                                                <select name="paymentMethod" id="paymentMethod" class="form-control form-control-sm form-control-transactions select2-default">
                                                    <option value="">---</option>
                                                    <option value="Cash" <?php if ($paymentMethod == 'Cash') echo 'selected' ?>>CASH</option>
                                                    <option value="Gcash" <?php if ($paymentMethod == 'Gcash') echo 'selected' ?>>GCASH</option>
                                                    <option value="Cash/Gcash" <?php if ($paymentMethod == 'Cash/Gcash') echo 'selected' ?>>C/GC</option>
                                                </select>
                                            </th>
                                            <?php
                                            $statusList  = array(
                                                1 => 'Received',
                                                3 => 'Wash',
                                                4 => 'Dry',
                                                5 => 'Fold',
                                                6 => 'Ready',
                                                7 => 'Released',
                                                0 => 'Cancelled',
                                            )
                                            ?>
                                            <th>
                                                <select name="status" id="status" class="form-control form-control-sm form-control-transactions select2-default">
                                                    <option value="">---</option>
                                                    <?php foreach ($statusList as $stat => $value) { ?>
                                                        <option value="<?php echo $stat ?>" <?php if ($stat == $status) echo 'selected' ?>><?php echo $value ?></option>
                                                    <?php  } ?>
                                                </select>
                                            </th>
                                            <th>
                                                <select name="userID" id="userID" class="form-control form-control-sm form-control-transactions select2-default">
                                                    <option value="">---</option>
                                                    <?php foreach ($cashiers as $cas) { ?>
                                                        <option value="<?php echo $cas->userID ?>" <?php if ($cas->userID == $userID) echo 'selected' ?>><?php echo $cas->username ?></option>
                                                    <?php } ?>
                                                </select>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($records) {
                                            $totalCash   = 0;
                                            $totalGcash  = 0;
                                            $totalUnpaid = 0;
                                            $totalAmtDue = 0;
                                            $totalAmtPaid = 0; ?>
                                            <?php foreach ($records as $rec) { ?>
                                                <?php
                                                $color = '';
                                                if ($rec->status == 0) {
                                                    $color = 'danger';
                                                }

                                                if ($rec->status == 7) {
                                                    $color = 'success';
                                                }

                                                if ($rec->status < 7 && $rec->status > 0) {
                                                    $color = 'info';
                                                }
                                                ?>

                                                <tr onclick="view('<?php echo $rec->qrCode ?>')">
                                                    <td><a href="<?php echo $controller_page . '/view/' . $rec->qrCode ?>" class="text-dark"><?php echo date('m/d/Y', strtotime($rec->dateCreated)) ?></a></td>
                                                    <td><a href="<?php echo $controller_page . '/view/' . $rec->qrCode ?>" class="text-dark"><?php echo str_pad($rec->transID, 2) ?></a></td>
                                                    <td><a href="<?php echo $controller_page . '/view/' . $rec->qrCode ?>" class="text-dark"><?php echo strtoupper($rec->customer) ?></a></td>
                                                    <td><a href="<?php echo $controller_page . '/view/' . $rec->qrCode ?>" class="text-dark"><?php echo $rec->mobile ?></a></td>
                                                    <td><?php echo $rec->kiloQty ?></td>
                                                    <td><?php echo $rec->comforterLoad ?></td>
                                                    <td>
                                                        <?php
                                                        $cash = $rec->cash + $rec->payment1Cash + $rec->payment2Cash;
                                                        echo number_format($cash, 2, '.', ',');
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $gCash = $rec->gCash + $rec->payment1Gcash + $rec->payment2Gcash;
                                                        echo number_format($gCash, 2, '.', ',');
                                                        ?>
                                                    </td>
                                                    <td><?php echo number_format($rec->amountPaid, 2) ?></td>
                                                    <td><?php echo number_format($rec->balance, 2) ?></td>
                                                    <td><?php echo number_format($rec->totalAmount, 2) ?></td>
                                                    <td><?php echo $rec->paymentMethod ?></td>
                                                    <td><span class="badge badge-pill badge-<?php echo $color ?>"><?php echo $statusList[$rec->status] ?></span>
                                                    <td><?php echo $rec->username ?></td>
                                                </tr>
                                            <?php
                                                $totalCash    += $cash;
                                                $totalGcash   += $gCash;
                                                $totalUnpaid  += $rec->balance;
                                                $totalAmtPaid += $rec->amountPaid;
                                                $totalAmtDue  += $rec->totalAmount;
                                            } ?>
                                            <tr>
                                                <td colspan="3"></td>
                                                <td colspan="3" class="text-right"><strong>GRAND TOTAL</strong></td>
                                                <td><strong><?php echo number_format($totalCash, 2) ?></strong></td>
                                                <td><strong><?php echo number_format($totalGcash, 2) ?></strong></td>
                                                <td><strong><?php echo number_format($totalAmtPaid, 2) ?></strong></td>
                                                <td><strong><?php echo number_format($totalUnpaid, 2) ?></strong></td>
                                                <td><strong><?php echo number_format($totalAmtDue, 2) ?></strong></td>
                                                <td colspan="3"></td>
                                            </tr>
                                        <?php } else { ?>
                                            <tr>
                                                <td colspan="14" class="text-center"><i>No data found</i></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer p-1">
                            <div class="d-flex justify-content-between">
                                <?php echo $pagination; ?>
                                <div class="limit">
                                    <?php if (isset($limit)) { ?>
                                        <!-- Pagination Details -->
                                        <div class="limit-details d-flex">
                                            <div class="range wx-50">
                                                <select class="form-control form-control-sm form-control-transactions select2-default text-center" id="limit" name="limit">
                                                    <?php for ($i = 10; $i <= 200; $i *= 2) { ?>
                                                        <option value="<?php echo $i ?>" <?php if ($limit == $i)  echo "selected"; ?>><?php echo $i ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="fw-light fs-italic text-muted text-end ml-2">
                                                <?php $display = min($offset + $limit, $ttl_rows); ?>
                                                <small class="dataTables_info">Displaying <?php echo $offset + 1; ?> - <?php echo $display; ?> of <?php echo number_format($ttl_rows, 0); ?> records</small>
                                            </div>
                                            <!-- End of Pagination Details -->
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(".select2-default").select2({
        minimumResultsForSearch: -1,
    });

    flatpickr('.flatpickr-input', {});

    function view(id) {
        return window.location.href = "<?php echo $controller_page . '/view/' ?>" + id
    }

    $('#exportlist').on('click', function() {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you wish to export list?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?php echo $controller_page . '/exportlist' ?>";
            }
        })
    });

    $('#printlist').on('click', function() {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you wish to print list?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirm'
        }).then((result) => {
            if (result.isConfirmed) {
                var width = 800;
                var height = 800;
                var left = 400;
                var top = (window.innerHeight) - (height);
                var options = "width=" + width + ",height=" + height + ",top=" + top + ",left=" + left + ",resizable=0, fullscreen=0";
                var popup1 = window.open('<?php echo $controller_page . '/printlist' ?>', "Popup", options);
                // Check if Popup1 is closed and activate Popup2
                var checkPopup1Status = function() {
                    if (popup1.closed) {
                        clearInterval(popup1CheckInterval); // Stop checking
                        $('#printlist').attr('disabled', false);
                    }
                };

                var popup1CheckInterval = setInterval(checkPopup1Status, 500);
                return false; // Prevents the default behavior of the <a> tag
            }
        })
    });
</script>