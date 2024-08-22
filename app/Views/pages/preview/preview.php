<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title><?php echo $content['title'] ?></title>
  <link rel="stylesheet" href="<?php echo base_url('vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('css/style.css') ?>">
  <link rel="shortcut icon" href="<?php echo base_url() ?>images/LOGO.jpg" />
  <link rel="stylesheet" href="<?php echo base_url('sweetAlert2/sweetalert2.min.css') ?>">
</head>

<body>
  <script src="<?php echo base_url('vendors/js/vendor.bundle.base.js') ?>"></script>
  <script src="<?php echo base_url('js/jquery.cookie.js') ?>"></script>
  <script src="<?php echo base_url('js/bootstrap.min.js') ?>"></script>
  <script src="<?php echo base_url('js/popper.min.js') ?>"></script>
  <script src="<?php echo base_url('js/template.js') ?>"></script>
  <script src="<?php echo base_url('js/dashboard.js') ?>"></script>
  <script src="<?php echo base_url('/js/qrcode/qrcode.js') ?>"></script>
  <script src="<?php echo base_url('/js/qrcode/jquery.qrcode.js') ?>"></script>
  <script src="<?php echo base_url() ?>sweetAlert2/sweetalert2.all.min.js"></script>


  <!-- <input type="hidden" name="transID" id="transID" value="<?php echo $content['transID'] ?>"> -->
  <input type="hidden" name="qrCode" id="qrCode" value="<?php echo $customer['qrCode'] ?>">
  <input type="hidden" name="url" id="url" value="<?php echo site_url('print') ?>">
  <input type="hidden" name="site_url" id="site_url" value="<?php echo site_url('claimSlip/') ?>">


  <style>
    .list:hover {
      cursor: pointer;
    }

    .receive {
      color: black;
    }

    .wash {
      color: #0331f2;
    }

    .dry {
      color: #0331f2;
    }

    .fold {
      color: #0331f2;
    }

    .ready {
      color: darkgreen;
    }

    .release {
      color: green;
    }

    .cancel {
      color: red;
    }
  </style>

  <?php
  $status = "";
  if ($customer['status'] == 1) {
    $status = 'receive';
  } else if ($customer['status'] == 3) {
    $status = 'wash';
  } else if ($customer['status'] == 4) {
    $status = 'dry';
  } else if ($customer['status'] == 5) {
    $status = 'fold';
  } else if ($customer['status'] == 6) {
    $status = 'ready';
  } else if ($customer['status'] == 7) {
    $status = 'release';
  } else {
    $status = 'cancel';
  } ?>
  <div class="container-fluid ">
    <div class="row d-flex justify-content-center px-2">
      <div class="col-md-12 mt-3">
        <div class="row">
          <div class="col-md-10">
            <h2 class="tranType"><?php echo $content['title'] ?></h2>
          </div>

        </div>
        <div class="row mb-3">
          <div class="col-md-8">
            <div class="card">
              <div class="card-body px-1">
                <table class="table table-hover table-borderless table-transactions mb-3">
                  <tbody>
                    <tr>
                      <td style="font-size: 15px; font-weight:bold">Name</td>
                      <td style="font-size: 15px; font-weight:bold"><?php echo strtoupper($customer['customer']) ?></td>
                      <td style="font-size: 15px; font-weight:bold">Transaction Date</td>
                      <td style="font-size: 15px; font-weight:bold"> <?php
                                                                      $date = date_create($customer['dateCreated']);
                                                                      echo date_format($date, "m/d/Y h:i A")
                                                                      ?></td>
                    </tr>
                    <tr>
                      <td style="font-size: 15px; font-weight:bold"> Mobile No.</td>
                      <td style="font-size: 15px; font-weight:bold"> <?php echo $customer['mobile'] ?></td>
                      <td style="font-size: 15px; font-weight:bold">Status</td>
                      <td class="<?php echo $status ?>" style="font-size: 15px; font-weight:bold">
                        <?php
                        if ($customer['status'] == 1) {
                          echo 'Receive';
                        } elseif ($customer['status'] == 2) {
                          echo 'Sort';
                        } elseif ($customer['status'] == 3) {
                          echo 'Wash';
                        } elseif ($customer['status'] == 4) {
                          echo 'Dry';
                        } elseif ($customer['status'] == 5) {
                          echo 'Fold';
                        } elseif ($customer['status'] == 6) {
                          echo 'Ready';
                        } elseif ($customer['status'] == 7) {
                          echo 'Released';
                        } else {
                          echo 'Cancelled';
                        }
                        ?></td>
                    </tr>
                    <tr>
                      <td style="font-size: 15px;">Payment Method</td>
                      <td style="font-size: 15px;" colspan="3">
                        <?php
                        echo $customer['paymentMethod'];
                        ?></td>
                    </tr>
                    <?php if ($customer['referenceNo'] != "") { ?>
                      <tr>
                        <td style="font-size: 15px;">Reference No.</td>
                        <td style="font-size: 15px;" colspan="3"> <?php echo $customer['referenceNo'] ?></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
                <table class="table table-hover table-striped table-transactions mb-3">
                  <thead class="table-dark">
                    <tr>
                      <th style="font-size: 15px;">Particular</th>
                      <th style="font-size: 15px;">Qty</th>
                      <th style="font-size: 15px;">Price</th>
                      <th style="font-size: 15px;">Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if ($customer['kiloQty'] != 0) { ?>
                      <tr>
                        <td style="font-size: 15px;">Clothes</td>
                        <td style="font-size: 15px;"><?php echo $customer['kiloQty'] . ' kg' ?></td>
                        <td style="font-size: 15px;"><?php echo '₱ ' . number_format($customer['kiloPrice'], 2, '.', ',') . ' / kilo' ?></td>
                        <td style="font-size: 15px;"><?php echo " ₱ " . number_format($customer['kiloAmount'], 2, '.', ',') ?></td>
                      </tr>
                    <?php } ?>
                    <?php if ($customer['comforterLoad'] != 0) { ?>
                      <tr>
                        <td style="font-size: 15px;">Comforter</td>
                        <td style="font-size: 15px;">
                          <?php
                          if ($customer['comforterLoad'] > 1) {
                            echo $customer['comforterLoad'] . ' loads';
                          } else {
                            echo $customer['comforterLoad'] . ' load';
                          }
                          ?> </td>
                        <td style="font-size: 15px;"><?php echo '₱ ' . number_format($customer['comforterPrice'], 2, '.', ',') . ' / load' ?></td>
                        <td style="font-size: 15px;"><?php echo " ₱ " . number_format($customer['comforterAmount'], 2, '.', ',') ?></td>
                      </tr>
                    <?php } ?>
                    <?php if ($customer['detergentSet'] != 0) { ?>
                      <tr>
                        <td style="font-size: 15px;">Detergent & Softener</td>
                        <td style="font-size: 15px;">
                          <?php
                          if ($customer['detergentSet'] > 1) {
                            echo $customer['detergentSet'] . ' sets';
                          } else {
                            echo $customer['detergentSet'] . ' set';
                          }
                          ?></td>
                        <td style="font-size: 15px;"><?php echo '₱ ' . number_format($customer['detergentPrice'], 2, '.', ',') . ' / set' ?></td>
                        <td style="font-size: 15px;"><?php echo " ₱ " . number_format($customer['detergentAmount'], 2, '.', ',') ?></td>
                      </tr>
                    <?php } ?>
                    <?php if ($customer['bleachLoad'] != 0) { ?>
                      <tr>
                        <td style="font-size: 15px;">Bleach</td>
                        <td style="font-size: 15px;">
                          <?php
                          if ($customer['bleachLoad'] > 1) {
                            echo $customer['bleachLoad'] . ' loads';
                          } else {
                            echo $customer['bleachLoad'] . ' load';
                          }
                          ?> </td>
                        <td style="font-size: 15px;"><?php echo '₱ ' . number_format($customer['bleachPrice'], 2, '.', ',') . ' / load' ?></td>
                        <td style="font-size: 15px;"><?php echo " ₱ " . number_format($customer['bleachAmount'], 2, '.', ',') ?></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
                <table class="table table-hover table-borderless table-transactions mb-3">
                  <tbody>
                    <?php if ($customer['remarks'] != "") { ?>
                      <tr>
                        <td style="width:100px;color:red;font-size: 15px;">Remarks</td>
                        <td style="color:red; font-size: 15px;"> <?php echo $customer['remarks'] ?> </td>
                      </tr>
                    <?php } ?>
                    <tr>
                      <td style="width:100px; font-size: 15px;">Job Order</td>
                      <td style="font-size: 15px;">
                        <?php echo $customer['totalLoads'] ?></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="col-md-4 ">
            <div class="card">
              <div class="card-body px-1">
                <table class="table table-hover table-borderless table-transactions mb-3">
                  <tbody>
                    <tr>
                      <td style="font-size: 20px; font-weight:bold"> Total Due </td>
                      <td style="font-size: 20px;font-weight:bold"> <?php echo "₱ " . number_format($customer['totalAmount'], 2, '.', ',') ?> </td>
                    </tr>
                    <tr>
                      <td style="font-size: 20px; font-weight:bold"> Amt Paid </td>
                      <td style="font-size: 20px; font-weight:bold"> <?php echo "₱ " . number_format($customer['amountPaid'], 2, '.', ',') ?> </td>
                    </tr>
                    <tr>
                      <td style="font-size: 20px; font-weight:bold"> Balance </td>
                      <td style="font-size: 20px; font-weight:bold"> <?php echo "₱ " . number_format($customer['balance'], 2, '.', ',') ?> </td>
                    </tr>
                    <tr>
                      <td style="font-size: 20px; "> Change </td>
                      <td style="font-size: 20px; "> <?php echo "₱ " . number_format($customer['cashChange'], 2, '.', ',') ?> </td>
                    </tr>
                  </tbody>
                </table>
                <div id="qrcode" class="text-center px-1">
                </div>
              </div>
            </div>
          </div>
        </div>
        <footer>
          <div class="row">
            <div class="col-md-6">
              <input type="hidden" name="exiturl" id="exiturl" value="<?php echo site_url('/dashboard') ?>">
              <button type="button" class="btn btn-secondary btn-md text-light me-2" id="exit">
                <i class="mdi mdi-exit-to-app"></i>
                Exit </button>
            </div>
            <div class="col-md-6 text-end">
              <button type="button" class="btn btn-primary btn-md text-light me-2" id="print">
                <i class="mdi mdi-printer"></i>Print
              </button>
            </div>
          </div>
        </footer>
      </div>
    </div>
  </div>

  <script>
    $('#exit').on('click', function() {
      Swal.fire({
        title: 'Confirm Exit?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = '<?php echo site_url() ?>';
        }
      })
    });

    $("#print").on("click", function() {
      Swal.fire({
        title: 'Confirm Print?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
      }).then((result) => {
        if (result.isConfirmed) {
          var width = 20;
          var height = 20;
          var left = 0;
          var top = (window.innerHeight) - (height);
          var url1 = '<?php echo site_url("print/" . $customer['qrCode']) ?>';
          var url2 = '<?php echo site_url("job_order_print/" . $customer['qrCode']) ?>';
          var options = `width=${width},height=${height},top=${top},left=${left},resizable=0,fullscreen=0`;

          var popup1 = window.open(url1, "Popup1", options);
          var popup2;

          // Check if Popup1 is closed and activate Popup2
          var checkPopup1Status = function() {
            if (popup1.closed) {
              clearInterval(popup1CheckInterval); // Stop checking
              popup2 = window.open(url2, "Popup2", options);
            }
          };

          // Check Popup1 status every 500 milliseconds (adjust the interval as needed)
          var popup1CheckInterval = setInterval(checkPopup1Status, 500);

        }
      })
    });

    var newUrl = $('#site_url').val() + $("#qrCode").val();
    $("#qrcode").qrcode({
      width: 210,
      height: 210,
      text: newUrl,
    });
  </script>
</body>

</html>