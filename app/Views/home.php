<?php
$urls = array(
    'Regular'        => 'regular',
    'DIYRegular'     => 'diyregular',
    'Student'        => 'student',
    'DIYStudent'     => 'diystudent',
    'ExpressRegular' => 'expressregular',
    'ExpressStudent' => 'expressstudent',
);

$styles = array(
    'Regular'        => 'regular',
    'DIYRegular'     => 'regular',
    'Student'        => 'student',
    'DIYStudent'     => 'student',
    'ExpressRegular' => 'express',
    'ExpressStudent' => 'express',
);
?>
<div class="main-panel m-2">
    <div class="row px-2">
        <?php foreach ($laundry_price as $laun) {
            $category = str_replace(' ', '', $laun->category); ?>
            <div class="col-lg-4 col-md-4 mb-1 grid-margin stretch-card p-1">
                <div class="card offers wrapper <?php if (array_key_exists($category, $styles)) echo $styles[$category] ?>">
                    <a href="<?php if (array_key_exists($category, $styles)) echo site_url($urls[$category]) ?>" style="text-decoration: none;">
                        <div class="card-body text-center">
                            <div class="center">
                                <h1 style="font-size: 35px; font-weight: 700; font-family: inherit; color: #FFF;">
                                    <?php echo strtoupper($laun->category); ?>
                                </h1>
                                <h1 style="font-size: 30px; font-weight: 700; font-family: inherit; color: #FFF;">
                                    <?php echo $laun->kilo; ?>/kg
                                </h1>
                            </div>
                            <div class="wrapper2">
                                <div><span class="dot"></span></div>
                                <div><span class="dot"></span></div>
                                <div><span class="dot"></span></div>
                                <div><span class="dot"></span></div>
                                <div><span class="dot"></span></div>
                                <div><span class="dot"></span></div>
                                <div><span class="dot"></span></div>
                                <div><span class="dot"></span></div>
                                <div><span class="dot"></span></div>
                                <div><span class="dot"></span></div>
                                <div><span class="dot"></span></div>
                                <div><span class="dot"></span></div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="row">
        <div class="col-12 d-flex">
            <a href="<?php echo site_url('sales') ?>" class="btn btn-primary me-2 extra-menu">
                <i class="mdi mdi-cash-usd extra-menu-icon"></i>
                <h1 class="extra-menu-title">SALES</h1>
            </a>
            <?php if ($user['userType'] == 'Admin') { ?>
                <a href="<?php echo site_url('inventory') ?>" class="btn btn-primary me-2 extra-menu">
                    <i class="mdi mdi-view-list extra-menu-icon"></i>
                    <h1 class="extra-menu-title">INVENTORY</h1>
                </a>
            <?php } ?>
            <a href="<?php echo site_url('dsr_generate') ?>" class="btn btn-primary me-2 extra-menu">
                <i class="mdi mdi-chart-bar extra-menu-icon"></i>
                <h1 class="extra-menu-title">DSR</h1>
            </a>
            <a href="<?php echo site_url('expenses') ?>" class="btn btn-primary me-2 extra-menu">
                <i class="mdi mdi-cash-multiple extra-menu-icon"></i>
                <h1 class="extra-menu-title">EXPENSES</h1>
            </a>
        </div>
    </div>
</div>

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" style="display: none;" id="smsList">
    Launch demo modal
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">SMS Sent List</h5>
            </div>
            <div class="modal-body">
                <table class="table table-hover table-collapsable table-transactions table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>No.</th>
                            <th>Customer</th>
                            <th>Mobile</th>
                            <th>Status</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody id="sentSmslist">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary text-white btn-sm" data-dismiss="modal" id="closeModal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('#closeModal').on('click', function() {
        window.location.href = '<?php echo site_url() ?>'
    });

    $('#dailySms').on('click', function() {
        Swal.fire({
            title: 'Are you sure you want send SMS to all ready orders?',
            text: 'You can only send sms once a day',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: '<?php echo site_url('dailysms') ?>',
                    data: 'data',
                    dataType: "JSON",
                    beforeSend: function() {
                        $('#dailySms').attr('disabled', true);
                        Swal.fire({
                            title: 'Sending sms..',
                            html: 'It will take a while please do not close the window',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading()
                            },
                        }).then((result) => {
                            /* Read more about handling dismissals below */
                            if (result.dismiss === Swal.DismissReason.timer) {
                                console.log('I was closed by the timer')
                            }

                            if (result.dismiss === Swal.DismissReason.backdrop) {
                                console.log('I was closed by back drop')
                            }
                        })
                    },
                    success: function(response) {
                        console.log(response);
                        Swal.close();
                        if (response.sendSms == true) {
                            Swal.fire({
                                title: 'SMS Sent Succefully',
                                text: 'Click ok to see the list',
                                icon: 'success',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Ok',
                                cancelButtonText: 'Close'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    let count = 1;
                                    let content
                                    for (let res of response.mobileNumbers) {
                                        let successText = 'failed';
                                        let bg = 'danger';
                                        if (res.status == true) {
                                            successText = 'success'
                                            bg = 'success';
                                        }
                                        content += `<tr>
                                                    <td>${count++}</td>
                                                    <td>${res.customer}</td>
                                                    <td>${res.mobile}</td>
                                                    <td>
                                                        <span class="badge badge-pill badge-${bg} text-dark">${successText}</span>
                                                    </td>
                                                    <td>
                                                      ${res.message}
                                                    </td>
                                                </tr>`
                                    }
                                    $('#sentSmslist').html(content);
                                    $('#smsList').click();
                                } else {
                                    window.location.href = '<?php echo site_url() ?>'
                                }
                            })
                        } else {
                            Swal.fire({
                                title: 'Daily SMS Already Used',
                                text: 'You can only send sms once a day',
                                icon: 'warning',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = '<?php echo site_url() ?>'
                                }
                            })
                        }
                    },
                    error: function(jqXHR, exception) {
                        Swal.close();
                        Swal.fire({
                            title: jqXHR.status,
                            text: jqXHR.responseText,
                            icon: 'danger',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Ok'
                        })
                    }
                });
            }
        })
    });
</script>