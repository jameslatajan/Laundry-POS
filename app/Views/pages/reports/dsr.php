<style>
    .table-sales td {
        font-size: 17px;
        padding: 5px !important;
    }

    input[type=radio] {
        width: 20px;
        height: 1em;
    }

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
        <div class="row mb-1">
            <div class="col-md-8 d-flex">
                <h2 class="module-title"><?php echo $title ?></h2>
            </div>
        </div>
    </div>

    <form action="<?php echo site_url('dsr_admin') ?>" method="POST" class="d-flex justify-content-center">
        <div class="card mb-4">
            <div class="card-header p-1">
                <table class="table table-bordered table-sm">
                    <tbody>
                        <tr>
                            <td class="wx-50">DATE:</td>
                            <td class="wx-120"><input type="date" class="form-control form-control-sm form-control-transactions flatpickr-input" name="sales_date" value="<?php if ($sales_date) echo date('Y-m-d', strtotime($sales_date))  ?>" required></td>
                            <td class="wx-100 text-right">CASHIER: </td>
                            <td class="wx-120">
                                <select name="userID" id="userID" class="form-control selectpicker form-control-sm form-control-transactions select2-default" style="width: 100px;" required>
                                    <option value="">Select</option>
                                    <?php foreach ($cashiers as $cashier) { ?>
                                        <option value="<?php echo $cashier->userID ?>" <?php if ($cashier_id == $cashier->userID)  echo 'selected'; ?>><?php echo $cashier->username ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td><button type="submit" class="btn btn-primary btn-sm rounded-pill">FILTER</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-body px-1 pt-1 pb-2">
                <?php if ($rec) { ?>
                    <div class="container-fluid wx-500">
                        <div class="d-flex justify-content-end mt-2">
                            <button class="btn btn-primary btn-sm rounded-pill" id="save_print">Print</button>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <table class="table table-borderless table-sm table-sales" style="text-align: center;">
                                    <tbody>
                                        <tr>
                                            <td style="font-weight: bold;">LABACHINE LAUNDRY LOUNGE</td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold;">DAILY SALES REPORT</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="table table-borderless table-md table-sales mt-1" style="margin-bottom: 10px;margin-top: 10px;">
                                    <tbody>
                                        <tr style="font-weight:bold; font-size:12px">
                                            <td style="width: 150px;  font-size: 15px">CASHIER: </td>
                                            <td style="text-align: left; font-size: 15px"><?php echo $curr_cashier->username ?></td>
                                            <td></td>
                                        </tr>
                                        <tr style="font-weight:bold; font-size:12px">
                                            <td style=" font-size: 15px">SALES DATE: </td>
                                            <td style="text-align: left; font-size: 15px"><?php echo date('m/d/Y h:i A', strtotime($rec->sales_date)) ?></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <!-- start sales -->
                                <table class="table table-borderless table-md table-sales" style="margin-bottom: 10px;">
                                    <tbody>
                                        <tr style="text-align: center;  border-bottom: 1px dashed black; font-weight:bold">
                                            <td colspan="2"> SALES</td>
                                        </tr>
                                        <tr>
                                            <td>CASH</td>
                                            <td style="text-align: right;"><?php echo number_format($rec->ds_cash, 2)  ?></td>
                                        </tr>
                                        <tr>
                                            <td>GCASH</td>
                                            <td style="text-align: right;"><?php echo number_format($rec->ds_gcash, 2)  ?></td>
                                        </tr>
                                        <tr>
                                            <td>UNPAID</td>
                                            <td style="text-align: right;"><?php echo number_format($rec->ds_unpaid, 2) ?></td>
                                        </tr>
                                        <tr style="border-top: 1px dashed black; border-bottom: 1px dashed black;font-weight:bold">
                                            <td>TOTAL</td>
                                            <td style="text-align: right;"><?php echo number_format($rec->ds_total, 2)  ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <!-- end sales -->

                                <!-- start collection -->
                                <table class="table table-borderless table-md table-sales" style="margin-bottom: 10px;">
                                    <tbody>
                                        <tr style="text-align: center; font-weight:bold; border-bottom: 1px dashed black">
                                            <td colspan="3">COLLECTIONS</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">CASH</td>
                                            <td style="text-align: right;"><?php echo number_format($rec->col_cash, 2)  ?></td>
                                        </tr>
                                        <tr style="border-bottom: 1px dashed black;">
                                            <td colspan="2" style="padding-top:10px !important;">GCASH</td>
                                            <td style="text-align: right;"><?php echo number_format($rec->col_gcash, 2)  ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <!-- end collection -->
                                <!-- start items -->
                                <table class="table table-borderless table-md table-sales" style="margin-bottom: 10px;">
                                    <tbody>
                                        <tr style="text-align: center;  border-bottom: 1px dashed black; font-weight:bold">
                                            <td colspan="2"> ITEMS</td>
                                        </tr>
                                        <tr>
                                            <td>CASH</td>
                                            <td style="text-align: right;"><?php echo number_format($rec->item_cash, 2)  ?></td>
                                        </tr>
                                        <tr>
                                            <td>GCASH</td>
                                            <td style="text-align: right;"><?php echo number_format($rec->item_gcash, 2)  ?></td>
                                        </tr>
                                        <tr style="border-top: 1px dashed black; border-bottom: 1px dashed black;font-weight:bold">
                                            <td>TOTAL</td>
                                            <td style="text-align: right;"><?php echo number_format($rec->item_total, 2)  ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <!-- end items -->

                                <!-- start sales -->
                                <table class="table table-borderless table-md table-sales" style="margin-bottom: 10px;">
                                    <tbody>
                                        <tr style="text-align: center;  border-bottom: 1px dashed black;font-weight:bold">
                                            <td class="hr" colspan="2"> EXPENSES</td>
                                        </tr>
                                        <tr style="border-top: 1px dashed black; border-bottom: 1px dashed black;font-weight:bold">
                                            <td>TOTAL</td>
                                            <td style="text-align: right;"><?php echo number_format($rec->total_expenses, 2) ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <!-- end sales -->

                                <!-- start Summary -->
                                <table class="table table-borderless table-md table-sales" style="margin-bottom: 1px;border-bottom: 1px dashed black;">
                                    <tbody>
                                        <tr style="text-align: center;  border-bottom: 1px dashed black;font-weight:bold">
                                            <td class="hr" colspan="2"> CASH SUMMARY</td>
                                        </tr>
                                        <tr>
                                            <td>CASH</td>
                                            <td style="text-align: right;"><?php echo number_format($rec->total_cash, 2) ?></td>
                                        </tr>
                                        <tr>
                                            <td>GCASH</td>
                                            <td style="text-align: right;"><?php echo number_format($rec->total_gcash, 2) ?></td>
                                        </tr>
                                        <tr style="font-weight:bold;font-size:12px">
                                            <td>ACTUAL CASH</td>
                                            <td style="text-align: right;"><?php echo number_format($rec->remittance, 2) ?></td>
                                        </tr>
                                        <tr style="border-bottom: 1px dashed black;font-weight:bold;font-size:12px">
                                            <td>VARIANCE</td>
                                            <td style="text-align: right;"><?php echo number_format($rec->variance, 2) ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <?php if ($rec->varSettledAmt) { ?>
                                    <table class="table table-borderless table-md table-sales mb-2" style="margin-bottom: 1px;border-bottom: 1px dashed black;">
                                        <tbody>
                                            <tr>
                                                <td style="font-weight:bold;">SETTLED DATE </td>
                                                <td style="text-align: right;font-weight:bold;"><?php echo date('m/d/Y h:i A', strtotime($rec->dateSettled)) ?></td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight:bold;">AMOUNT</td>
                                                <td style="text-align: right;font-weight:bold;"><?php echo number_format($rec->varSettledAmt, 2) ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                <?php } ?>
                            </div>
                            <?php if ($rec->variance && !$rec->varSettledAmt) { ?>
                                <div class="col-12 mt-2 d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill" data-toggle="modal" data-target="#settleModal">SETTLE</button>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } else { ?>
                    <table class="table table-borderless table-sm table-sales" style="text-align: center;">
                        <tbody>
                            <tr>
                                <td style="font-weight: bold;">NO DATA FOUND</td>
                            </tr>
                        </tbody>
                    </table>
                <?php } ?>
            </div>
        </div>
    </form>

</div>

<!-- Modal Settle-->
<form action="<?php echo site_url('dsr_admin/settle') ?>" method="POST" id="frmSettle">
    <div class="modal fade" id="settleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" style="width:494px !important">
            <div class="modal-content">
                <div class="modal-header p-1">
                    <h4 class="modal-title">Settle</h4>
                </div>
                <div class="modal-body p-0">
                    <?php if ($rec) { ?>
                        <input type="hidden" name="dsrID" id="dsrID" value="<?php echo $rec->dsrID ?>">
                    <?php } ?>
                    <table class="table table-borderless table-transactions table-md">
                        <tbody>
                            <tr>
                                <td style="font-size: 20px; width:150px">AMOUNT <span class="text-danger">*</span></td>
                                <td><input type="text" class="form-control form-control-md p-1" style="font-size: 20px;" name="varSettledAmt" id="varSettledAmt" value="" title="Amount" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer d-flex justify-content-between p-1">
                    <button type="button" class="btn btn-secondary btn-sm text-white rounded-pill" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-sm rounded-pill" id="saveSettle">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>



<script>
    $(".select2-default").select2({
        minimumResultsForSearch: -1,
    });

    flatpickr('.flatpickr-input', {});
    $('input[type=number][max]:not([max=""])').on('input', function(ev) {
        var $this = $(this);
        var maxlength = $this.attr('max').length;
        var value = $this.val();
        if (value && value.length >= maxlength) {
            $this.val(value.substr(0, maxlength));
        }
    });

    $('#dsr_print').on('click', function() {
        let url = $('#url').val();
        let dateCreated = $('#dateCreated').val();
        let userID = $('#userID').find(':selected').val();
        window.location.replace(url + dateCreated + '/' + userID)
    });

    function sanitizeInput(event) {
        var input = event.target;
        var value = input.value;

        // Remove any non-numeric characters 
        value = value.replace(/[^0-9]/gi, '');

        // Update the input value with the sanitized value
        input.value = value;
    }

    $('#save_print').on('click', function() {
        $('#save_print').attr('disabled', true);
        if (check_fields()) {
            Swal.fire({
                title: 'Confirm Print?',
                text: 'Are you planning to generate a DSR (Daily Sales Report)? Would you like to proceed?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if (result.isConfirmed) {
                    var width = 20;
                    var height = 20;
                    var left = 0;
                    var top = (window.innerHeight) - (height);
                    var options = "width=" + width + ",height=" + height + ",top=" + top + ",left=" + left + ",resizable=0, fullscreen=0";
                    var popup1 = window.open('<?php echo site_url('dsr_admin/print/') ?>' + '<?php echo $cashier_id; ?>/' + '<?php echo $sales_date; ?>/', "Popup", options);
                    // Check if Popup1 is closed and activate Popup2
                    var checkPopup1Status = function() {
                        if (popup1.closed) {
                            clearInterval(popup1CheckInterval); // Stop checking
                            // window.location.replace('<?php echo site_url('dsr_admin/') ?>')
                            $('#save_print').attr('disabled', false);
                        }
                    };

                    var popup1CheckInterval = setInterval(checkPopup1Status, 500);
                    return false; // Prevents the default behavior of the <a> tag
                }
            })
        }
    });

    function check_fields() {
        var valid = true;
        var req_fields = "";

        $('#frmFilter [required]').each(function() {
            if ($(this).val() == '') {
                req_fields += "<br/>" + $(this).attr('title');
                valid = false;
            }
        })

        Swal.fire({
            title: 'Some fields are empty!',
            html: req_fields ? "Required Fields: " + req_fields : "All fields are filled.",
            icon: req_fields ? 'warning' : 'success',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                if ($('#save_print').length) {
                    $('#save_print').attr('disabled', false);
                }
                $('#save_print').attr('disabled', false);
            }
        });

        return valid;
    }

    $('#clear').on('click', function(e) {
        e.preventDefault();
        $('#dateCreated').val('');
        $('#frm').submit();
    });

    $('#saveSettle').on('click', function() {
        var amount = $('#varSettledAmt').val();
        var isSettle = true;

        // if ($("input[name='dsrID']:checked").length == 0) {
        //     Swal.fire({
        //         title: 'Something went wrong',
        //         text: 'Select variance',
        //         icon: 'warning',
        //         confirmButtonColor: '#3085d6',
        //         cancelButtonColor: '#d33',
        //         confirmButtonText: 'OK'
        //     })

        //     isSettle = false;
        // }

        if (!amount) {
            Swal.fire({
                title: 'Something went wrong',
                text: 'Amount is required',
                icon: 'warning',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'OK'
            })

            isSettle = false;
        }

        if (isSettle) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you wish to proceed?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#frmSettle').submit();
                }
            })
        }

    });

    $('#searchDate').on('change', function() {
        $('#data-table').css('display', 'block');
        $.ajax({
            type: "GET",
            url: '<?php echo site_url('/dsr_admin/getDsr/') ?>' + $('#searchDate').val(),
            dataType: "json",
            success: function(response) {
                var tableBody = $('#data-table tbody');
                var totalVariance = 0;
                if (response) {
                    tableBody.empty(); // Clear previous data
                    for (var key in response) {
                        if (response.hasOwnProperty(key)) {
                            var entry = response[key];
                            var username = entry.username;
                            var variance = parseFloat(entry.variance); // Parse variance as a float
                            const dateString = entry.sales_date;
                            const formattedDate = new Date(dateString).toLocaleString('en-US', {
                                month: '2-digit',
                                day: '2-digit',
                                year: 'numeric',
                                hour: 'numeric',
                                minute: '2-digit',
                                hour12: true
                            });

                            // Create a new row and append it to the table
                            var newRow =
                                `<tr>` +
                                `<td style="font-size: 20px;">` + formattedDate + `</td>` +
                                `<td style="font-size: 20px;">` + username + `</td>` +
                                `<td style="font-size: 20px;">` + variance + `</td>`;
                            if (variance) {
                                newRow += `<td style="font-size:20px;"><input type="radio" name="dsrID" id="` + entry.dsrID + `" value="` + entry.dsrID + `" title="Variance"  style="font-size:20px;" required/> </td>` +
                                    `</tr>`
                            } else {
                                newRow += `<td style="font-size:20px;"></td>` +
                                    `</tr>`
                            }
                            tableBody.append(newRow);

                            // Update total variance
                            totalVariance += variance;
                        }
                    }

                    // Update the total variance in the footer
                    $('#total-variance').text(totalVariance); // Display total variance with 2 decimal places

                    if (totalVariance == 0) {
                        $('#payment1button').hide();
                    } else {
                        $('#payment1button').show();
                    }
                } else {
                    $('#payment1button').show();
                }
            }
        });
    });
</script>