<div class="container-fluid ">
    <div class="row">
        <div class="col-12">
            <h2>VARIANCE REPORT</h2>
        </div>
    </div>
    <form action="<?php echo site_url('statistics/variance') ?>" method="POST">
        <div class="row mb-3" style="width:200px">
            <div class="col-12 d-flex">
                <h4 class="mt-2 me-2">MONTH</h4>
                <select name="month" id="month" class="selectpicker form-control form-control-sm me-2" style="width:150px">
                    <option value="">Choose</option>
                    <?php foreach ($months as $index => $month) {
                        $value = str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?>
                        <option value="<?php echo $value ?>" <?php if ($value == $mon) echo 'selected' ?>><?php echo $month ?></option>
                    <?php } ?>
                </select>
                <h4 class="mt-2 me-2">YEAR</h4>
                <select name="year" id="year" class="selectpicker form-control form-control-sm me-2" style="width:150px">
                    <option value="">Choose</option>
                    <?php foreach ($years as $yr) { ?>
                        <option value="<?php echo $yr->year ?>" <?php if ($yr->year == $year) echo 'selected' ?>><?php echo $yr->year ?></option>
                    <?php } ?>
                </select>
                <button class="btn btn-primary btn-sm">FILTER</button>
            </div>
        </div>
    </form>
    <?php if ($variance) { ?>
        <div class="row p-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-hover table-bordered table-transaction">
                            <thead>
                                <tr>
                                    <th style="text-align: left; padding-left: 30px !important; width: 100px; font-size:12px">DATE</th>
                                    <th style="text-align: right; font-size:12px; width: 100px">SHIFT 1</th>
                                    <th style="text-align: right; font-size:12px; width: 100px">SHIFT 2</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $totalShift1 = 0;
                                $totalShift2 = 0;
                                foreach ($variance as $var) { ?>
                                    <tr>
                                        <td style="text-align: left; padding-left: 30px !important; font-size:12px"><?php echo date('M d, Y', strtotime($var['sales_date'])) ?></td>
                                        <td style="text-align: right; font-size:12px"><?php echo number_format($var['shift1'], 2)  ?></td>
                                        <td style="text-align: right; font-size:12px"><?php echo number_format($var['shift2'], 2)  ?></td>
                                    </tr>
                                <?php
                                    $totalShift1 += $var['shift1'];
                                    $totalShift2 += $var['shift2'];
                                } ?>

                                <tr>
                                    <td style="text-align: right; font-size:12px">Total</td>
                                    <td style="text-align: right; font-size:12px"><?php echo number_format($totalShift1, 2)  ?></td>
                                    <td style="text-align: right; font-size:12px"><?php echo number_format($totalShift2, 2)  ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>