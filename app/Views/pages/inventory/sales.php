<style>
  .select2-container {
    width: 100% !important;
    font-size: 12px !important;
    /* Adjust as needed */
  }

  .select2-container .select2-selection--single {
    height: 25px !important;
    /* Adjust the height as needed */
    display: flex;
    align-items: center;
  }

  .select2-container .select2-selection--multiple {
    height: auto !important;
    /* Ensure multiple selection container adapts */
    min-height: 25px !important;
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
  <div class="container-fluid my-2">
    <div class="row">
      <div class="col-4 d-flex">
        <h2 class="module-title"><?php echo $module_title ?></h2>
      </div>
    </div>
    <div class="row">
      <div class="col-4 pr-1">
        <form action="<?php echo $controller_page . '/save' ?>" method="POST" id="frmSave">
          <div class="card">
            <div class="card-header p-1">
              <h3 class="card-title">CREATE SALES</h3>
            </div>
            <div class="card-body p-0">
              <table class="table table-borderless table-sm mb-0">
                <tbody>
                  <tr>
                    <td style="font-size: 15px;" class="wx-50">VALE BY</td>
                    <td>
                      <select name="createValeBy" id="createValeBy" class="form-control form-control-sm form-control-transactions select2-search " data-live-search="true">
                        <option value="">Select</option>
                        <?php foreach ($users as $row) { ?>
                          <option value="<?php echo  $row->userID ?>"><?php echo  $row->username ?></option>
                        <?php } ?>
                      </select>
                    </td>
                  </tr>

                  <?php if ($current_user->userType == 'Admin') { ?>
                    <tr class="salesDate">
                      <td style="font-size: 15px;">DATE</td>
                      <td>
                        <input type="date" name="createSalesDate" id="createSalesDate" class="form-control form-control-md form-control-transactions flatpickr-input px-1" title="Sales Date" value="<?php echo date('Y-m-d') ?>">
                      </td>
                    </tr>
                  <?php } ?>
                  <tr>
                    <td style="font-size: 15px;">ITEM <span class="text-danger">*</span></td>
                    <td>
                      <select name="createDescription" id="createDescription" class="form-control form-control-md form-control-transactions select2-search " data-live-search="true" data-size="5" title="Item" required>
                        <option value="">Select Item</option>
                        <?php foreach ($items as $row) { ?>
                          <option value="<?php echo $row->itemID ?>"><?php echo $row->description ?></option>
                        <?php  } ?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td style="font-size: 15px;" class="pe-2">PRICE</td>
                    <td> <input type="text" id="createPrice" name="createPrice" class="form-control form-control-md form-control-transactions px-1" title="Price" value="0.00" readonly></td>
                  </tr>
                  <tr>
                    <td style="font-size: 15px;">QTY</td>
                    <td>
                      <input type="text" id="createQuantity" name="createQuantity" title="Quantity" class="form-control form-control-md form-control-transactions px-1" value="1" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                    </td>
                  </tr>
                  <tr>
                    <td style="font-size: 15px;" class="pe-2"> AMOUNT </td>
                    <td style="width: 200px;">
                      <input type="text" name="createAmount" id="createAmount" title="Amount" class="form-control form-control-md form-control-transactions px-1" value="0.00" readonly>
                    </td>
                  </tr>
                  <tr class="referenceNo">
                    <td style="font-size: 15px;">REF No.</td>
                    <td><input type="text" id="createReferenceNo" name="createReferenceNo" title="Reference No" class="form-control form-control-md form-control-transactions referenceNo px-1" value=""></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="card-footer text-right p-1">
              <button type="button" class="btn btn-primary btn-sm rounded-pill btn-transactions" id="saveCreate">Save</button>
            </div>
          </div>
        </form>
      </div>

      <!-- Sales List -->
      <div class="col-8 pl-1">
        <form action="<?php echo $controller_page ?>" method="POST" id="frmFilter">
          <input type="hidden" id="sortby" name="sortby" value="<?php echo $sortby ?>">
          <input type="hidden" id="sortorder" name="sortorder" value="<?php echo $sortorder ?>">
          <div class="card">
            <div class="card-header d-flex justify-content-between p-1">
              <div class="d-flex mt-1">
                <h6 class="mx-1 mt-1">Date: </h6>
                <input type="date" name="startDate" id="startDate" class="form-control form-control-sm form-control-transactions flatpickr-input wx-100" value="<?php echo date('Y-m-d', strtotime($startDate)) ?>">
                <span>-</span>
                <input type="date" name="endDate" id="endDate" class="form-control form-control-sm form-control-transactions flatpickr-input wx-100" value="<?php echo date('Y-m-d', strtotime($endDate)) ?>">
              </div>
              <div class="buttons">
                <button class="btn btn-primary btn-sm rounded-pill btn-transactions" id="filter"><i class="mdi mdi-filter"></i> Filter </button>
                <button type="button" class="btn btn-primary btn-sm rounded-pill btn-transactions" id="clear"><i class="mdi mdi-window-close"></i>Clear</button>
                <?php if ($data['user']['userType'] == 'Admin') { ?>
                  <button type="button" class="btn btn-primary btn-sm rounded-pill btn-transactions" id="exportlist"><i class="mdi mdi-file-excel"></i> Export</button>
                <?php } ?>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover table-collapsable table-transactions table-sm" id="myTable" style="width: 150%;">
                  <thead>
                    <tr class="header-blue">
                      <?php
                      $headers = array(
                        array('column_header' => 'DATE CREATED', 'column_field' => 'salesDate', 'width' => 'wx-100', 'align' => 'center'),
                        array('column_header' => 'ITEM', 'column_field' => 'description', 'width' => 'wx-300 ', 'align' => 'center'),
                        array('column_header' => 'COST', 'column_field' => 'itemCost', 'width' => 'wx-100 ', 'align' => 'center'),
                        array('column_header' => 'QTY', 'column_field' => 'qty', 'width' => 'wx-100 ', 'align' => 'center'),
                        array('column_header' => 'PRICE', 'column_field' => 'price', 'width' => 'wx-100 ', 'align' => 'center'),
                        array('column_header' => 'AMT', 'column_field' => 'amount', 'width' => 'wx-100 ', 'align' => 'center'),
                        array('column_header' => 'VALE BY', 'column_field' => 'valeBy', 'width' => 'wx-200', 'align' => 'center'),
                        array('column_header' => 'P. METHOD', 'column_field' => 'paymentMethod', 'width' => 'wx-100 ', 'align' => 'center'),
                        array('column_header' => 'SALES DATE', 'column_field' => 'dateCreated', 'width' => 'wx-100', 'align' => 'center'),
                        array('column_header' => 'CASHIER', 'column_field' => 'userID', 'width' => 'wx-200', 'align' => 'center'),
                      );

                      echo $HtmlHelper->tabular_header($headers, $sortby, $sortorder);
                      ?>
                    </tr>

                    <tr>
                      <th></th>
                      <th><input type="text" class="form-control form-control-sm form-control-transactions" name="description" id="description" value="<?php echo $description ?>"></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th>
                        <select name="valeBy" id="valeBy" class="form-control form-control-sm  form-control-transactions select2-default">
                          <option value=""></option>
                          <?php foreach ($users as $use) { ?>
                            <option value="<?php echo $use->userID ?>" <?php if ($valeBy == $use->userID) echo 'selected'; ?>><?php echo $use->username ?></option>
                          <?php } ?>
                        </select>
                      </th>
                      <th>
                        <select name="paymentMethod" id="paymentMethod" class="form-control form-control-sm form-control-transactions select2-default">
                          <option value=""></option>
                          <option value="Cash" <?php if ($paymentMethod == 'Cash') echo 'selected'; ?>>Cash</option>
                          <option value="Gcash" <?php if ($paymentMethod == 'Gcash') echo 'selected'; ?>>GCash</option>
                          <option value="None" <?php if ($paymentMethod == 'None') echo 'selected'; ?>>None</option>
                        </select>
                      </th>
                      <th> </th>
                      <th>
                        <select name="userID" id="userID" class="form-control form-control-sm  form-control-transactions select2-default">
                          <option value=""></option>
                          <?php foreach ($users as $use) { ?>
                            <option value="<?php echo $use->userID ?>" <?php if ($userID == $use->userID) echo 'selected'; ?>><?php echo $use->username ?></option>
                          <?php } ?>
                        </select>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if ($records) { ?>
                      <?php
                      $totalAmt = 0;
                      foreach ($records as $rec) { ?>
                        <?php
                        $style   = "";
                        $onclick = "";
                        if ($rec->paymentMethod == "None") {
                          $style   = "style='color: green'";
                          $onclick = "onclick='openModal($rec->salesID)'";
                        } ?>
                        <tr <?php echo $style . ' ' .  $onclick ?>>
                          <td style="text-align: left;"><?php echo date('m/d/Y', strtotime($rec->dateCreated)) ?></td>
                          <td><?php echo $rec->description ?></td>
                          <td><?php echo number_format($rec->itemCost, 2)  ?></td>
                          <td><?php echo $rec->qty ?></td>
                          <td><?php echo number_format($rec->price, 2)  ?></td>
                          <td><?php echo number_format($rec->amount, 2)  ?></td>
                          <td><?php echo $rec->valeName ?></td>
                          <td><?php echo $rec->paymentMethod ?></td>
                          <td><?php if ($rec->salesDate != "0000-00-00 00:00:00") echo date('d/m/Y', strtotime($rec->salesDate)) ?></td>
                          <td><?php echo $rec->username ?></td>
                        </tr>
                      <?php
                        $totalAmt += $rec->amount;
                      } ?>
                      <tr>
                        <td colspan="4" style="text-align: end; font-weight:bold">GRAND TOTAL</td>
                        <td><?php echo number_format($totalAmt, 2)  ?></td>
                        <td colspan="5"></td>
                      </tr>
                    <?php } else { ?>
                      <tr>
                        <td style="text-align:center;" colspan="14">No data found</td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer p-1">
              <div class="d-flex justify-content-between">
                <?php echo $pagination; ?>
                <div class="limit">
                  <?php if (isset($limit)) { ?>
                    <!-- Pagination Details -->
                    <div class="limit-details d-flex">
                      <div class="range wx-50">
                        <select class="form-control form-control-sm form-control-transactions select2-default text-center" id="limit" name="limit">
                          <?php for ($i = 10; $i <= 200; $i *= 2) { ?>
                            <option value="<?php echo $i ?>" <?php if ($limit == $i)  echo "selected"; ?>><?php echo $i ?></option>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="fw-light fs-italic text-muted text-end ml-2">
                        <?php $display = min($offset + $limit, $ttl_rows); ?>
                        <small class="dataTables_info">Displaying <?php echo $offset + 1; ?> - <?php echo $display; ?> of <?php echo number_format($ttl_rows, 0); ?> records</small>
                      </div>
                      <!-- End of Pagination Details -->
                    </div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Pay Vale -->
    <form action="<?php echo $controller_page . '/savevale' ?>" method="POST" id="frmSaveVale">
      <div class="modal fade" id="modalSaveVale" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog " role="document" style="width:370px  !important">
          <div class="modal-content ">
            <div class="modal-header">
              <h4 class="modal-title" id="exampleModalLabel">Pay Vale</h4>
            </div>
            <div class="modal-body">
              <input type="hidden" name="paySalesID" id="paySalesID" value="">
              <div class="d-flex mb-3">
                <h3 class="me-2">VALE BY: </h3>
                <h3 id="payValeBy"></h3>
              </div>
              <table class="table table-borderless table-transactions">
                <tbody>
                  <tr>
                    <td style="font-size: 20px; width: 100px">ITEM</td>
                    <td style="font-size: 20px" id="payItem"></td>
                  </tr>
                  <tr>
                    <td style="font-size: 20px">QTY</td>
                    <td style="font-size: 20px" id="payQuantity"></td>
                  </tr>
                  <tr>
                    <td style="font-size: 20px">PRICE</td>
                    <td style="font-size: 20px" id="payPrice"></td>
                  </tr>
                  <tr>
                    <td style="font-size: 20px">AMOUNT</td>
                    <td style="font-size: 20px" id="payAmount"></td>
                  </tr>
                  <tr>
                    <td style="font-size: 20px">REF No.</td>
                    <td><input type="text" id="payReferenceNo" name="referenceNo" class="form-control form-control-md" value="" title="Reference No." a></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="modal-footer d-flex justify-content-between">
              <button type="button" class="btn btn-secondary btn-sm text-white" data-bs-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary btn-sm" id="saveVale">Save</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  $(".select2-default").select2({
    minimumResultsForSearch: -1,
  });
  $(".select2-search").select2({});

  flatpickr('.flatpickr-input', {});

  function openModal(salesID) {
    $.ajax({
      type: "GET",
      url: "<?php echo $controller_page . '/getsales/' ?>" + salesID,
      dataType: "JSON",
      success: function(response) {
        $('#paySalesID').val(response.salesID);
        $('#payItem').text(response.description);
        $('#payQuantity').text(response.qty);
        $('#payPrice').text(response.price);
        $('#payAmount').text(response.amount);

        $('#payValeBy').text(response.username);
        $('#modalSaveVale').modal('show');
      }
    });
  }

  function check_fields(frm) {
    var valid = true;
    var req_fields = "";

    $(`#${frm} [required]`).each(function() {
      if ($(this).val() == '') {
        req_fields += $(this).attr('title') + "<br/>";
        valid = false;
      }
    })

    if (!valid) {
      Swal.fire({
        title: 'Required Fields',
        html: req_fields,
        icon: 'warning',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Ok',
      }).then((result) => {

      })
    }
    return valid;
  }

  $(document).ready(function() {
    $('#exportlist').on('click', function() {
      Swal.fire({
        title: 'Are you sure?',
        text: 'Do you wish to export list?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = "<?php echo $controller_page . '/exportlist' ?>";
        }
      })
    });

    $('#saveVale').on('click', function() {
      if (check_fields('frmSaveVale')) {
        Swal.fire({
          title: 'Are you sure?',
          text: 'Your are going to save this data. Would you like to continue?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes',
          cancelButtonText: 'No',
        }).then((result) => {
          if (result.isConfirmed) {
            $('#frmSaveVale').submit();
          }
        })
      }
    });

    var valeList = [];
    $('#salesList tbody tr').on('click', function() {
      var tdText = $(this).find('td:eq(0)').text();
      if ($(this).find('td:eq(5)').text() != "") {
        $('#valeSalesID').val($(this).find('.salesID').val());
        $('#payItem').val($(this).find('td:eq(1)').text());
        $('#payQuantity').val($(this).find('td:eq(2)').text());
        $('#payPrice').val($(this).find('td:eq(3)').text());
        $('#payAmount').val($(this).find('td:eq(4)').text());
        $('#payDateCreated').val($(this).find('td:eq(8)').text());
        $('#payValeBy').text($(this).find('td:eq(5)').text());
      }
      // Display the text in an alert (you can modify this to display it in a modal or elsewhere)
    });

    $('#createValeBy').on('change', function() {
      if ($('#createValeBy').find('option:selected').val() >= 1) {
        $('#referenceNo').val("");
        $('.referenceNo').hide();
      } else {
        $('#referenceNo').val("");
        $('.referenceNo').show();
      }
    });

    $('#save_vale').on('click', function() {
      Swal.fire({
        title: 'Confirm Save?',
        text: 'Your are going to save this data. Would you like to continue?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
        cancelButtonText: 'No'
      }).then((result) => {
        if (result.isConfirmed) {
          $('#frmVale').submit();
        }
      })
    });

    $('#createDescription').on('change', function() {
      let itemID = $(this).val();

      if (itemID) {
        $.ajax({
          type: "GET",
          url: "<?php echo $controller_page . '/getitem/' ?>" + itemID,
          dataType: "json",
          success: function(response) {
            var price = parseFloat(response.price);
            $('#createPrice').val(price.toFixed(2));

            let quantity = $('#createQuantity').val();
            var amount = parseFloat(quantity * price);
            $('#createAmount').val(amount.toFixed(2));
          }
        });
      }
    });

    $('#createQuantity').on('input', function() {
      let quantity = $('#createQuantity').val();
      let price = $('#createPrice').val();

      var amount = parseFloat(quantity * price);

      $('#createAmount').val(amount.toFixed(2));
    });

    $('#saveCreate').on('click', function() {
      if (check_fields('frmSave')) {
        $('#saveCreate').attr('disabled', true);
        Swal.fire({
          title: 'Are you sure?',
          text: 'Your are going to save this data. Would you like to continue?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes',
          cancelButtonText: 'No',
        }).then((result) => {
          if (result.isConfirmed) {
            $('#frmSave').submit();
          } else {
            $('#saveCreate').attr('disabled', false);
          }
        })
      }
    });
  });
</script>