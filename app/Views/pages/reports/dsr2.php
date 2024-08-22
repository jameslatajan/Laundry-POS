<style>
    .txtLabel {
        font-size: 30px !important;
        font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
    }

    body {
        font-family: Arial, Helvetica, sans-serif;
    }

    h5,

    td {
        font-size: 14px;
    }

    img {
        margin: auto;
    }

    .heading,
    .heading2 {
        margin-bottom: 0px !important;
    }

    .square {
        height: 1px;
        width: 1px;
        background-color: white;
        border-style: solid;
        border-width: thin;
        margin-left: 90%;
    }
</style>
<div class="main-panel">
    <div class="container-fluid my-2">
        <div class="row">
            <div class="col-12 d-flex">
                <h2 class="module-title"><?php echo $title ?></h2>
            </div>
        </div>
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                <div class="card w-50">
                    <div class="card-body p-2">
                        <input type="hidden" name="userID" id="userID" value="<?php echo $userID ?>">
                        <?php if ($isDsr) { ?>
                            <div class="alert alert-warning" role="alert">Warning: DSR has already been generated; generating it again will overwrite the existing data. </div>
                        <?php } ?>
                        <table class="table table-sm table-borderless">
                            <tbody>
                                <tr>
                                    <td class="txtLabel wx-250">CASHIER:</td>
                                    <td class="txtLabel"><?php if (!empty($cashier['username'])) echo ucfirst($cashier['username']) | '' ?></td>
                                </tr>
                                <tr>
                                    <td class="txtLabel"> ACTUAL CASH: </td>
                                    <td class="txtLabel"><input type="text" id="actualCash" name="actualCash" class="form-control form-control-lg txtLabel p-2" title="Actual Cash" value="" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer p-1 text-right">
                        <button type="button" class="btn btn-primary btn-md btn-transactions rounded-pill" id="generate">Generate</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '#generate', function() {
        if (check_fields()) {
            Swal.fire({
                title: 'Confirm Submit?',
                text: 'Are you planning to generate a DSR (Daily Sales Report)? Would you like to proceed?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#generate').attr('disabled', true);
                    var width = 20;
                    var height = 20;
                    var left = 0;
                    var top = (window.innerHeight) - (height);
                    var options = "width=" + width + ",height=" + height + ",top=" + top + ",left=" + left + ",resizable=0, fullscreen=0";
                    var popup1 = window.open('<?php echo site_url('/dsr_generate/print/') ?>' + $('#actualCash').val(), "Popup", options);

                    // Check if Popup1 is closed and activate Popup2
                    var checkPopup1Status = function() {
                        if (popup1.closed) {
                            clearInterval(popup1CheckInterval); // Stop checking
                            window.location.href = '<?php echo site_url('/dsr_generate/logout') ?>'
                        }
                    };

                    var popup1CheckInterval = setInterval(checkPopup1Status, 500);
                    // return false; // Prevents the default behavior of the <a> tag
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
        })

        return valid;
    }
</script>