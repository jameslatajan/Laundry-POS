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
    <div class="container-fluid pt-2">
        <div class="row">
            <div class="col-12">
                <h2 class="module-title"><?php echo $title ?></h2>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header p-1">
                        <form action="<?php echo site_url('/statistics/productivity') ?>" method="POST" id="frmFilter" class="w-50 button d-flex">
                            <h6 class="mt-2 mr-2">DATE: </h6>
                            <input type="date" name="startDate" value="<?php if ($startDate) echo $startDate ?>" class="form-control form-control-sm form-control-transactions flatpickr-input wx-100 py-3" required>
                            <span class="mx-2 mt-2">-</span>
                            <input type="date" name="endDate" value="<?php if ($endDate) echo $endDate ?>" class="form-control form-control-sm form-control-transactions flatpickr-input wx-100 py-3" required>
                            <button type="button" class="btn btn-primary btn-sm ml-2 btn-transactions rounded-pill" id="filter">FILTER</button>
                        </form>
                    </div>
                    <?php if ($productivity) { ?>
                        <div class="card-body p-0">
                            <table class="table table-bordered table-md table-transactions ">
                                <thead class="header-blue">
                                    <tr>
                                        <th class="wx-100">STAFF</th>
                                        <th class="wx-100">WASH</th>
                                        <th class="wx-100">DRY</th>
                                        <th class="wx-100">FOLD</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($productivity as $prod) { ?>
                                        <tr>
                                            <td><?php echo $prod['userName'] ?></td>
                                            <td><?php if ($prod['wash']) echo number_format($prod['wash'])   ?></td>
                                            <td><?php if ($prod['dry'])  echo number_format($prod['dry'])  ?></td>
                                            <td><?php if ($prod['fold']) echo number_format($prod['fold'])  ?></td>
                                        </tr>
                                    <?php } ?>
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