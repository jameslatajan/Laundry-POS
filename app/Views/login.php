<!DOCTYPE html>
<html lang="en">

<head>
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
</head>

<body>
  <div class="container-scroller d-flex" style="background-color: #d9d9d9 !important;">
    <div class="container-fluid page-body-wrapper full-page-wrapper d-flex background">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-md-8 col-lg-5  mx-auto">
            <div class="auth-form-light text-left px-4 px-sm-5">
              <div class="row">
                <div class="brand-logo col-md-12 d-flex justify-content-center">
                  <div class="brand">
                    <img src="<?php echo base_url() ?>assets/images/LOGO.jpg" alt="logo" class="img-logo">
                  </div>
                  <div class="company">
                    <h2 class="logo-name mt-5">LABACHINE</h2>
                  </div>
                </div>
                <div class="alert mb-1 w-100" id="alert"></div>
                <div class="form-group col-md-12 mb-1">
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><i class="mdi mdi-account text-light"></i></div>
                    </div>
                    <input type="text" class="form-control form-control-md" placeholder="Username" name="username" id="username">
                  </div>
                  <small class="errors" id="usernameerror"></small>
                </div>
                <div class="form-group col-md-12 mb-3">
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><i class="mdi mdi-key-variant text-light"></i></div>
                    </div>
                    <input type="password" class="form-control form-control-md input-password" placeholder="Password" id="password" name="password">
                  </div>
                  <small class="errors" id="passworderror"></small>
                </div>
                <div class="form-group col-md-12">
                  <button class="btn btn-block btn-primary btn-md font-weight-medium auth-form-btn btn-signin" id="signin">Login</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <script>
    $(document).ready(function() {
      $("#signin").on("click", function() {
        // e.preventDefault;
        let username = $("#username").val();
        let password = $("#password").val();

        $.ajax({
          type: "POST",
          url: "signin",
          data: {
            username: username,
            password: password,
          },
          dataType: "JSON",
          beforeSend: function() {
            $('#signin').attr('disabled', true);
            $("#signin").html('<span class="spinner-border spinner-border-sm"></span>');
          },
          success: function(response) {
            console.log(response);
            if (response.success) {
              window.location.replace(response.url);
            } else {
              var alertHtml = `
                <div class="alert alert-danger alert-dismissible fade show mb-1" role="alert">
                  ${response.msg}
                </div>
              `;

              $("#alert").html(alertHtml);
              $("#usernameerror").text(response["username"]);
              $("#passworderror").text(response["password"]);

            }

            $('#signin').attr('disabled', false);
            $("#signin").html('Login');
          },
        });
      });

      $(document).on("keypress", function(e) {
        // e.preventDefault;
        if (e.which == 13) {
          let username = $("#username").val();
          let password = $("#password").val();

          $.ajax({
            type: "POST",
            url: "signin",
            data: {
              username: username,
              password: password,
            },
            dataType: "JSON",
            beforeSend: function() {
              $('#signin').attr('disabled', true);
              $("#signin").html('<span class="spinner-border spinner-border-sm"></span>');
            },
            success: function(response) {
              console.log(response);
              if (response["success"] == true) {
                window.location.replace(response["url"]);
              } else {
                var alertHtml = `
                <div class="alert alert-danger alert-dismissible fade show mb-1" role="alert">
                  ${response.msg}
                </div>
              `;

                $("#alert").html(alertHtml);
                $("#usernameerror").text(response["username"]);
                $("#passworderror").text(response["password"]);
              }

              $('#signin').attr('disabled', false);
              $("#signin").html('Login');
            },
          });
        }
      });

      $("#showpass").on("click", function() {
        var passInput = $("#password").attr("type");
        if (passInput === "password") {
          $("#password").attr("type", "text");
        } else {
          $("#password").attr("type", "password");
        }
      });
    });
  </script>
</body>

</html>