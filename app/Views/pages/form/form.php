    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $title ?></title>

        <link rel="shortcut icon" href="<?php echo base_url() ?>assets/images/LOGO.jpg" />

        <!-- base:css -->
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/vendors/mdi/css/materialdesignicons.min.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/vendors/css/vendor.bundle.base.css">
        <!-- base:css -->

        <!-- plugins -->
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/libs/bootstrap4/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/libs/select2/select2.min.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/libs/sweetAlert2/sweetalert2.min.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/libs/jquery_ui/jquery-ui.css">
        <!-- plugins -->

        <!-- custom -->
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/style.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/custom/css/style.css">
        <!-- custom -->

        <!-- base:js -->
        <script src="<?php echo base_url() ?>assets/vendors/js/vendor.bundle.base.js"></script>
        <script src="<?php echo base_url() ?>assets/libs/bootstrap4/popper.min.js"></script>
        <script src="<?php echo base_url() ?>assets/libs/bootstrap4/bootstrap.bundle.min.js"></script>
        <!-- base: js -->

        <!-- plug ins -->
        <!-- plug ins -->

        <!-- libs -->
        <script src="<?php echo base_url() ?>assets/libs/select2/select2.min.js"></script>
        <script src="<?php echo base_url() ?>assets/libs/sweetAlert2/sweetalert2.all.min.js"></script>
        <script src="<?php echo base_url() ?>assets/libs/jquery_ui/jquery-ui.js"></script>
        <!-- libs -->

        <style>
            .table-particular td,
            th {
                font-size: 25px !important;
                font-family: Arial, Helvetica, sans-serif;
                font-weight: bold;
                padding-top: 10px;
                padding-bottom: 10px;
                padding-left: 5px;
                padding-right: 5px;
            }

            .table-customer td {
                font-size: 20px;
                font-family: Arial, Helvetica, sans-serif;
                font-weight: bold;
                padding: 5px;
            }

            .table-total td {
                font-size: 45px !important;
            }

            .cash-table td {
                padding-top: 10px;
                padding-bottom: 10px;
                padding-left: 5px;
                padding-right: 5px;
                font-size: 30px !important;
            }

            .select2-results__option {
                font-size: 15px !important;
            }
        </style>
    </head>

    <body>
        <form action="<?php echo $controller_page . '/save' ?>" method="POST" id="frmSave">
            <input type="hidden" name="tranType" value="<?php echo $laundry->category ?>">
            <input type="hidden" name="kiloPrice" value="<?php echo $laundry->kilo ?>">
            <input type="hidden" name="comforterPrice" value="<?php echo $laundry->comforter ?>">
            <input type="hidden" name="detergentPrice" value="<?php echo $laundry->detergent ?>">
            <input type="hidden" name="bleachPrice" value="<?php echo $laundry->bleach ?>">
            <input type="hidden" name="totalLoads" id="totalLoads" value="" class="form-control" placeholder="Total Loads">
            <div class="main-panel">
                <div class="content-wrapper pb-1">
                    <div class="container-fluid mt-2">
                        <div class="row mb-2">
                            <div class="col-6 px-1">
                                <div class="card">
                                    <div class="card-body p-0">
                                        <table class="table table-borderless table-md table-customer">
                                            <tbody>
                                                <tr>
                                                    <td>CUSTOMER'S NAME<span class="text-danger">*</span></td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex">
                                                            <input type="text" class="form-control form-control-lg text-uppercase input-customer p-1" placeholder="first Middle Last" name="customer" id="customer" value="" title="Customer's Name" required />
                                                            <button type="button" id="triggerButton" class="btn btn-primary btn-sm">Search</button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>MOBILE #.<span class="text-danger">*</span></td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <input type="number" class="form-control form-control-lg input-customer p-1" placeholder="STARTS WITH 09" name="mobile" id="mobile" value="" oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="11" title="Mobile Number" required />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <i class="h6">Please check your work before saving.</i>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 px-1 ">
                                <div class="card table-content">
                                    <div class="card-body py-2">
                                        <h3 class="text-white my-1"> <?php echo strtoupper($title) ?></h3>
                                    </div>
                                </div>
                                <div class="card table-content">
                                    <div class="card-body py-1 justify-content-end px-1" style="height: 162px;">
                                        <table class="table table-borderless table-total ">
                                            <tbody>
                                                <tr>
                                                    <td>TOTAL DUE </td>
                                                    <td class="w-25">
                                                        <div class="d-flex justify-content-end">
                                                            <span class="mt-1 mr-2">₱</span>
                                                            <input type="text" class="form-control form-control-lg text-end total p-3" name="totalAmount" id="totalAmount" value="" placeholder="0.00" readonly title="Particular" required />
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>CHANGE</td>
                                                    <td>
                                                        <div class="d-flex justify-content-end">
                                                            <span class="mt-1 mr-2">₱</span>
                                                            <input type="text" class="form-control form-control-lg text-end total p-3" name="cashChange" id="cashChange" value="" placeholder="0.00" readonly />
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-7 px-1">
                                <div class="card p-0">
                                    <div class="card-body p-0">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <table class="table table-collapse table-md table-particular mb-3">
                                                <thead>
                                                    <tr class="header-blue">
                                                        <th class="wx-400 px-2">PARTICULAR</th>
                                                        <th class="wx-100 px-2">QTY</th>
                                                        <th class="wx-100 text-right px-2">AMOUNT</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- clothes -->
                                                    <tr>
                                                        <td><span>CLOTHES</span> (<small><?php echo $laundry->kilo ?>/kg</small>)</td>
                                                        <td>
                                                            <select class="form-select category-select w-100" name="kiloQty" id="kiloQty">
                                                                <option value="">---</option>
                                                                <?php for ($i = 4; $i <= 50; $i++) { ?>
                                                                    <option value="<?php echo $i ?>"><?php echo $i ?> kg</option>
                                                                <?php } ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex">
                                                                <span class="mt-2 pr-2">₱</span>
                                                                <input type="text" class="form-control form-control-sm input-amount" name="kiloAmount" id="kiloAmount" value="" placeholder="0.00" readonly />
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <!-- /clothes -->
                                                    <!-- comforter -->
                                                    <tr>
                                                        <td><span>COMFORTER </span> (<small><?php echo $laundry->comforter ?>/load</small>)</td>
                                                        <td>
                                                            <select class="form-control form-control-lg category-select" name="comforterLoad" id="comforterLoad">
                                                                <option value="">---</option>
                                                                <?php for ($i = 1; $i <= 50; $i++) { ?>
                                                                    <option value="<?php echo $i ?>"><?php echo $i ?> loads</option>
                                                                <?php } ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex">
                                                                <span class="mt-2 pr-2">₱</span>
                                                                <input type="text" class="form-control form-control-sm input-amount" name="comforterAmount" id="comforterAmount" value="" placeholder="0.00" readonly />
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <!-- /comforter -->
                                                    <!-- detergent -->
                                                    <tr>
                                                        <td><span> DETERGENT & SOFTENER</span> (<small><?php echo $laundry->detergent ?>/set</small>)</td>
                                                        <td>
                                                            <select class="form-control form-control-lg category-select" name="detergentSet" id="detergentSet">
                                                                <option value="">---</option>
                                                                <?php for ($i = 1; $i <= 50; $i++) { ?>
                                                                    <option value="<?php echo $i ?>"><?php echo $i ?> set</option>
                                                                <?php } ?>
                                                            </select>
                                                        </td>

                                                        <td>
                                                            <div class="d-flex">
                                                                <span class="mt-2 pr-2">₱</span>
                                                                <input type="text" class="form-control form-control-sm input-amount" name="detergentAmount" id="detergentAmount" value="" placeholder="0.00" readonly />
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <!-- /detergent -->
                                                    <!-- bleach -->
                                                    <tr>
                                                        <td><span>BLEACH</span> (<small><?php echo $laundry->bleach ?>/set</small>)</td>
                                                        <td>
                                                            <select class="form-control form-control-lg category-select" name="bleachLoad" id="bleachLoad">
                                                                <option value="">---</option>
                                                                <?php for ($i = 1; $i <= 50; $i++) { ?>
                                                                    <option value="<?php echo $i ?>"><?php echo $i ?> set</option>
                                                                <?php } ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex">
                                                                <span class="mt-2 pr-2">₱</span>
                                                                <input type="text" class="form-control form-control-sm input-amount" name="bleachAmount" id="bleachAmount" value="" placeholder="0.00" readonly />
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <!-- /bleach -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-5 px-1">
                                <div class="card">
                                    <div class="card-body px-2 py-4">
                                        <table class="table table-borderless cash-table">
                                            <tbody>
                                                <tr>
                                                    <td>TENDER</td>
                                                    <td><input type="number" class="form-control form-control-sm input-cash text-right" id="cash" name="cash" value="" placeholder="" title="Cash" oninput="this.value = this.value.replace(/[^0-9]/g, '')" /></td>
                                                </tr>
                                                <tr>
                                                    <td>GCASH</td>
                                                    <td><input type="number" class="form-control form-control-sm input-cash text-right" id="gCash" name="gCash" value="" placeholder="" oninput="this.value = this.value.replace(/[^0-9]/g, '')" /></td>
                                                </tr>
                                                <tr>
                                                    <td>REF #</td>
                                                    <td><input type="text" class="form-control form-control-sm" id="referenceNo" name="referenceNo" value="" placeholder="" title="Reference Number" /></td>
                                                </tr>
                                                <tr>
                                                    <td>REMARKS</td>
                                                    <td class="text-end">
                                                        <select class="form-select form-select-lg remarks-select" name="remarks" id="remarks">
                                                            <option value="">---</option>
                                                            <?php foreach ($remarks as $rem) { ?>
                                                                <option value="<?php echo $rem ?>"><?php echo $rem ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row fixed-bottom px-3 pb-2">
                            <div class="col-12 d-flex justify-content-between p-1">
                                <a href="<?php echo site_url('dashboard') ?>" class="btn btn-secondary rounded-pill btn-lg text-white" id="return">RETURN</a>
                                <div class="button_save">
                                    <button type="button" class="btn btn-primary rounded-pill btn-lg" id="frmCmdSave">SAVE</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <script>
            function totalLoads() {
                var clothes = parseInt($('#kiloQty').val() || 0);
                var comforterLoad = parseInt($('#comforterLoad').val() || 0);
                var maxLoad = <?php echo $maxLoad->value ?>;
                var minLoad = <?php echo $minLoad->value ?>;

                var load = 0;
                if (clothes == 0) {
                    load = 0;
                } else if (clothes <= maxLoad) {
                    load = 1;
                } else {
                    load = clothes / maxLoad;
                }

                var total = load;
                var totalLoads = Math.ceil(total + comforterLoad);
                $('#totalLoads').val(totalLoads);
            }

            function check_fields(frm) {
                var valid = true;
                var req_fields = "";

                $(`#${frm} [required]`).each(function() {
                    if ($(this).val() == '') {
                        req_fields += $(this).attr('title') + "<br/>";
                        valid = false;
                    }
                })

                if ($('#mobile').val().length !== 11) {
                    req_fields += $('#mobile').attr('title') + " length must be 11";
                    valid = false;
                }

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

            function sendData(data) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo $controller_page . '/save' ?>",
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('.button_save').html('<h4>Printing...</h4>');
                        popOp(response.qrCode);
                    }
                });
            }

            function popOp(qrCode) {
                var width = 20;
                var height = 20;
                var left = 0;
                var top = window.innerHeight - height;
                var printClaim = '<?php echo site_url("print") ?>' + '/' + qrCode;
                var printJo = '<?php echo site_url("job_order_print") ?>' + '/' + qrCode;
                var options = `width=${width},height=${height},top=${top},left=${left},resizable=0,fullscreen=0`;

                // Open the first window
                var printClaimPop = window.open(printClaim, "Claim Slip", options);

                // Set up a timer to check if the first window is closed
                var checkFirstWindow = setInterval(function() {
                    if (printClaimPop.closed) {
                        clearInterval(checkFirstWindow); // Stop checking

                        // Open the second window when the first one is closed
                        var printJoPop = window.open(printJo, "Job Order", options);

                        // Set up a timer to check if the second window is closed
                        var checkSecondWindow = setInterval(function() {
                            if (printJoPop.closed) {
                                clearInterval(checkSecondWindow); // Stop checking
                                // Perform the desired action when the second window is closed
                                window.location.replace('<?php echo site_url('dashboard') ?>')
                            }
                        }, 500); // Check every 500 milliseconds for the second window
                    }
                }, 500); // Check every 500 milliseconds for the first window
            }

            $(".category-select").select2({
                minimumResultsForSearch: -1,
            });

            $(".remarks-select").select2({
                minimumResultsForSearch: -1,
            });

            $("#customer").autocomplete({
                source: function(request, response) {
                    // Check if the autocomplete should be active
                    if ($("#customer").data("autocompleteActive")) {

                        // AJAX request to fetch autocomplete suggestions
                        $.ajax({
                            type: "GET",
                            url: "<?php echo site_url('getcustomers') ?>/" + request.term,
                            dataType: "json",
                            beforeSend: function() {
                                var myElement = $("#triggerButton");

                                // Prepend the smaller spinner to the element
                                myElement.html('<span class="spinner-border spinner-border-sm text-white text-sm me-2"></span>');
                                myElement.attr('disabled', true);

                                // After 2000 milliseconds (2 seconds), remove the spinner
                                setTimeout(function() {
                                    // Remove the span using jQuery
                                    myElement.html('Search');
                                    myElement.attr('disabled', false);
                                }, 1000);

                            },
                            success: function(data) {
                                var result = [];

                                if (data.length > 0) {
                                    result = data;
                                }

                                response(result);
                            },
                            error: function() {
                                console.log("Error fetching autocomplete suggestions.");
                            }
                        });
                    }
                },
                minLength: 10000, // Set a high initial value
                select: function(event, ui) {
                    $('#mobile').val(ui.item.mobile);
                }
            });

            $("#triggerButton").on("click", function() {
                // Enable autocomplete and set a lower minLength to trigger the search
                $("#customer").data("autocompleteActive", true);
                $("#customer").autocomplete("option", "minLength", 1);
                $("#customer").autocomplete("search", $("#customer").val());
            });

            $("#customer").on("keypress", function(event) {
                // Enable autocomplete and set a lower minLength when Enter key is pressed
                if (event.keyCode === 13) {
                    $("#customer").data("autocompleteActive", true);
                    $("#customer").autocomplete("option", "minLength", 1);
                    $("#customer").autocomplete("search", $("#customer").val());
                }
            });

            // Disable autocomplete after a search
            $("#customer").on("autocompleteselect", function() {
                $("#customer").data("autocompleteActive", false);
            });

            $("#kiloQty").change(function() {
                var kiloQty = $(this).children("option:selected").val();
                var kiloPrice = <?php echo $laundry->kilo ?>;
                var kiloAmount = kiloPrice * kiloQty;
                $("#kiloAmount").val(kiloAmount.toFixed(2));

                let tempComforter = parseFloat($("#comforterAmount").val()) || 0.00;
                let tempDetergent = parseFloat($("#detergentAmount").val()) || 0.00;
                let tempBleach = parseFloat($("#bleachAmount").val()) || 0.00;

                let tempTotal = tempComforter + kiloAmount + tempDetergent + tempBleach;
                $("#totalAmount").val(tempTotal.toFixed(2));

                let cash = parseFloat($("#cash").val()) || 0.00;
                if (tempTotal && cash > tempTotal) {
                    cashChange = cash - tempTotal;
                    $("#cashChange").val(cashChange.toFixed(2));
                } else {
                    $("#cashChange").val('0.00');
                }

                var clothes = parseInt($('#kiloQty').val() || 0);
                var maxLoad = <?php echo $maxLoad->value ?>;

                var load = 0;
                if (clothes == 0) {
                    load = 0;
                } else if (clothes <= maxLoad) {
                    load = 1;
                } else {
                    load = clothes / maxLoad;
                }

                var total = load;
                var kiloLoad = Math.ceil(total);

                if (kiloLoad > 1) {
                    var newDetergentSetValue = kiloLoad - 1;
                    $('#detergentSet').val(newDetergentSetValue).change(); // Trigger the change event
                } else {
                    $('#detergentSet').val("").change(); // Trigger the change event
                }

                totalLoads()
            });

            $("#comforterLoad").change(function() {
                var comforterLoad = $(this).children("option:selected").val();
                var comforterPrice = <?php echo $laundry->comforter ?>;
                var comforterAmount = comforterPrice * comforterLoad;
                $("#comforterAmount").val(comforterAmount.toFixed(2));

                let tempKilo = parseFloat($("#kiloAmount").val()) || 0.00;
                let tempDetergent = parseFloat($("#detergentAmount").val()) || 0.00;
                let tempBleach = parseFloat($("#bleachAmount").val()) || 0.00;

                let tempTotal = comforterAmount + tempKilo + tempDetergent + tempBleach;

                $("#totalAmount").val(tempTotal.toFixed(2));
                let cash = parseFloat($("#cash").val()) || 0.00;
                if (tempTotal && cash > tempTotal) {
                    cashChange = cash - tempTotal;
                    $("#cashChange").val(cashChange.toFixed(2));
                } else {
                    $("#cashChange").val('0.00');
                }

                totalLoads()
            });

            $("#detergentSet").change(function() {
                var detergentSet = $(this).children("option:selected").val();
                var detergentPrice = <?php echo $laundry->detergent ?>;
                var detergentAmount = detergentPrice * detergentSet;
                $("#detergentAmount").val(detergentAmount.toFixed(2));

                let tempKilo = parseFloat($("#kiloAmount").val()) || 0.00;
                let tempComforter = parseFloat($("#comforterAmount").val()) || 0.00;
                let tempBleach = parseFloat($("#bleachAmount").val()) || 0.00;

                let tempTotal = tempComforter + tempKilo + detergentAmount + tempBleach;
                $("#totalAmount").val(tempTotal.toFixed(2));

                let cash = parseFloat($("#cash").val()) || 0.00;
                if (tempTotal && cash > tempTotal) {
                    cashChange = cash - tempTotal;
                    $("#cashChange").val(cashChange.toFixed(2));
                } else {
                    $("#cashChange").val('0.00');
                }

            });

            $("#bleachLoad").change(function() {
                bleachLoad = $(this).children("option:selected").val();
                var bleachPrice = <?php echo $laundry->bleach ?>;
                bleachAmount = bleachPrice * bleachLoad;
                $("#bleachAmount").val(bleachAmount.toFixed(2));

                let tempKilo = parseFloat($("#kiloAmount").val()) || 0.00; // Default to 0 if NaN
                let tempComforter = parseFloat($("#comforterAmount").val()) || 0.00; // Default to 0 if NaN
                let tempDetergent = parseFloat($("#detergentAmount").val()) || 0.00; // Default to 0 if NaN

                let tempTotal = tempKilo + tempComforter + bleachAmount + tempDetergent;
                $("#totalAmount").val(tempTotal.toFixed(2));

                let cash = parseFloat($("#cash").val()) || 0.00;
                if (tempTotal && cash > tempTotal) {
                    cashChange = cash - tempTotal;
                    $("#cashChange").val(cashChange.toFixed(2));
                } else {
                    $("#cashChange").val('0.00');
                }
            });

            $("#cash").on("input", function() {
                if ($(this).val() != 0 && $("#totalAmount").val() != 0) {
                    var cashChange = $(this).val() - $("#totalAmount").val();
                    if (cashChange < 0) {
                        $("#cashChange").val('0.00');
                    } else {
                        $("#cashChange").val(cashChange.toFixed(2));
                    }
                } else {
                    $("#cashChange").val('0.00');
                }
            });

            $('#gCash').on('input', function() {
                var gCash = parseFloat($(this).val());
                if (gCash > 0) {
                    $('#referenceNo').attr('required', true);
                } else {
                    $('#referenceNo').removeAttr('required');
                }
            });

            $('#frmCmdSave').on('click', function() {
                if (check_fields('frmSave')) {
                    $('#frmCmdSave').attr('disabled', true);
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Your are going to save this data. Would you like to continue?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'No',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Initialize an object to store form values
                            $('.button_save').html('<h3>Loading Please Wait</h3>');
                            var formValues = {};
                            $("#frmSave").find(":input:not(:button)").each(function() {
                                var element = $(this);
                                if (element) {
                                    formValues[element.attr("name")] = element.val() || 0;
                                }
                            });

                            sendData(formValues)
                        } else {
                            $('#frmCmdSave').attr('disabled', false);
                        }
                    })
                }
            });

            $('#mobile').on('input', function() {
                $(this).val(function(_, value) {
                    return value.slice(0, $(this).attr('maxlength'));
                });
            });
        </script>
    </body>

    </html>