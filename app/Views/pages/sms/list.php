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
            <div class="col-12 d-flex justify-content-between">
                <h2 class="module-title"><?php echo $title ?></h2>
                <div class="button">
                    <!-- <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal3">Received List</button> -->
                    <button class="btn btn-primary btn-sm rounded-pill btn-transactions" data-toggle="modal" data-target="#myModal"><i class="mdi mdi-cellphone mdi-transactions"></i> Ready List</button>
                    <?php if ($data['user']['userType'] == 'Admin') { ?>
                        <button class="btn btn-primary btn-sm rounded-pill btn-transactions" data-toggle="modal" data-target="#textblast"><i class="mdi mdi-cellphone mdi-transactions"></i> Text Blast </button>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?php if ($message = session('message')) { ?>
                    <?php $messageType = session('message_type'); ?>
                    <div class="alert alert-dismissible alert-<?php echo $messageType ?> mb-0 text-dark" role="alert">
                        <?php echo $message ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="row justify-content-center px-1">
            <div class="col-12">
                <form action="<?php echo $controller_page ?>" method="POST" id="frmFilter">
                    <input type="hidden" id="sortby" name="sortby" value="<?php echo $sortby ?>">
                    <input type="hidden" id="sortorder" name="sortorder" value="<?php echo $sortorder ?>">
                    <div class="card">
                        <div class="card-header p-1 d-flex justify-content-between">
                            <div class="d-flex">
                                <span class="mt-1">Date: </span>
                                <input type="date" name="startDate" id="startDate" class="form-control form-control-sm form-control-transactions flatpickr-input" value="<?php echo date('Y-m-d', strtotime($startDate)) ?>" style="width:100px">
                                <span class="mx-2 m-1">-</span>
                                <input type="date" name="endDate" id="endDate" class="form-control form-control-sm form-control-transactions flatpickr-input" value="<?php echo date('Y-m-d', strtotime($endDate)) ?>" style="width:100px">
                            </div>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary btn-sm me-3 rounded-pill mx-1 btn-transactions" id="filter"><i class="mdi mdi-filter mdi-transactions"></i> Filter </button>
                                <button type="button" class="btn btn-primary btn-sm rounded-pill btn-transactions" id="clear"><i class="mdi mdi-window-close mdi-transactions"></i>Clear</button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-collapsable table-sm table-transactions" id="myTable">
                                    <thead>
                                        <tr class="header-blue">
                                            <?php
                                            $headers = array(
                                                array('column_header' => 'DATE', 'column_field' => 'dateSent', 'width' => 'wx-150', 'align' => 'center'),
                                                array('column_header' => 'CUSTOMER', 'column_field' => 'customer', 'width' => 'wx-200', 'align' => 'center'),
                                                array('column_header' => 'MOBILE', 'column_field' => 'mobile', 'width' => 'wx-100', 'align' => 'center'),
                                                array('column_header' => 'MESSAGE', 'column_field' => 'message', 'width' => 'wx-150', 'align' => 'center'),
                                                array('column_header' => 'SMS STATUS', 'column_field' => 'status', 'width' => 'wx-80', 'align' => 'center'),
                                                array('column_header' => 'JO STATUS', 'column_field' => 'jostatus', 'width' => 'wx-80', 'align' => 'center'),
                                                array('column_header' => 'RESPONSE', 'column_field' => 'response', 'width' => 'wx-100', 'align' => 'center'),
                                            );

                                            echo $HtmlHelper->tabular_header($headers, $sortby, $sortorder);
                                            ?>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th><input type="text" class="form-control form-control-sm form-control-transactions" name="customer" id="customer" value="<?php echo $customer ?>" style="width:135px"></th>
                                            <th><input type="text" class="form-control form-control-sm form-control-transactions" name="mobile" id="mobile" value="<?php echo $mobile ?>" style="width:135px"></th>
                                            <th></th>
                                            <th>
                                                <select name="status" id="status" class="form-control form-control-sm form-control-transactions select2-default">
                                                    <option value="">---</option>
                                                    <option value="success" <?php if ($status == 'success') echo 'selected' ?>>success</option>
                                                    <option value="failed" <?php if ($status == 'failed') echo 'selected' ?>>failed</option>
                                                </select>
                                            </th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($records) { ?>
                                            <?php foreach ($records as $rec) { ?>
                                                <?php
                                                $status = false;
                                                $badge  = 'danger';
                                                if (trim($rec->status) == "success") {
                                                    $badge = 'success';
                                                    $status = true;
                                                }
                                                ?>
                                                <tr <?php if (!$status) echo 'data-toggle="modal" data-target="#myModal2"'; ?>>
                                                    <td title="date" style="text-align: center;"><?php echo date('m/d/Y h:i A', strtotime($rec->dateSent)) ?></td>
                                                    <input type="hidden" name="smsID" value="<?php echo $rec->smsID ?>">
                                                    <input type="hidden" name="transID" value="<?php echo $rec->transID ?>">
                                                    <td title="customer"><?php echo $rec->customer ?></td>
                                                    <td title="mobile"><?php echo $rec->mobile ?></td>
                                                    <td title="message">
                                                        <?php
                                                        $words  = explode(' ',  $rec->message);
                                                        $first  = isset($words[0]) ? $words[0] : '';
                                                        $second = isset($words[1]) ? $words[1] : '';
                                                        $third  = isset($words[2]) ? $words[2] : '';
                                                        echo  $first . ' ' . $second . ' ' . $third . '..'  ?>
                                                    </td>
                                                    <td title="status"><span class="badge badge-<?php echo $badge ?> rounded-pill"><?php echo $rec->status ?></span></td>
                                                    <?php
                                                    $status = "";
                                                    if ($rec->jostatus == 1) {
                                                        $status = "Received";
                                                    } else if ($rec->jostatus == 3) {
                                                        $status = "Wash";
                                                    } else if ($rec->jostatus == 4) {
                                                        $status = "Dry";
                                                    } else if ($rec->jostatus == 5) {
                                                        $status = "Fold";
                                                    } else if ($rec->jostatus == 6) {
                                                        $status = "Ready";
                                                    } else {
                                                        $status = "Released";
                                                    }
                                                    ?>
                                                    <td title="jostatus"><?php echo  $status ?></td>
                                                    <td title="response"><?php echo $rec->response ?></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <tr>
                                                <td colspan="7" class="text-center"><i>No data found</i></td>
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

<form action="<?php echo $controller_page . '/resend' ?>" method="POST" id="frmSaveResend">
    <div class="modal fade" id="myModal2">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header p-1">
                    <h4 class="modal-title">View</h4>
                </div>
                <div class="modal-body p-1">
                    <input type="hidden" name="myID" id="myID" value="">
                    <input type="hidden" name="myTransID" id="myTransID" value="">
                    <table class="table table-borderless table-sm">
                        <tbody>
                            <tr>
                                <td>Customer: </td>
                                <td><input type="text" name="mycustomer" id="mycustomer" class="form-control form-control-sm wx-200" value="" readonly></td>
                            </tr>
                            <tr>
                                <td>Mobile: </td>
                                <td><input type="text" name="mymobile" id="mymobile" class="form-control form-control-sm wx-200" value=""></td>
                            </tr>
                            <tr>
                                <td>Status: </td>
                                <td><input type="text" name="mystatus" id="mystatus" class="form-control form-control-sm wx-200" value="" readonly></span></td>
                            </tr>
                            <tr>
                                <td>JO Status: </td>
                                <td><input type="text" name="mystatus" id="myjostatus" class="form-control form-control-sm wx-200" value="" readonly></span></td>
                            </tr>
                            <tr>
                                <td>Response: </td>
                                <td><input type="text" name="myresponse" id="myresponse" class="form-control form-control-sm wx-200" value="" readonly></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer p-1">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill text-white" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-sm rounded-pill" id="cmdSaveResend">Send</button>
                </div>
            </div>
        </div>
    </div>
</form>

<form action="<?php echo $controller_page . '/saveReady' ?>" method="POST" id="frmSaveReady">
    <div class="modal fade" id="myModal">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Ready List</h4>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                </div>
                <div class="modal-body">
                    <table class="table table-hover table-collapsable table-transactions">
                        <thead>
                            <tr>
                                <td style="font-size: 14px;text-align:center"><input type="checkbox" id="checkAllReady" class="checkAllReady" name="checkAllReady" value="" style=" transform: scale(1.5);"> <label for="checkAllReady">Check All</label></td>
                                <th style="font-size: 14px;text-align:center">No.</th>
                                <th style="font-size: 14px;text-align:center">Customer</th>
                                <th style="font-size: 14px;text-align:center">Mobile</th>
                                <th style="font-size: 14px;text-align:center">JO Status</th>
                                <th style="font-size: 14px;text-align:center">Transac Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($readyList) { ?>
                                <?php
                                $no = 1;
                                foreach ($readyList as $ready) { ?>
                                    <tr class="table-row">
                                        <td style="font-size: 10px; text-align:center; padding: 10px 0 !important;"><input type="checkbox" name="checkAllReady[]" class="checkAllReady" value="<?php echo $ready->transID ?>" style=" transform: scale(1.5);"></td>
                                        <td style="font-size: 10px; padding: 10px 0 !important;text-align:center"><?php echo $no++ ?></td>
                                        <td style="font-size: 10px; padding: 10px 0 !important;text-align:center"><?php echo $ready->customer ?></td>
                                        <td style="font-size: 10px; padding: 10px 0 !important;text-align:center"><?php echo $ready->mobile ?></td>
                                        <?php
                                        $status = "";
                                        if ($ready->status == 6) {
                                            $status = "Ready";
                                        } ?>
                                        <td style="font-size: 10px; padding: 10px 0 !important;text-align:center"><?php echo $status ?></td>
                                        <td style="font-size: 10px; padding: 10px 0 !important; text-align:center"><?php echo date('m/d/Y', strtotime($ready->dateCreated)) ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary  btn-sm" id="cmdSaveReady">Send</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- <form action="<?php echo $controller_page . '/saveReceive' ?>" method="POST" id="frmSaveReceive">
    <div class="modal fade" id="myModal3">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Received List</h4>
                    <div class="buttons d-flex">
                        <button type="button" class="btn btn-primary btn-sm me-2" id="cmdSave3">Send</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <div class="modal-body">
                    <table class="table table-hover table-collapsable table-transactions">
                        <thead>
                            <tr>
                                <td style="font-size: 14px;text-align:center"><input type="checkbox" id="checkAllReceive" class="checkAllReceive" name="checkAllReceive" value="" style=" transform: scale(1.5);"> <label for="checkAllReceive">Check All</label></td>
                                <th style="font-size: 14px;text-align:center">No.</th>
                                <th style="font-size: 14px;text-align:center">Customer</th>
                                <th style="font-size: 14px;text-align:center">Mobile</th>
                                <th style="font-size: 14px;text-align:center">JO Status</th>
                                <th style="font-size: 14px;text-align:center">Transac Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($readyList) { ?>
                                <?php
                                $no = 1;
                                foreach ($receivelist as $ready) { ?>
                                    <tr class="table-row">
                                        <td style="font-size: 10px; text-align:center; padding: 10px 0 !important;"><input type="checkbox" name="checkAllReceive[]" class="checkAllReceive" value="<?php echo $ready->transID ?>" style=" transform: scale(1.5);"></td>
                                        <td style="font-size: 10px; padding: 10px 0 !important;text-align:center"><?php echo $no++ ?></td>
                                        <td style="font-size: 10px; padding: 10px 0 !important;text-align:center"><?php echo $ready->customer ?></td>
                                        <td style="font-size: 10px; padding: 10px 0 !important;text-align:center"><?php echo $ready->mobile ?></td>
                                        <?php
                                        $status = "";
                                        if ($ready->status == 1) {
                                            $status = "Received";
                                        } ?>
                                        <td style="font-size: 10px; padding: 10px 0 !important;text-align:center"><?php echo $status ?></td>
                                        <td style="font-size: 10px; padding: 10px 0 !important; text-align:center"><?php echo date('m/d/Y', strtotime($ready->dateCreated)) ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary  btn-sm" id="cmdSaveReceive">Send</button>
                </div>
            </div>
        </div>
    </div>
</form> -->

<div class="modal fade" id="textblast">
    <form action="<?php echo $controller_page . '/textblast' ?>" method="POST" id="frmEntry">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header p-1">
                    <h4 class="modal-title">Text Blast</h4>
                </div>
                <div class="modal-body p-1">
                    <table class="table table-borderless table-sm table-transactions">
                        <tbody>
                            <tr>
                                <td class="wx-80">Trans Date <span class="text-danger">*</span></td>
                                <td><input type="date" class="form-control form-control-sm form-control-transactions flatpickr-input wx-100" name="dateCreated" id="smsdateCreated" value="" title="Transaction Date" required></td>
                                <td class="wx-80">JO Status <span class="text-danger">*</span></td>
                                <td class="wx-100">
                                    <select name="status" id="smsstatus" class="form-control form-control-sm select2-default" title="Status" required>
                                        <option value="">---</option>
                                        <option value="1">Receive</option>
                                        <option value="3">Wash</option>
                                        <option value="4">Dry</option>
                                        <option value="5">Fold</option>
                                        <option value="6">Ready</option>
                                    </select>
                                </td>
                                <td class="text-right"><button class="btn btn-primary btn-sm rounded-pill" type="button" id="search">Search</button></td>
                            </tr>
                            <td colspan="2">Message <span class="text-danger">*</span></td>
                            </tr>
                            <tr>
                                <td colspan="5">
                                    <textarea name="message" id="message" cols="100" rows="5" class="form-control form-control-sm" title="Message" required></textarea>
                                    <div class="text-footer d-flex justify-content-between">
                                        <p>Character Count <span id="charCount"></span></p>
                                        <p>Message Count <span id="messCount"></span></p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="text-center"> <!-- Add a container with text-center class -->
                        <table class="table table-hover table-bordered table-transactions" id="searchTable" style="display: none;">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>CUSTOMER</th>
                                    <th>MOBILE</th>
                                </tr>
                            </thead>
                            <tbody id="searchTableBody">
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer d-flex justify-content-between p-1">
                    <button type="button" class="btn btn-secondary btn-sm text-white rounded-pill" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-sm rounded-pill" id="cmdSaveTextblast" disabled>Send</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(".select2-default").select2({
        minimumResultsForSearch: -1,
    });

    flatpickr('.flatpickr-input', {});

    $(document).ready(function() {
        $('#search').on('click', function() {
            var dateCreated = $('#smsdateCreated').val();
            var status = $('#smsstatus').find(":selected").val();

            if (dateCreated != "" && status != "") {
                $.ajax({
                    type: "GET",
                    url: "<?php echo site_url('getsms/') ?>" + status + '/' + dateCreated,
                    dataType: "json",
                    success: function(response) {
                        if (response.length != 0) {
                            $('#searchTable').css('display', 'table'); // Change 'inline' to 'table'
                            $('#cmdSaveTextblast').attr('disabled', false);
                            var h = "";
                            var count = 1;
                            for (var key in response) {
                                if (response.hasOwnProperty(key)) {
                                    h += `<tr>
                                    <td>${count ++}</td>
                                    <td>${response[key].customer}</td>
                                    <td>${response[key].mobile}</td>
                                </tr>`;
                                }
                            }

                            $('#searchTableBody').html(h);
                        } else {
                            Swal.fire({
                                title: 'No Data',
                                text: 'No data found',
                                icon: 'warning',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Ok',
                            });

                            $('#searchTable').css('display', 'none');
                            $('#cmdSaveTextblast').attr('disabled', true);
                        }
                    }
                });
            } else {
                $('#cmdSaveTextblast').attr('disabled', true);

                Swal.fire({
                    title: 'Required Fields',
                    icon: 'warning',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok',
                });
            }
        });

        function check_fields(frm) {
            var valid = true;
            var req_fields = "";

            $('#' + frm + ' [required]').each(function() {
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
                })
            }

            return valid;
        }

        $('#message').on('input', function() {
            // Get the current character count
            var charCount = $(this).val().length;
            var messCount = Math.ceil(charCount / 160);

            if (charCount > 0 && charCount <= 160) {
                messCount = 1;
            }

            // Update the character count display
            $('#charCount').text(charCount);
            // Update the message count display
            $('#messCount').text(messCount);

        });

        $('#cmdSaveResend').on('click', function() {
            if (check_fields('frmSaveResend')) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Click yes to send sms',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#007bff', // Blue color for OK button
                    cancelButtonColor: '#6c757d', // Grey color for Cancel button
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Your logic when the OK button is clicked
                        $('#frmSaveResend').submit();

                        Swal.fire({
                            title: 'Sending SMS',
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
                    }
                });
            }
        });

        $('#cmdSaveTextblast').on('click', function() {
            if (check_fields()) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Click yes to send sms',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#007bff', // Blue color for OK button
                    cancelButtonColor: '#6c757d', // Grey color for Cancel button
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Your logic when the OK button is clicked
                        $('#frmEntry').submit();
                        Swal.fire({
                            title: 'Sending SMS',
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
                    }
                });
            }
        });

        $('#cmdSaveReady').on('click', function() {
            var checked = $(".checkAllReady:checked").length > 0;
            if (checked) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Click yes to send sms',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#007bff', // Blue color for OK button
                    cancelButtonColor: '#6c757d', // Grey color for Cancel button
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Your logic when the OK button is clicked
                        $('#frmSaveReady').submit();
                        Swal.fire({
                            title: 'Sending SMS',
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
                    }
                });
            } else {
                Swal.fire({
                    title: 'Cannot save changes.',
                    text: 'Need to check some checkboxes',
                    icon: 'warning',
                    confirmButtonColor: '#007bff', // Blue color for OK button
                    confirmButtonText: 'Ok',
                })
            }
        });

        // $('#cmdSaveReceive').on('click', function() {
        //     var checked = $(".myCheck2:checked").length > 0;
        //     if (checked) {
        //         Swal.fire({
        //             title: 'Are you sure?',
        //             text: 'Click yes to send sms',
        //             icon: 'warning',
        //             showCancelButton: true,
        //             confirmButtonColor: '#007bff', // Blue color for OK button
        //             cancelButtonColor: '#6c757d', // Grey color for Cancel button
        //             confirmButtonText: 'Yes',
        //             cancelButtonText: 'No'
        //         }).then((result) => {
        //             if (result.isConfirmed) {
        //                 // Your logic when the OK button is clicked
        //                 $('#frmSaveReceive').submit();
        //                 Swal.fire({
        //                     title: 'Sending SMS',
        //                     html: 'It will take a while, please wait',
        //                     allowOutsideClick: false,
        //                     didOpen: () => {
        //                         Swal.showLoading()
        //                     },
        //                 }).then((result) => {
        //                     if (result.dismiss === Swal.DismissReason.timer) {
        //                         console.log('Authentication timed out');
        //                     } else if (result.dismiss === Swal.DismissReason.backdrop) {
        //                         console.log('Authentication canceled');
        //                     }
        //                 });
        //             }
        //         });
        //     } else {
        //         Swal.fire({
        //             title: 'Cannot save changes.',
        //             text: 'Need to check some checkboxes',
        //             icon: 'warning',
        //             confirmButtonColor: '#007bff', // Blue color for OK button
        //             confirmButtonText: 'Ok',
        //         })
        //     }
        // });

        $('#checkAllReady').on('click', function() {
            var isCheckAllChecked = $(this).prop('checked');
            if (isCheckAllChecked) {
                $('.checkAllReady').prop('checked', true);
            } else {
                $('.checkAllReady').prop('checked', false);
            }
        });


        // $('#checkAllReceive').on('click', function() {
        //     var isCheckAllChecked = $(this).prop('checked');
        //     if (isCheckAllChecked) {
        //         $('.checkAllReceive').prop('checked', true);
        //     } else {
        //         $('.checkAllReceive').prop('checked', false);
        //     }
        // });

        $(".table-row").click(function() {
            // Find the nearest checkbox within the clicked row
            var checkbox = $(this).find(".myCheck");
            // Check the checkbox
            checkbox.prop("checked", !checkbox.prop("checked"));
        });

        $('#myTable tbody tr').on('click', function() {
            var row = $(this);
            var myID = row.find("input[name='smsID']").val();
            var transID = row.find("input[name='transID']").val();
            $('#myID').val(myID);
            $('#myTransID').val(transID);

            var columnHeaders = ['date', 'customer', 'mobile', 'message', 'status', 'jostatus', 'response'];
            // Get the values of all td elements in the row
            row.find('td').each(function(index) {
                var columnHeader = columnHeaders[index];
                var cellValue = $(this).text();

                // Check if the target element is an input field or a span
                var $targetElement = $('#my' + columnHeader);
                if ($targetElement.is('input')) {
                    // If it's an input, set the value
                    $targetElement.val(cellValue);
                } else if ($targetElement.is('span')) {
                    // If it's a span, set the text content
                    $targetElement.text(cellValue);
                }
            });
        });
    });
</script>