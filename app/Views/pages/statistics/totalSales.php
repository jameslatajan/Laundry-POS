<style>
    .select2-container {
        width: 100% !important;
        font-size: 12px !important;
        /* Adjust as needed */
    }

    .select2-container .select2-selection--single {
        height: 30px !important;
        /* Adjust the height as needed */
        display: flex;
        align-items: center;
    }

    .select2-container .select2-selection--multiple {
        height: auto !important;
        /* Ensure multiple selection container adapts */
        min-height: 30px !important;
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
    <div class="container-fluid pt-2">
        <div class="row">
            <div class="col-12">
                <h2 class="module-title"><?php echo $title ?></h2>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between p-1">
                        <form action="<?php echo site_url('statistics/totalSales') ?>" method="POST" class="w-50 button d-flex">
                            <h6 class="mt-2 mr-2">MONTH</h6>
                            <select name="month" id="month" class="form-control form-control-sm select2-default form-control-transactions wx-200">
                                <?php foreach ($months as $index => $month) {
                                    $value = str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?>
                                    <option value="<?php echo $value ?>" <?php if ($value == $mon) echo 'selected' ?>><?php echo $month ?></option>
                                <?php } ?>
                            </select>
                            <h6 class="mt-2 mx-2">YEAR</h6>
                            <select name="year" id="year" class="form-control form-control-sm select2-default form-control-transactions wx-200">
                                <?php foreach (YEARS as $yr) { ?>
                                    <option value="<?php echo $yr ?>" <?php if ($yr == $year) echo 'selected' ?>><?php echo $yr ?></option>
                                <?php } ?>
                            </select>
                            <button class="btn btn-primary btn-sm mx-2 rounded-pill btn-transactions">FILTER</button>
                        </form>
                    </div>
                    <div class="card-body p-1">
                        <div class="text d-flex mb-1">
                            <h2 class="mr-2">GRAND TOTAL: </h2>
                            <h2 id="totalSales"> </h2>
                        </div>
                        <canvas id="areaChart" class="w-100 h-50"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(".select2-default").select2({
        minimumResultsForSearch: -1,
    });

    $(document).ready(function() {
        // Assuming $totalAmount is a PHP variable containing an array
        var totalSales = <?php echo json_encode($totalAmount); ?>;
        var areaChartInstance; // Declare areaChartInstance variable
        var days = <?php echo json_encode($days); ?>;

        var areaOptions = {
            plugins: {
                filler: {
                    propagate: true
                },
                tooltip: {
                    callback: function(value) {
                        return numeral(value).format('$ 0,0');
                    }
                }
            }
        };

        function updateChart() {
            // Assuming $totalSum and $days are PHP variables containing valid data
            $('#totalSales').text('<?php echo $totalSum ?>');

            var areaData = {
                labels: days, // Convert days to strings
                datasets: [{
                    label: "TOTAL SALES",
                    data: totalSales,
                    borderColor: 'rgb(75, 192, 192)',
                    borderWidth: 2,
                    fill: false
                }]
            };


            if ($("#areaChart").length) {
                var areaChartCanvas = $("#areaChart").get(0);
                areaChartCanvas.height = 250; // Adjust the height as per your requirements
                areaChartCanvas.width = 800; // Adjust the width as per your requirements

                var context = areaChartCanvas.getContext("2d");
                context.clearRect(0, 0, areaChartCanvas.width, areaChartCanvas.height);

                if (areaData.datasets.length === 0) {
                    $('#showTotal').html(`
                        <div class='col-lg-12 col-md-12 text-end'> 
                            <h1 class="text-end">No data available</h1>
                        </div>
                    `);
                } else {
                    if (areaChartInstance) {
                        areaChartInstance.destroy();
                    }
                    areaChartInstance = new Chart(areaChartCanvas, {
                        type: 'line',
                        data: areaData,
                        options: areaOptions
                    });
                }
            }


        }

        updateChart();
    });
</script>