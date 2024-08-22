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
                        <form action="<?php echo site_url('statistics/jobOrder') ?>" method="POST" class="d-flex w-50 button">
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
                        <div class="text">
                            <h3>REGULAR: <span id="noStud"></span></h3>
                            <h3 class="me-2">STUDENT: <span id="noReg"></span></h3>
                        </div>
                        <canvas id="areaChart2"></canvas>
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
        var student = <?php echo json_encode($student); ?>;
        var regular = <?php echo json_encode($regular); ?>;
        var noReg = <?php echo $noReg; ?>;
        var noStud = <?php echo $noStud; ?>;
        var days = <?php echo json_encode($days); ?>;
        var areaChartInstance;

        console.log(student);

        var areaOptions = {
            plugins: {
                filler: {
                    propagate: true
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return numeral(context.parsed.y).format('$ 0,0');
                        }
                    }
                }
            }
        };

        function updateChart() {
            $('#noReg').text(noReg);
            $('#noStud').text(noStud);

            var areaData = {
                labels: days.map(String), // Convert days to strings
                datasets: [{
                        label: 'Regular',
                        data: regular,
                        backgroundColor: 'rgba(34, 139, 34, 0.8)',
                        borderColor: 'rgb(34, 139, 34)',
                        borderWidth: 1,
                        fill: false,
                    },
                    {
                        label: 'Student',
                        data: student,
                        backgroundColor: 'rgba(0, 0, 255, 0.8)',
                        borderColor: 'rgb(0, 0, 255)',
                        borderWidth: 1,
                        fill: false,
                    }
                ]
            };

            var areaChartCanvas = $("#areaChart2").get(0);
            areaChartCanvas.height = 250; // Adjust the height as per your requirements
            areaChartCanvas.width = 800; // Adjust the width as per your requirements

            var context = areaChartCanvas.getContext("2d");
            context.clearRect(0, 0, areaChartCanvas.width, areaChartCanvas.height);

            if (areaData.datasets.length === 0) {
                $('#joTable').html(`
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

        updateChart();
    });
</script>