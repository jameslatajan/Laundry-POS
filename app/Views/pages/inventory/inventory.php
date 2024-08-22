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
      <div class="col-12 d-flex justify-content-between">
        <h2 class="module-title"><?php echo $module_title ?></h2>
        <button type="button" class="btn btn-primary btn-sm btn-transactions rounded-pill mb-2" data-toggle="modal" data-target="#saveItem"><i class="mdi mdi-plus md-transactions"></i> Add Item</button>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <form action="<?php echo $controller_page ?>" method="POST" id="frmFilter">
          <input type="hidden" id="sortby" name="sortby" value="<?php echo $sortby ?>">
          <input type="hidden" id="sortorder" name="sortorder" value="<?php echo $sortorder ?>">
          <div class="card ">
            <div class="card-header d-flex justify-content-end p-1">
              <div class="d-flex">
                <button class="btn btn-primary btn-sm mr-2 btn-transactions rounded-pill" id="filter"><i class="mdi mdi-filter"></i> Filter </button>
                <button type="button" class="btn btn-primary mr-2 btn-sm btn-transactions rounded-pill" id="clear"><i class="mdi mdi-window-close"></i>Clear</button>
                <!-- <button type="button" class="btn btn-primary btn-sm me-3" id="printlist"><i class="mdi mdi-printer"></i> Print</button> -->
                <?php if ($data['user']['userType'] == 'Admin') { ?>
                  <button type="button" class="btn btn-primary btn-sm btn-transactions rounded-pill" id="exportlist"><i class="mdi mdi-file-excel"></i> Export</button>
                <?php } ?>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover table-collapsable table-sm table-transactions" id="myTable">
                  <thead>
                    <tr class="header-blue">
                      <th></th>
                      <?php
                      $headers = array(
                        array('column_header' => 'DESCRIPTION', 'column_field' => 'description', 'width' => 'w-30', 'align' => 'center'),
                        array('column_header' => 'QTY ON HAND', 'column_field' => 'qty', 'width' => 'w-10 ', 'align' => 'center'),
                        array('column_header' => 'COST', 'column_field' => 'cost', 'width' => 'w-10 ', 'align' => 'center'),
                        array('column_header' => 'PRICE', 'column_field' => 'price', 'width' => 'w-10 ', 'align' => 'center'),
                      );

                      echo $HtmlHelper->tabular_header($headers, $sortby, $sortorder);
                      ?>
                      <th>STOCK CARD</th>
                    </tr>
                    <tr>
                      <th></th>
                      <th><input type="text" class="form-control form-control-sm form-control-transactions" name="description" id="description" value="<?php echo $description ?>" style="font-size: 15px  !important;"></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if ($records) { ?>
                      <?php foreach ($records as $rec) { ?>
                        <tr>
                          <td style="display: flex; text-align:center">
                            <button type="button" class="btn btn-outline-secondary btn-sm mr-2 btn-transactions" data-toggle="modal" data-target="#editStock" onclick="getEditStock(<?php echo $rec->itemID ?>)"><i class="mdi mdi-plus-box mdi-transactions"></i></button>
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-transactions" data-toggle="modal" data-target="#editItem" onclick="getEditStock(<?php echo $rec->itemID ?>)"><i class="mdi mdi-square-edit-outline mdi-transactions"></i></button>
                          </td>
                          <td style="font-size: 15px;"><?php echo $rec->description ?></td>
                          <td style="font-size: 15px;"><?php echo $rec->qty ?></td>
                          <td style="font-size: 15px;"><?php echo number_format($rec->cost, 2)  ?></td>
                          <td style="font-size: 15px;"><?php echo number_format($rec->price, 2)  ?></td>
                          <td style="font-size: 15px; text-align:center"><button type="button" class="btn btn-outline-secondary btn-sm btn-transactions" id="stockCard" onclick="getStockcard(<?php echo $rec->itemID ?>)"><i class="mdi mdi-view-list mdi-transactions"></i></button></td>
                        </tr>
                      <?php } ?>
                    <?php } else { ?>
                      <tr>
                        <td style="font-size: 25px; text-align:center;" colspan="14">No data found</td>
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
  </div>
</div>

<!-- display stock card -->
<form action="<?php echo $controller_page . '/saveitem' ?>" method="POST" id="frmSaveItem">
  <div class="modal fade" id="saveItem" tabindex="-1" aria-labelledby="saveItem" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header p-1">
          <h4 class="modal-title">Add Item</h4>
        </div>
        <div class="modal-body p-0">
          <table class="table table-borderless table-md table-transactions">
            <tbody>
              <tr>
                <td style="font-size: 20px" class="wx-200">ITEM NAME <span class="text-danger">*</span></td>
                <td><input type="text" class="form-control form-control-md" style="font-size: 20px" name="saveItemName" id="saveItemName" title="Item Name" value="" required></td>
              </tr>
              <tr>
                <td style="font-size: 20px">COST <span class="text-danger">*</span></td>
                <td><input type="text" class="form-control form-control-md" style="font-size: 20px" name="saveCost" id="saveCost" value="" title="Cost" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required></td>
              </tr>
              <tr>
                <td style="font-size: 20px">PRICE <span class="text-danger">*</span></td>
                <td><input type="text" class="form-control form-control-md" style="font-size: 20px" name="savePrice" id="savePrice" value="" title="Price" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required></td>
              </tr>
              <tr>
                <td style="font-size: 20px">QTY ON HAND <span class="text-danger">*</span></td>
                <td><input type="text" class="form-control form-control-md" style="font-size: 20px" name="saveQty" id="saveQty" value="" title="Qty on hand" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="modal-footer d-flex justify-content-between p-1">
          <button type="button" class="btn btn-secondary btn-sm text-white rounded-pill" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary btn-sm rounded-pill" id="saveItemDetails">Save</button>
        </div>
      </div>
    </div>
  </div>
</form>

<form action="<?php echo $controller_page . '/updatestock' ?>" method="POST" id="frmSaveEditStock">
  <div class="modal fade" id="editStock" tabindex="-1" aria-labelledby="updateItem" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header p-1">
          <h4 class="modal-title">Edit Stock</h4>
        </div>
        <div class="modal-body p-0">
          <input type="hidden" name="itemIDeditStock" id="itemIDeditStock" value="">
          <table class="table  table-borderless table-transactions table-md">
            <tbody>
              <tr>
                <td style="font-size: 20px;" class="w-50 text-right">ITEM NAME: </td>
                <td style="font-size: 20px;" id="descriptionEditStock"></td>
              </tr>
              <tr>
                <td style="font-size: 20px;" class="text-right">COST: </td>
                <td style="font-size: 20px;" id="costEditStock"></td>
              </tr>
              <tr>
                <td style="font-size: 20px;" class="text-right">PRICE: </td>
                <td style="font-size: 20px;" id="priceEditStock"></td>
              </tr>
              <tr>
                <td style="font-size: 20px;" class="text-right">QTY ON HAND <span class="text-danger">*</span></td>
                <td> <input type="text" class="form-control form-control-md wx-100" style="font-size: 20px;" name="qty" id="qty" title="Qty on Hand" value="" style="width: 150px;" oninput="this.value = this.value.replace(/[^-0-9]/g, '')" required>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="modal-footer d-flex justify-content-between p-1">
          <button type="button" class="btn btn-secondary btn-sm text-white rounded-pill" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary btn-sm rounded-pill" id="saveEditStock">Save</button>
        </div>
      </div>
    </div>
  </div>
</form>

<form action="<?php echo $controller_page . '/updateitem' ?>" method="post" id="frmSaveEditItem">
  <div class="modal fade" id="editItem" tabindex="-1" aria-labelledby="editItem" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header p-1">
          <h4 class="modal-title">Edit Item Details</h4>
        </div>
        <div class="modal-body p-0">
          <input type="hidden" name="itemIDeditItem" id="itemIDeditItem" value="">
          <table class="table  table-borderless table-transactions">
            <tbody>
              <tr>
                <td style="font-size: 25px; width: 250px" class="text-right">ITEM NAME: </td>
                <td style="font-size: 25px;"  id="descriptionEdiItem"></td>
              </tr>
              <tr>
                <td style="font-size: 25px;" class="text-right">COST <span class="text-danger">*</span></td>
                <td style="font-size: 25px;"> <input type="text" class="form-control form-control-md" name="costEditItem" id="costEditItem" title="Cost" value="" style="width: 150px; font-size:20px" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required></td>
              </tr>
              <tr>
                <td style="font-size: 25px;" class="text-right">PRICE <span class="text-danger">*</span></td>
                <td style="font-size: 25px;"> <input type="text" class="form-control form-control-md" name="priceEditItem" id="priceEditItem" title="Price" value="" style="width: 150px; font-size:20px" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="modal-footer d-flex justify-content-between p-1">
          <button type="button" class="btn btn-secondary btn-sm text-white rounded-pill" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary btn-sm rounded-pill" id="saveEditItem">Save</button>
        </div>
      </div>
    </div>
  </div>
</form>

<!-- display stock card -->
<div class="modal fade" id="stockcardmodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header p-1">
        <h4 class="modal-title">Stock Card</h4>
      </div>
      <div class="modal-body p-1" id="stockBody">
      </div>
      <div class="modal-footer p-1">
        <button type="button" class="btn btn-secondary btn-sm text-white rounded-pill" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    $(".select2-default").select2({
      minimumResultsForSearch: -1,
    });

    flatpickr('.flatpickr-input', {});
  });

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

  $('#saveItemDetails').on('click', function() {
    if (check_fields('frmSaveItem')) {
      Swal.fire({
        title: 'Are you sure?',
        text: 'Your are going to save this data',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
      }).then((result) => {
        if (result.isConfirmed) {
          $('#frmSaveItem').submit();
        }
      })
    }
  });

  $('#saveEditItem').on('click', function() {
    if (check_fields('frmSaveEditItem')) {
      Swal.fire({
        title: 'Are you sure?',
        text: 'Your are going to save this data',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
      }).then((result) => {
        if (result.isConfirmed) {
          $('#frmSaveEditItem').submit();
        }
      })
    }
  });

  $('#saveEditStock').on('click', function() {
    if (check_fields('frmSaveEditStock')) {
      Swal.fire({
        title: 'Are you sure?',
        text: 'Your are going to save this data',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
      }).then((result) => {
        if (result.isConfirmed) {
          $('#frmSaveEditStock').submit();
        }
      })
    }
  });

  $('#myTable tbody tr').click(function() {
    var rowData = $(this).find('td').map(function() {
      return $(this).text();
    }).get();

    $('#descriptionEditStock').text(rowData[1]);
    $('#costEditStock').text(rowData[3]);
    $('#priceEditStock').text(rowData[4]);

    $('#descriptionEdiItem').text(rowData[1]);
    $('#costEditItem').attr('placeholder', rowData[3]);
    $('#priceEditItem').attr('placeholder', rowData[4]);
  });

  function getEditStock(id) {
    $('#itemIDeditStock').val(id);
    $('#itemIDeditItem').val(id);
  }

  function getStockcard(id) {
    (async function getFormValues() {
      const {
        value: formValues
      } = await

      Swal.fire({
        imageUrl: "<?php echo base_url('assets/images/stockcard.jpg') ?>",
        html: `<div style="display:flex; justify-content: center">
              <div>
               <input type="date" class="form-control form-control-lg" style="width: 230px; margin-bottom:10px;font-size:20px" id="startDate" name="startDate" value="<?php echo date('Y-m-01') ?>" style="width: 150px" title="Start Date">
               <input type="date" class="form-control form-control-lg" style="width: 230px;font-size:20px" id="endDate" name="endDate" data-toggle="datetimepicker" value="<?php echo date('Y-m-d') ?>" title="End Date">
              </div>
            </div>`,
        focusConfirm: false,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Ok',
        preConfirm: () => {
          startDate = $('#startDate').val();
          endDate = $('#endDate').val();
          $.post("<?php echo $controller_page ?>/get_stockcard", {
              itemID: id,
              startDate: startDate,
              endDate: endDate
            },
            function(data) {
              $('#stockBody').html(data);
              $('#stockcardmodal').modal('show');
            }, "text");
        }
      })
    })() //do not modify
  }

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

  $('#printlist').on('click', function() {
    Swal.fire({
      title: 'Are you sure?',
      text: 'Do you wish to print list?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Confirm'
    }).then((result) => {
      if (result.isConfirmed) {
        var width = 800;
        var height = 800;
        var left = 400;
        var top = (window.innerHeight) - (height);
        var options = "width=" + width + ",height=" + height + ",top=" + top + ",left=" + left + ",resizable=0, fullscreen=0";
        var popup1 = window.open('<?php echo $controller_page . '/printlist' ?>', "Popup", options);
        // Check if Popup1 is closed and activate Popup2
        var checkPopup1Status = function() {
          if (popup1.closed) {
            clearInterval(popup1CheckInterval); // Stop checking
            $('#printlist').attr('disabled', false);
          }
        };

        var popup1CheckInterval = setInterval(checkPopup1Status, 500);
        return false; // Prevents the default behavior of the <a> tag
      }
    })
  });
</script>