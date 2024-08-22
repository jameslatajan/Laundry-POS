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
            <div class="col-2 text-start d-flex">
                <h2 class="module-title"><?php echo $title ?></h2>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <form action="<?php echo site_url('unpaid') ?>" method="POST" id="frmFilter">
                    <input type="hidden" id="sortby" name="sortby" value="<?php echo $sortby ?>">
                    <input type="hidden" id="sortorder" name="sortorder" value="<?php echo $sortorder ?>">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between p-1">
                            <div class="date d-flex">
                                <span class="mt-1">Date: </span>
                                <input type="date" name="startDate" id="startDate" class="form-control form-control-sm form-control-transactions flatpickr-input" value="<?php echo date('Y-m-d', strtotime($startDate)) ?>" style="width:100px">
                                <span class="ml-2 mr-2 mt-1">-</span>
                                <input type="date" name="endDate" id="endDate" class="form-control form-control-sm form-control-transactions flatpickr-input" value="<?php echo date('Y-m-d', strtotime($endDate)) ?>" style="width:100px">
                            </div>
                            <div class="button">
                                <button class="btn btn-primary btn-sm rounded-pill btn-transactions" id="filter"><i class="mdi mdi-filter"></i> Filter </button>
                                <button class="btn btn-primary btn-sm rounded-pill btn-transactions" id="clear"><i class="mdi mdi-window-close"></i> Clear</button>
                                <?php if ($data['user']['userType'] == 'Admin') { ?>
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill btn-transactions" id="printlist"><i class="mdi mdi-printer"></i> Print</button>
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill btn-transactions" id="exportlist"><i class="mdi mdi-file-excel"></i> Export</button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-collapsable table-transactions table-sm" id="myTable">
                                    <thead>
                                        <tr class="header-blue">
                                            <?php
                                            $headers = array(
                                                array('column_header' => 'DATE', 'column_field' => 'dateCreated', 'width' => 'wx-150', 'align' => 'center'),
                                                array('column_header' => 'SERIES No.', 'column_field' => 'transID', 'width' => 'wx-150', 'align' => 'center'),
                                                array('column_header' => 'CUSTOMER', 'column_field' => 'customer', 'width' => 'wx-200', 'align' => 'center'),
                                                array('column_header' => 'MOBILE', 'column_field' => 'mobile', 'width' => 'wx-200', 'align' => 'center'),
                                                array('column_header' => 'AMT PAID', 'column_field' => 'amountPaid', 'width' => 'wx-150', 'align' => 'center'),
                                                array('column_header' => 'BAL', 'column_field' => 'balance', 'width' => 'wx-150', 'align' => 'center'),
                                                array('column_header' => 'AMT DUE', 'column_field' => 'totalAmount', 'width' => 'wx-200', 'align' => 'center'),
                                            );

                                            echo $HtmlHelper->tabular_header($headers, $sortby, $sortorder);
                                            ?>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th><input type="text" class="form-control form-control-sm form-control-transactions" name="transID" id="transID" value="<?php echo $transID ?>"></th>
                                            <th><input type="text" class="form-control form-control-sm form-control-transactions" name="customer" id="customer" value="<?php echo $customer ?>" style="width: 135px;"></th>
                                            <th><input type="text" class="form-control form-control-sm form-control-transactions" name="mobile" id="mobile" value="<?php echo $mobile ?>" style="width: 135px;"></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($records) {
                                            $totalUnpaid = 0;
                                            $totalAmtDue = 0;
                                            $totalAmtPaid = 0;
                                        ?>
                                            <?php foreach ($records as $rec) { ?>
                                                <tr onclick="view('<?php echo $rec->qrCode ?>')">
                                                    <td><?php echo date('m/d/Y', strtotime($rec->dateCreated)) ?></td>
                                                    <td><?php echo str_pad($rec->transID, 4, "0", STR_PAD_LEFT) ?></td>
                                                    <td><?php echo strtoupper($rec->customer) ?></td>
                                                    <td><?php echo $rec->mobile ?></td>
                                                    <td><?php echo number_format($rec->amountPaid, 2) ?></td>
                                                    <td><?php echo number_format($rec->balance, 2) ?></td>
                                                    <td><?php echo number_format($rec->totalAmount, 2) ?></td>
                                                </tr>
                                            <?php
                                                $totalUnpaid  += $rec->balance;
                                                $totalAmtPaid += $rec->amountPaid;
                                                $totalAmtDue  += $rec->totalAmount;
                                            } ?>

                                            <tr>
                                                <td colspan="4" style="text-align: end; font-weight:bold">GRAND TOTAL</td>
                                                <td style="font-weight: bold;"><?php echo number_format($totalAmtPaid, 2) ?></td>
                                                <td style="font-weight: bold;"><?php echo number_format($totalUnpaid, 2) ?></td>
                                                <td style="font-weight: bold;"><?php echo number_format($totalAmtDue, 2) ?></td>
                                            </tr>
                                        <?php } else { ?>
                                            <tr>
                                                <td style="text-align:center;" colspan="7"><i>No data found</i> </td>
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

    // function view(id) {
    //     return window.location.href = "<?php echo site_url('transaction/') ?>" + id
    // }

    $(document).ready(function() {
        $('#perPage').on('change', function() {
            let perPage = $('#perPage').find(':selected').val();
            $.ajax({
                type: "GET",
                url: "unpaid?perPage=" + perPage,
                dataType: "JSON",
                complete: function() {
                    window.location.href = "unpaid?perPage=" + perPage;
                },
            });
        });

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
    });
</script>