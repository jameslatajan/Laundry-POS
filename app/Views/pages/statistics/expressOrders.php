<style>
    .table-transaction td {
        font-size: 15px;
    }

    .table-transaction th {
        font-size: 15px !important;
    }

    .table-transaction table {
        width: 100% !important;
        margin: 0 !important;
    }

    .table-transactions thead tr th {
        color: #fff;
        text-align: left;
        background-color: #000;
        position: relative;
    }
</style>
<div class="container-fluid ">
    <div class="row my-3">
        <div class="col-12">
            <h2 class="module-title">Express Orders</h2>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12 d-flex">
            <form action="<?php echo site_url('statistics/expressOrders') ?>" method="get" id="myform">
                <select name="year" id="year" class="selectpicker form-control form-control-sm me-2" style="width:150px">
                    <option value="">Choose</option>
                    <?php foreach (YEARS as $yr) { ?>
                        <option value="<?php echo $yr ?>" <?php if ($yr == $year) echo 'selected' ?>><?php echo $yr ?></option>
                    <?php } ?>
                </select>
            </form>
        </div>
    </div>

    <div class="row p-2">
        <div class="col-12">
            <div class="card" style="width: 60%;">
                <div class="card-body">
                    <div class="col-12"></div>
                    <?php if ($records) { ?>
                        <table class="table table-hover table-transaction">
                            <thead>
                                <tr>
                                    <td style="font-size: 15px; width: 200px">Month</td>
                                    <td style="font-size: 15px; width: 200px">Express Student</td>
                                    <td style="font-size: 15px; width: 200px">Express Regular</td>
                                    <td style="font-size: 15px;">Amount</td>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                $ttlStud = 0;
                                $ttlreg  = 0;
                                $ttl     = 0;
                                foreach ($records as $rec) { ?>
                                    <tr>
                                        <td style="font-size: 15px;"><?php echo $rec['month'] ?></td>
                                        <td style="font-size: 15px;"><?php echo $rec['student'] ?></td>
                                        <td style="font-size: 15px;"><?php echo $rec['regular'] ?></td>
                                        <td style="font-size: 15px;"><?php echo number_format($rec['total'], 2)  ?></td>
                                    </tr>
                                <?php
                                    $ttlStud += $rec['student'];
                                    $ttlreg  += $rec['regular'];
                                    $ttl     += $rec['total'];
                                } ?>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th><?php echo $ttlStud ?></th>
                                    <th><?php echo $ttlreg ?></th>
                                    <th><?php echo number_format($ttl, 2)  ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#year').on('change', function() {
        $('#myform').submit();
    });
</script>