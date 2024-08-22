<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laundry Shop</title>

    <link rel="shortcut icon" href="<?php echo base_url() ?>images/LOGO.jpg" />
    <link rel="stylesheet" href="<?php echo base_url() ?>vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="<?php echo base_url() ?>vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="<?php echo base_url() ?>css/style.css">

    <script src="<?php echo base_url() ?>vendors/js/vendor.bundle.base.js"></script>
    <script src="<?php echo base_url() ?>js/jquery.cookie.js"></script>
    <script src="<?php echo base_url() ?>js/bootstrap.min.js"></script>
    <script src="<?php echo base_url() ?>js/popper.min.js"></script>

    <style>
        .pefromance-table td,
        th {
            font-size: 10.5px !important;
            font-weight: bold !important;
        }

        .metrics-table td,
        th {
            font-size: 10.5px !important;
            font-weight: bold !important;
        }

        .pending-table td,
        th {
            font-size: 10.5px !important;
            font-weight: bold !important;
        }

        .processing-table td,
        th {
            font-size: 10.5px !important;
            font-weight: bold !important;
        }

        .table-overflow {
            height: 400px;
            overflow-y: auto;
            margin-bottom: 5px;
        }

        .card {
            border-radius: 0px;
        }

        .card .card-body {
            padding-left: 10px !important;
            padding-right: 10px !important;
        }

        #date h2 {
            font-size: 30px;
        }

        .date-container {
            border-radius: 10px;
            background-color: #BB0000;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo site_url('performance') ?>"> <img src="<?php echo base_url() ?>images/logo.png" alt="" style="width:80px">
            </a>
            <div class="collapse navbar-collapse d-flex justify-content-center" id="navbarNav">
                <div class="date-container">
                    <h2 class="navbar-text text-white px-3 mb-0" id="date"></h2>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-3 pb-3">
        <div class="row">
            <div class="col-6">
                <div class="row mb-2">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="text-center">PERFORMANCE</h3>
                                <table class="table table-bordered table-striped pefromance-table mb-1">
                                    <thead>
                                        <tr>
                                            <th style="width: 200px">STAFF</th>
                                            <th style="text-align: center;">WASH</th>
                                            <th style="text-align: center;">DRY</th>
                                            <th style="text-align: center;">FOLD</th>
                                            <th style="text-align: center; width: 200px">POINTS</th>
                                        </tr>
                                    </thead>
                                    <tbody id="perforBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <table class="table table-bordered table-striped metrics-table mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 300px">METRICS</th>
                                            <th style="text-align: center;">MORNING</th>
                                            <th style="text-align: center;">AFTERNOON</th>
                                            <th style="text-align: center;">EVENING</th>
                                        </tr>
                                    </thead>
                                    <tbody id="metricsBody">
                                        <tr>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-center">PENDING JO</h3>
                        <div class="table-overflow">
                            <table class="table table-bordered table-striped pending-table mb-1">
                                <tbody id="pendingBody">

                                </tbody>
                            </table>
                        </div>
                        <spani id="pendingNo"></spani>
                    </div>
                </div>
            </div>

            <div class="col-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-center">PROCESSED JO</h3>
                        <div class="table-overflow">
                            <table class="table table-bordered table-striped processing-table mb-1">
                                <tbody id="processBody">

                                </tbody>
                            </table>
                        </div>
                        <small id="processNo"></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function getData() {
            $.ajax({
                type: "GET",
                url: "<?php echo site_url('/performance/getperformance') ?>",
                dataType: "JSON",
                success: function(response) {
                    var html = "";
                    for (var data in response) {
                        html += `
                    <tr>
                        <td>${response[data].userName}</td>
                        <td style="text-align: center;">${response[data].wash || ""}</td>
                        <td style="text-align: center;">${response[data].dry || ""}</td>
                        <td style="text-align: center;">${response[data].fold || ""}</td>
                        <td style="text-align: center;">${response[data].points || ""}</td>
                    </tr>`;
                    }

                    $('#perforBody').html(html);
                }
            });


            $.ajax({
                type: "GET",
                url: "<?php echo site_url('/performance/getmetrics') ?>",
                dataType: "JSON",
                success: function(response) {
                    var html = "";
                    for (var status in response) {
                        html += '<tr>';
                        html += '<td>' + status.toUpperCase() + '</td>';

                        for (var timeOfDay in response[status]) {
                            html += '<td style="text-align: center;">';

                            if (response[status][timeOfDay] !== null) {
                                html += response[status][timeOfDay];
                            } else {
                                html += '';
                            }

                            html += '</td>';
                        }

                        html += '</tr>';
                    }
                    $('#metricsBody').html(html);
                }
            });

            $.ajax({
                type: "GET",
                url: "<?php echo site_url('/performance/getpending') ?>",
                dataType: "JSON",
                success: function(response) {
                    var html = "";
                    for (var data in response) {
                        html += `
                    <tr>
                        <td>${response[data]}</td>
                    </tr>`;
                    }
                    $('#pendingBody').html(html);

                    // var count = response.length;
                    // if (count == 1) {
                    //     $('#pendingNo').text(count + ' pending job order ');
                    // }

                    // if (count > 1) {
                    //     $('#pendingNo').text(count + ' pending job orders ');
                    // }
                }
            });

            $.ajax({
                type: "GET",
                url: "<?php echo site_url('/performance/getprocess') ?>",
                dataType: "JSON",
                success: function(response) {
                    var html = "";
                    for (var data in response) {
                        html += `
                    <tr>
                        <td>${response[data]}</td>
                    </tr>`;
                    }
                    $('#processBody').html(html);

                    // var count = response.length;

                    // if (count == 1) {
                    //     $('#processNo').text(count + ' processed  job order');
                    // }

                    // if (count > 1) {
                    //     $('#processNo').text(count + ' processed job orders');
                    // }
                }
            });

            $.ajax({
                type: "GET",
                url: "<?php echo site_url('/performance/getdate') ?>",
                dataType: "JSON",
                success: function(response) {
                    $('#date').text(response.date);
                }
            });
        }

        function getDate() {

        }

        $(document).ready(function() {

            getData();

            setInterval(() => {
                // Call the getData function here
                getData();
            }, 5000);

            // setInterval(() => {
            //     // Call the getDate function here
            //     getDate();
            // }, 1000);;

        });
    </script>

</body>
</html>