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
            <div class="col-md-2 text-start d-flex">
                <h2 class="module-title"><?php echo $title ?></h2>
            </div>
        </div>
        <form action="<?php echo $controller_page . '/save' ?>" method="POST" id="frmEntry">
            <div class="row">
                <div class="col-4 pr-1">
                    <div class="card">
                        <div class="card-header p-1">
                            <h4 class="card-title mb-1">CREATE EXPENSES</h4>
                        </div>
                        <div class="card-body p-0">
                            <?php if ($user->userType == 'Admin') { ?>
                                <div class="col-12 mb-2 mt-2">
                                    <h6>Expenses Date <span class="text-danger">*</span></h6>
                                    <input type="date" name="expDate" id="expDate" class="form-control form-control-md form-control-transactions flatpickr-input" title="Expenses Date" value="<?php echo date('Y-m-d') ?>" required>
                                </div>
                            <?php } ?>
                            <div class="col-12 mb-2">
                                <h6>Particular <span class="text-danger">*</span></h6>
                            </div>
                            <div class="col-12 mb-2">
                                <input type="text" id="particular" name="particular" class="form-control form-control-md form-control-transactions" title="Particular" value="" required>
                            </div>
                            <div class="col-12 mb-2">
                                <h6>Amount <span class="text-danger">*</span></h6>
                            </div>
                            <div class="col-12 mb-2">
                                <input type="text" id="amount" name="amount" class="form-control form-control-md form-control-transactions" title="Amount" value="" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>
                        </div>
                        <div class="card-footer text-right p-1">
                            <button type="button" class="btn btn-primary btn-sm rounded-pill btn-transactions" id="cmdSave">Save</button>
                        </div>
                    </div>
                </div>
        </form>

        <div class="col-8 pl-1">
            <form action="<?php echo $controller_page ?>" method="POST" id="frmFilter">
                <input type="hidden" id="sortby" name="sortby" value="<?php echo $sortby ?>">
                <input type="hidden" id="sortorder" name="sortorder" value="<?php echo $sortorder ?>">
                <div class="card">
                    <div class="card-header d-flex justify-content-between p-1">
                        <div class="d-flex mt-1">
                            <h6 class="mx-1 mt-1">Date: </h6>
                            <input type="date" name="startDate" id="startDate" class="form-control form-control-sm form-control-transactions flatpickr-input wx-100" value="<?php echo date('Y-m-d', strtotime($startDate)) ?>">
                            <span>-</span>
                            <input type="date" name="endDate" id="endDate" class="form-control form-control-sm form-control-transactions flatpickr-input wx-100" value="<?php echo date('Y-m-d', strtotime($endDate)) ?>">
                        </div>
                        <div class="buttons">
                            <button class="btn btn-primary btn-sm rounded-pill btn-transactions" id="filter"><i class="mdi mdi-filter"></i> Filter </button>
                            <button class="btn btn-primary btn-sm rounded-pill btn-transactions" id="clear"><i class="mdi mdi-window-close"></i> Clear</button>
                            <button type="button" class="btn btn-primary btn-sm rounded-pill btn-transactions" id="printlist"><i class="mdi mdi-printer"></i> Print</button>
                            <button type="button" class="btn btn-primary btn-sm rounded-pill btn-transactions" id="exportlist"><i class="mdi mdi-file-excel"></i> Export</button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-collapse table-sm table-transactions" id="myTable">
                                <thead>
                                    <tr class="header-blue">
                                        <?php
                                        $headers = array(
                                            array('column_header' => 'DATE', 'column_field' => 'expDate', 'width' => 'wx-100', 'align' => 'center'),
                                            array('column_header' => 'PARTICULAR', 'column_field' => 'particular', 'width' => 'wx-200', 'align' => 'center'),
                                            array('column_header' => 'AMOUNT', 'column_field' => 'amount', 'width' => 'wx-100', 'align' => 'center'),
                                            array('column_header' => 'DATE CREATED', 'column_field' => 'dateCreated', 'width' => 'wx-80', 'align' => 'center'),
                                            array('column_header' => 'CASHIER', 'column_field' => 'createdBy', 'width' => 'wx-200', 'align' => 'center'),
                                        );

                                        echo $HtmlHelper->tabular_header($headers, $sortby, $sortorder);
                                        ?>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th><input type="text" class="form-control form-control-sm form-control-transactions" name="particular" id="particular" value="<?php echo $particular ?>"></th>
                                        <th><input type="text" class="form-control form-control-sm form-control-transactions" name="amount" id="amount" value="<?php echo $amount ?>"></th>
                                        <th><input type="date" class="form-control form-control-sm form-control-transactions flatpickr-input" name="dateCreated" id="dateCreated" value="<?php echo $dateCreated ?>"></th>
                                        <th>
                                            <select class="form-control form-control-sm form-control-transactions select2-default" name="createdBy" id="createdBy">
                                                <option value=""></option>
                                                <?php foreach ($users as $use) { ?>
                                                    <option value="<?php echo $use->userID ?>" <?php if ($createdBy == $use->userID) echo 'selected' ?>><?php echo $use->username ?></option>
                                                <?php  } ?>
                                            </select>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($records) {
                                        $totalAmt = 0;
                                    ?>
                                        <?php foreach ($records as $rec) { ?>
                                            <tr onclick="view('<?php echo $rec->expID ?>')">
                                                <td><?php echo date('m/d/Y', strtotime($rec->expDate)) ?></td>
                                                <td><?php echo strtoupper($rec->particular) ?></td>
                                                <td><?php echo number_format($rec->amount, 2)  ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($rec->dateCreated))  ?></td>
                                                <td><?php echo $rec->username ?></td>
                                            </tr>
                                        <?php
                                            $totalAmt += $rec->amount;
                                        } ?>
                                        <tr>
                                            <td colspan="2" class="text-right font-weight-bold">GRAND TOTAL</td>
                                            <td class="font-weight-bold"><?php echo number_format($totalAmt, 2) ?></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    <?php } else { ?>
                                        <tr>
                                            <td style="font-size: 10px !important; text-align:center;" colspan="14">No data found</td>
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

<script>
    $(".select2-default").select2({
        minimumResultsForSearch: -1,
    });

    flatpickr('.flatpickr-input', {});

    $(document).ready(function() {
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
                }
            })
        });

        function check_fields() {
            var valid = true;
            var req_fields = "";

            $('#frmEntry [required]').each(function() {
                if ($(this).val() == '') {
                    req_fields += $(this).attr('title') + "<br/>";
                    valid = false;
                }
            })

            if (!valid) {
                Swal.fire({
                    title: 'Required Fields',
                    html: req_fields,
                    icon: 'warning',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok',
                }).then((result) => {

                })
            }
            return valid;
        }

        $('#cmdSave').click(function() {
            if (check_fields()) {
                $('#cmdSave').attr('disabled', true);
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Your are going to save this data',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#frmEntry').submit();
                    } else {
                        $('#cmdSave').attr('disabled', false);
                    }
                })
            }
        });
    });
</script>