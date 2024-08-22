<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">

  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Laundry Shop</title>

  <link rel="shortcut icon" href="<?php echo base_url() ?>assets/images/LOGO.jpg" />

  <!-- base:css -->
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/vendors/css/vendor.bundle.base.css">
  <!-- base:css -->

  <!-- plug ins -->
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/libs/bootstrap4/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/libs/select2/select2.min.css">
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/libs/flatpickr/flatpickr.min.css">
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/libs/sweetAlert2/sweetalert2.min.css">
  <!-- plug ins -->

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
  <script src="<?php echo base_url() ?>assets/vendors/chart.js/Chart.min.js"></script>
  <script src="<?php echo base_url() ?>assets/js/off-canvas.js"></script>
  <script src="<?php echo base_url() ?>assets/js/hoverable-collapse.js"></script>
  <script src="<?php echo base_url() ?>assets/js/template.js"></script>
  <!-- plug ins -->

  <!-- libs -->
  <script src="<?php echo base_url() ?>assets/libs/select2/select2.min.js"></script>
  <script src="<?php echo base_url() ?>assets/libs/moment/moment.js_2.29.1_moment.min.js"></script>
  <script src="<?php echo base_url() ?>assets/libs/moment/moment-timezone-with-data-10-year-range.min.js"></script>
  <script src="<?php echo base_url() ?>assets/libs/flatpickr/flatpickr.min.js"></script>
  <script src="<?php echo base_url() ?>assets/libs/sweetAlert2/sweetalert2.all.min.js"></script>
  <!-- libs -->
</head>

<body>
  <div class="container-scroller d-flex">
    <!-- partial: ./partials/_sidebar.html -->
    <nav class="sidebar sidebar-offcanvas" id="sidebar">
      <ul class="nav mt-0">
        <!-- Clock Section -->
        <li class="nav-item">
          <a class="nav-link" href="#">
            <i class="mdi mdi-calendar-clock menu-icon"></i>
            <span id="MyClockDisplay" class="clock menu-title" onload="showTime()"></span>
          </a>
        </li>

        <!-- Home Section -->
        <li class="nav-item <?php if ($current_module == 'home') echo 'active'; ?>">
          <a class="nav-link" href="<?php echo base_url('dashboard') ?>">
            <i class="mdi mdi-monitor menu-icon"></i>
            <span class="menu-title">Home</span>
          </a>
        </li>

        <!-- Transactions Section -->
        <li class="nav-item <?php if ($current_module == 'transactions') echo 'active'; ?>">
          <a class="nav-link" href="<?php echo base_url('transaction') ?>">
            <i class="mdi mdi-account-multiple-plus menu-icon"></i>
            <span class="menu-title">Transactions</span>
          </a>
        </li>

        <!-- SMS Section -->
        <li class="nav-item <?php if ($current_module == 'sms') echo 'active'; ?>">
          <a class="nav-link" href="<?php echo base_url('sms') ?>">
            <i class="mdi mdi-cellphone menu-icon"></i>
            <span class="menu-title">SMS</span>
          </a>
        </li>

        <!-- Statistics Section (Visible only to Admin) -->
        <?php if ($data['user']['userType'] == 'Admin') { ?>
          <li class="nav-item <?php if ($current_module == 'statistics') echo 'active'; ?>">
            <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
              <i class="mdi mdi-finance menu-icon"></i>
              <span class="menu-title">Statistics</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-basic">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item <?php if ($current_menu == 'totalSales') echo 'show'; ?>">
                  <a class="nav-link" href="<?php echo site_url('statistics/totalSales') ?>">Total Sales</a>
                </li>
                <li class="nav-item <?php if ($current_menu == 'jobOrder') echo 'show'; ?>">
                  <a class="nav-link" href="<?php echo site_url('statistics/jobOrder') ?>">Job Order</a>
                </li>
                <li class="nav-item <?php if ($current_menu == 'statreport') echo 'show'; ?>">
                  <a class="nav-link" href="<?php echo site_url('statistics/statreport') ?>">Monthly Sales</a>
                </li>
                <li class="nav-item <?php if ($current_menu == 'productivity') echo 'show'; ?>">
                  <a class="nav-link" href="<?php echo site_url('statistics/productivity') ?>">Productivity</a>
                </li>
                <li class="nav-item <?php if ($current_menu == 'performanceReport') echo 'show'; ?>">
                  <a class="nav-link" href="<?php echo site_url('statistics/performanceReport') ?>">Daily Performance</a>
                </li>
                <li class="nav-item <?php if ($current_menu == 'performacestat') echo 'show'; ?>">
                  <a class="nav-link" href="<?php echo site_url('statistics/performacestat') ?>">Staff Stat</a>
                </li>
                <li class="nav-item <?php if ($current_module == 'reports') echo 'show'; ?>">
                  <a class="nav-link" href="<?php echo site_url('statistics/dsrsummary') ?>">DSR Summary</a>
                </li>
              </ul>
            </div>
          </li>
        <?php } ?>

        <!-- Reports Section -->
        <li class="nav-item <?php if ($current_module == 'reports') echo 'active'; ?>">
          <a class="nav-link" data-toggle="collapse" href="#ui-basic2" aria-expanded="false" aria-controls="ui-basic">
            <i class="mdi mdi-file-outline menu-icon"></i>
            <span class="menu-title">Reports</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="ui-basic2">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item <?php if ($current_module == 'expenses') echo 'show'; ?>">
                <a class="nav-link" href="<?php echo site_url('expenses') ?>">Expenses</a>
              </li>
              <li class="nav-item <?php if ($current_module == 'allowances') echo 'show'; ?>">
                <a class="nav-link" href="<?php echo site_url('allowances') ?>">Allowances</a>
              </li>
              <li class="nav-item <?php if ($current_module == 'dsr_generate') echo 'show'; ?>">
                <a class="nav-link" href="<?php echo site_url('dsr_generate') ?>">DSR</a>
              </li>
              <?php if ($data['user']['userType'] == 'Admin') { ?>
                <li class="nav-item <?php if ($current_module == 'dsr_admin') echo 'show'; ?>">
                  <a class="nav-link" href="<?php echo site_url('dsr_admin') ?>">DSR Admin</a>
                </li>
              <?php } ?>
              <li class="nav-item <?php if ($current_module == 'unpaid_report') echo 'show'; ?>">
                <a class="nav-link" href="<?php echo site_url('unpaid_report') ?>">Unpaid Report</a>
              </li>
              <li class="nav-item <?php if ($current_module == 'unpaid') echo 'show'; ?>">
                <a class="nav-link" href="<?php echo site_url('unpaids') ?>">Unpaid</a>
              </li>
            </ul>
          </div>
        </li>

        <!-- Daily Sales Section -->
        <li class="nav-item active">
          <a class="nav-link bg-blue" data-toggle="collapse" href="#ui-basic3" aria-expanded="false" aria-controls="ui-basic">
            <i class="mdi mdi-currency-usd menu-icon "></i>
            <span class="menu-title bg-blue">Daily Sales</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse show bg-blue" id="ui-basic3">
            <ul class="nav flex-column sub-menu p-0 ">
              <li class="p-0">
                <div class="table-responsive table-scroll ">
                  <table class="table table-collapsed table-sm" id="customers">
                    <thead>
                      <tr class="">
                        <th scope="col" class="font-weight-bold">Customer</th>
                        <th scope="col" class="font-weight-bold">Amount</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if ($data['record']) { ?>
                        <?php foreach ($data['record'] as $row) { ?>
                          <tr>
                            <td>
                              <?php
                              $custName = '';
                              if (strlen($row['customer']) > 15) {
                                echo substr($row['customer'], 0, 15);
                              } else {
                                echo $row['customer'];
                              }
                              ?>
                              <p class="mb-0 customer-name"><?php echo $custName ?></p>
                              <small class="mobile"><?php echo $row['mobile'] ?></small>
                            </td>
                            <td>
                              <?php
                              $format1 = number_format($row['totalAmount'], 2);
                              echo '₱ ' . $format1
                              ?>
                            </td>
                          </tr>
                        <?php } ?>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </li>
            </ul>
          </div>
        </li>

        <!-- Logout Section -->
        <li class="nav-item">
          <a class="nav-link" href="#" id="logout">
            <i class="mdi mdi-logout-variant menu-icon"></i>
            <span class="menu-title">Log-Out</span>
          </a>
        </li>
      </ul>
    </nav>

    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial: ./partials/_navbar.html -->
      <nav class="navbar col-lg-12 col-12 px-0 py-0 d-flex flex-row">
        <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
          <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
          </button>
          <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="mdi mdi-menu"></span>
          </button>
          <div class="navbar-brand-wrapper">
            <a class="navbar-brand brand-logo" href="<?php echo base_url() ?>"><img src="<?php echo base_url() ?>assets/images/LOGO.jpg" alt="logo" style="width: 50px; border-radius:30px;" /></a>
            <a class="navbar-brand brand-logo-mini" href="<?php echo base_url() ?>"><img src="<?php echo base_url() ?>assets/images/LOGO.jpg" alt="logo" style="border-radius:30px" /></a>
          </div>
          <div class="d-block">
            <h1 class="font-weight-bold mb-0 d-none d-md-block mt-1" behavior="" direction=""><strong>Laundry Shop</strong></h1>
            <small>Current User: <?php echo $data['user']['firstName']  ?></small>
          </div>
          <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item dropdown me-1">
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="messageDropdown">
                <p class="mb-0 font-weight-normal float-left dropdown-header">Messages</p>
                <a class="dropdown-item preview-item">

                  <div class="preview-item-content flex-grow">
                    <h6 class="preview-subject ellipsis font-weight-normal">David Grey
                    </h6>
                    <p class="font-weight-light small-text text-muted mb-0">
                      The meeting is cancelled
                    </p>
                  </div>
                </a>
                <a class="dropdown-item preview-item">
                  <!-- <div class = "preview-thumbnail">
                <img src        = "images/faces/face2.jpg" alt = "image" class = "profile-pic">
                  </div> -->
                  <div class="preview-item-content flex-grow">
                    <h6 class="preview-subject ellipsis font-weight-normal">Tim Cook
                    </h6>
                    <p class="font-weight-light small-text text-muted mb-0">
                      New product launch
                    </p>
                  </div>
                </a>
                <a class="dropdown-item preview-item">
                  <!-- <div class = "preview-thumbnail">
                <img src        = "images/faces/face3.jpg" alt = "image" class = "profile-pic">
                  </div> -->
                  <div class="preview-item-content flex-grow">
                    <h6 class="preview-subject ellipsis font-weight-normal"> Johnson
                    </h6>
                    <p class="font-weight-light small-text text-muted mb-0">
                      Upcoming board meeting
                    </p>
                  </div>
                </a>
              </div>
            </li>
            <li class="nav-item dropdown me-2">
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
                <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-success">
                      <i class="mdi mdi-information mx-0"></i>
                    </div>
                  </div>
                  <div class="preview-item-content">
                    <h6 class="preview-subject font-weight-normal">Application Error</h6>
                    <p class="font-weight-light small-text mb-0 text-muted">
                      Just now
                    </p>
                  </div>
                </a>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-warning">
                      <i class="mdi mdi-settings mx-0"></i>
                    </div>
                  </div>
                  <div class="preview-item-content">
                    <h6 class="preview-subject font-weight-normal">Settings</h6>
                    <p class="font-weight-light small-text mb-0 text-muted">
                      Private message
                    </p>
                  </div>
                </a>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-info">
                      <i class="mdi mdi-account-box mx-0"></i>
                    </div>
                  </div>
                  <div class="preview-item-content">
                    <h6 class="preview-subject font-weight-normal">New user registration</h6>
                    <p class="font-weight-light small-text mb-0 text-muted">
                      6 days ago
                    </p>
                  </div>
                </a>
              </div>
            </li>
          </ul>

          <div class="row mb-2 text-end">
            <div class="cols">
              <div class="col-md-12 input-group">
                <div class="input-group input-group-merge">
                  <input type="number" class="form-control form-control-sm" placeholder="Series #" name="transID" id="transID" value="" />
                  <button type="button" class="btn btn-primary btn-sm mt-0" style="font-size:20px" id="getTransID"> Search</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </nav>