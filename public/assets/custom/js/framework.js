function gen_grade(rating) {
  let grade = "";
  if (rating.toString().trim().length > 0) {
    rating = parseFloat(rating).toFixed(2);

    if (rating < 75) {
      grade = "5.0";
    } else if (rating == 75.0) {
      grade = "3.0";
    } else if (rating <= 76.0) {
      grade = "2.9";
    } else if (rating <= 77.0) {
      grade = "2.8";
    } else if (rating <= 78.0) {
      grade = "2.7";
    } else if (rating <= 79.0) {
      grade = "2.6";
    } else if (rating <= 80.0) {
      grade = "2.5";
    } else if (rating <= 81.0) {
      grade = "2.4";
    } else if (rating <= 82.0) {
      grade = "2.3";
    } else if (rating <= 83.0) {
      grade = "2.2";
    } else if (rating <= 84.0) {
      grade = "2.1";
    } else if (rating <= 85.0) {
      grade = "2.0";
    } else if (rating <= 86.0) {
      grade = "1.9";
    } else if (rating <= 87.0) {
      grade = "1.8";
    } else if (rating <= 88.0) {
      grade = "1.7";
    } else if (rating <= 89.0) {
      grade = "1.6";
    } else if (rating <= 90.0) {
      grade = "1.5";
    } else if (rating <= 92.0) {
      grade = "1.4";
    } else if (rating <= 94.0) {
      grade = "1.3";
    } else if (rating <= 96.0) {
      grade = "1.2";
    } else if (rating <= 98.0) {
      grade = "1.1";
    } else if (rating <= 100.0) {
      grade = "1.0";
    }
  }

  return grade;
}

function swal2GetData(url, button, data, callback) {
  $.ajax({
    type: "GET",
    url: url,
    data: data,
    dataType: "json",
    beforeSend: function () {
      $("#" + button).attr("disabled", "disabled");
      $("#" + button).prepend('<span class="spinner-border spinner-border-sm"></span>');
    },
    complete: function () {
      $("#" + button).removeAttr("disabled");
      $("#" + button)
        .find("span.spinner-border")
        .remove();
    },
    success: function (response) {
      Swal.fire({
        title: response.title,
        text: response.message,
        icon: "success",
        confirmButtonColor: "#4D62C5",
        confirmButtonText: "Ok",
      }).then((result) => {
        var data = {
          swal2: result,
          response: response,
        };

        if (typeof callback === "function") {
          callback(data);
        }
      });
    },
    error: function (xhr) {
      if (xhr.responseJSON) {
        Swal.fire({
          title: xhr.responseJSON.title,
          text: xhr.responseJSON.message,
          icon: "error",
          confirmButtonColor: "#4D62C5",
          confirmButtonText: "Ok",
        }).then((result) => {
          var data = {
            swal2: result,
            response: xhr.responseJSON,
          };

          if (typeof callback === "function") {
            callback(data);
          }
        });
      } else {
        Swal.fire({
          title: "Error " + xhr.status,
          text: xhr.statusText,
          icon: "error",
          confirmButtonColor: "#4D62C5",
          confirmButtonText: "Ok",
        }).then((result) => {
          var data = {
            swal2: result,
            response: xhr,
          };

          if (typeof callback === "function") {
            callback(data);
          }
        });
      }

      $("#" + button).removeAttr("disabled");
      $("#" + button)
        .find("span.spinner-border")
        .remove();
    },
  });
}

function swal2PostData(form, url, button, callback) {
  var $form = $("#" + form);

  $form.find('input[type="checkbox"]').each(function () {
    if (this.disabled) {
      $(this).prop("checked", false);
    }
  });

  var formData = $form
    .find("input, select, textarea")
    .filter(function () {
      return !this.disabled;
    })
    .serializeArray();

  var frmObj = {};

  $.each(formData, function (index, field) {
    if (!frmObj[field.name]) {
      frmObj[field.name] = field.value;
    } else {
      if (!Array.isArray(frmObj[field.name])) {
        frmObj[field.name] = [frmObj[field.name]];
      }
      frmObj[field.name].push(field.value);
    }
  });

  $.ajax({
    type: "POST",
    url: url,
    data: frmObj,
    dataType: "json",
    beforeSend: function () {
      $("#" + button).attr("disabled", "disabled");
      $("#" + button).prepend('<span class="spinner-border spinner-border-sm me-1"></span> ');
    },
    success: function (response) {
      Swal.fire({
        title: response.title,
        text: response.message,
        icon: "success",
        confirmButtonColor: "#4D62C5",
        confirmButtonText: "Ok",
      }).then((result) => {
        var data = {
          swal2: result,
          response: response,
        };

        if (typeof callback === "function") {
          callback(data);
        }
      });
    },
    error: function (xhr) {
      if (xhr.responseJSON) {
        Swal.fire({
          title: xhr.responseJSON.title,
          text: xhr.responseJSON.message,
          icon: "error",
          confirmButtonColor: "#4D62C5",
          confirmButtonText: "Ok",
        }).then((result) => {
          var data = {
            swal2: result,
            response: xhr.responseJSON,
          };

          if (typeof callback === "function") {
            callback(data);
          }
        });
      } else {
        if (xhr.status == 403) {
          Swal.fire({
            title: "Session Time Out",
            text: "Needs to reload the page",
            icon: "error",
            confirmButtonColor: "#4D62C5",
            confirmButtonText: "Ok",
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.reload();
            }
          });
        } else {
          Swal.fire({
            title: "Error " + xhr.status,
            text: xhr.statusText,
            icon: "error",
            confirmButtonColor: "#4D62C5",
            confirmButtonText: "Ok",
          }).then((result) => {
            var data = {
              swal2: result,
              response: xhr,
            };

            if (typeof callback === "function") {
              callback(data);
            }
          });
        }
      }
    },
  }).always(function () {
    $("#" + button).removeAttr("disabled");
    $("#" + button)
      .find("span.spinner-border")
      .remove();
  });
}

function toastPostData(form, url, button, callback) {
  toastr.options = {
    closeButton: false,
    debug: false,
    newestOnTop: true,
    progressBar: false,
    positionClass: "toast-top-right",
    preventDuplicates: false,
    onclick: null,
    showDuration: "300",
    hideDuration: "1000",
    timeOut: "5000",
    extendedTimeOut: "1000",
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut",
  };

  var formData = $("#" + form).serializeArray();

  var frmObj = {};
  $.each(formData, function (index, field) {
    if (!frmObj[field.name]) {
      frmObj[field.name] = field.value;
    } else {
      if (!Array.isArray(frmObj[field.name])) {
        frmObj[field.name] = [frmObj[field.name]];
      }
      frmObj[field.name].push(field.value);
    }
  });

  $.ajax({
    type: "POST",
    url: url,
    data: frmObj,
    dataType: "json",
    beforeSend: function () {
      $("#" + button).attr("disabled", "disabled");
      $("#" + button).prepend('<span class="spinner-border spinner-border-sm me-1"></span> ');
    },
    success: function (response) {
      toastr["success"](response.message);

      var data = {
        response: response,
      };

      if (typeof callback === "function") {
        callback(data);
      }
    },
    complete: function () {
      $("#" + button).removeAttr("disabled");
      $("#" + button)
        .find("span.spinner-border")
        .remove();
    },
    error: function (xhr) {
      if (xhr.responseJSON) {
        toastr["error"](xhr.responseJSON.message);

        var data = {
          response: xhr.responseJSON,
        };

        if (typeof callback === "function") {
          callback(data);
        }
      } else {
        toastr["error"](xhr.message);

        var data = {
          response: xhr,
        };

        if (typeof callback === "function") {
          callback(data);
        }
      }

      $("#" + button).removeAttr("disabled");
      $("#" + button)
        .find("span.spinner-border")
        .remove();
    },
  });
}

function check_fields(frm) {
  var valid = true;
  var req_fields = "";
  $("#toastAlert").removeClass();

  $("#" + frm + " [required]").each(function () {
    if ($(this).val().trim() === "") {
      req_fields += "<br/>" + $(this).attr("title");
      valid = false;
    }
  });

  if (!valid) {
    Swal.fire({
      title: "Required Fields",
      html: "The following fields are required:<br/>" + req_fields,
      confirmButtonColor: "#4D62C5",
      icon: "warning",
    });
  }
  return valid;
}

function toast_check_fields(frm) {
  toastr.options = {
    closeButton: false,
    debug: false,
    newestOnTop: true,
    progressBar: false,
    positionClass: "toast-top-right",
    preventDuplicates: false,
    onclick: null,
    showDuration: "300",
    hideDuration: "1000",
    timeOut: "5000",
    extendedTimeOut: "1000",
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut",
  };

  var valid = true;
  var req_fields = "";

  $("#" + frm + " [required]").each(function () {
    if ($(this).val().trim() === "") {
      // Trim the value to handle whitespace
      req_fields += "</br> - " + $(this).attr("title");
      valid = false;
    }
  });

  if (!valid) {
    toastr["error"]("The following fields are required: " + req_fields);
  }

  return valid;
}

function sorting(fld) {
  if ($("#sortby").val() == fld) {
    if ($("#sortorder").val() == "asc") {
      $("#sortorder").val("desc");
    } else {
      $("#sortorder").val("asc");
    }
  } else {
    $("#sortby").val(fld);
    $("#sortorder").val("asc");
  }
  $("#frmFilter").submit();
}

$("#clear").on("click", function () {
  $(".form-control").each(function (index, element) {
    $(element).val("");
    console.log(element);
  });
  $("#limit").val(8);
  $("#frmFilter").submit();
});

$("#limit").on("change", function () {
  $("#frmFilter").submit();
});

function clear_filter(filterGroup) {
  $(filterGroup)
    .find(".form-control")
    .each(function () {
      $(this).val("");
    });
  $(filterGroup).closest("form").submit();
}

function printlist(url) {
  var popupWidth = 800;
  var popupHeight = 800;

  var leftPosition = (window.screen.width - popupWidth) / 2;
  var topPosition = (window.screen.height - popupHeight) / 2;
  window.open(url, "popup", "width=" + popupWidth + ",height=" + popupHeight + ",left=" + leftPosition + ",top=" + topPosition);
}

function cancel_confirmation(url, title = "", message = "", type = "") {
  if (!type) {
    type = "warning";
  }
  if (!title) {
    title = "Exit Page?";
  }
  if (!message) {
    message = "Are you sure do you want to exit?";
  }

  Swal.fire({
    title: title,
    text: message,
    icon: type,
    showCancelButton: true,
    confirmButtonColor: "#4D62C5",
    cancelButtonColor: "#636678",
    confirmButtonText: "Yes",
  }).then((result) => {
    if (result.isConfirmed) {
      window.location = url;
    }
  });
}

function popUp(url, width, height) {
  const left = (window.innerWidth - width) / 2;
  const top = (window.innerHeight - height) / 2;

  window.open(url, "Popup", `width=${width}, height=${height}, left=${left}, top=${top}`);
}

// function popUp(pageURL, pageTitle) {
//   let width = 1000;
//   let height = 1000;

//   let left = (screen.width - width) / 2;
//   let top = (screen.height - height) / 2;

//   let myWindow = window.open(
//     pageURL,
//     pageTitle,
//     "resizable=yes, width=" + width + ", height=" + height + ", top=" + top + ", left=" + left
//   );
// }

/**
 *
 * Extended functions
 */

function swal2GetDataExtended(url, button, data, callback) {
  $.ajax({
    type: "GET",
    url: url,
    data: data,
    dataType: "json",
    beforeSend: function () {
      $("#" + button).attr("disabled", "disabled");
      $("#" + button).prepend('<span class="spinner-border spinner-border-sm me-1"></span>');
    },
    success: function (response) {
      Swal.fire({
        title: response.title,
        text: response.message,
        icon: "success",
        confirmButtonColor: "#4D62C5",
        confirmButtonText: "Ok",
      }).then((result) => {
        var data = {
          swal2: result,
          response: response,
        };

        if (typeof callback === "function") {
          callback(data);
        }
      });
    },
    error: function (xhr) {
      if (xhr.responseJSON) {
        Swal.fire({
          title: xhr.responseJSON.title,
          text: xhr.responseJSON.message,
          icon: "error",
          confirmButtonColor: "#4D62C5",
          confirmButtonText: "Ok",
        }).then((result) => {
          var data = {
            swal2: result,
            response: xhr.responseJSON,
          };

          if (typeof callback === "function") {
            callback(data);
          }
        });
      } else {
        Swal.fire({
          title: "Error " + xhr.status,
          text: xhr.statusText,
          icon: "error",
          confirmButtonColor: "#4D62C5",
          confirmButtonText: "Ok",
        }).then((result) => {
          var data = {
            swal2: result,
            response: xhr,
          };

          if (typeof callback === "function") {
            callback(data);
          }
        });
      }
    },
  }).always(function () {
    $("#" + button).removeAttr("disabled");
    $("#" + button)
      .find("span.spinner-border")
      .remove();
  });
}

function swal2PostDataExtended(url, button, data, callback) {
  $.ajax({
    type: "POST",
    url: url,
    data: data,
    dataType: "json",
    beforeSend: function () {
      $("#" + button).attr("disabled", "disabled");
      $("#" + button).prepend('<span class="spinner-border spinner-border-sm me-1"></span> ');
    },
    success: function (response) {
      Swal.fire({
        title: response.title,
        text: response.message,
        icon: "success",
        confirmButtonColor: "#4D62C5",
        confirmButtonText: "Ok",
      }).then((result) => {
        var data = {
          swal2: result,
          response: response,
        };

        if (typeof callback === "function") {
          callback(data);
        }
      });
    },
    error: function (xhr) {
      if (xhr.responseJSON) {
        Swal.fire({
          title: xhr.responseJSON.title,
          text: xhr.responseJSON.message,
          icon: "error",
          confirmButtonColor: "#4D62C5",
          confirmButtonText: "Ok",
        }).then((result) => {
          var data = {
            swal2: result,
            response: xhr.responseJSON,
          };

          if (typeof callback === "function") {
            callback(data);
          }
        });
      } else {
        if (xhr.status == 403) {
          Swal.fire({
            title: "Request not allowed",
            text: "The action you requested is not allowed",
            icon: "error",
            confirmButtonColor: "#4D62C5",
            confirmButtonText: "Ok",
          });
        } else {
          Swal.fire({
            title: "Error " + xhr.status,
            text: xhr.statusText,
            icon: "error",
            confirmButtonColor: "#4D62C5",
            confirmButtonText: "Ok",
          }).then((result) => {
            var data = {
              response: xhr,
              swal2: result,
            };

            if (typeof callback === "function") {
              callback(data);
            }
          });
        }
      }
    },
  }).always(function () {
    $("#" + button).removeAttr("disabled");
    $("#" + button)
      .find("span.spinner-border")
      .remove();
  });
}

function toastPostDataExtended(url, button, data, callback) {
  toastr.options = {
    closeButton: false,
    debug: false,
    newestOnTop: true,
    progressBar: false,
    positionClass: "toast-top-right",
    preventDuplicates: false,
    onclick: null,
    showDuration: "300",
    hideDuration: "1000",
    timeOut: "5000",
    extendedTimeOut: "1000",
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut",
  };

  $.ajax({
    type: "POST",
    url: url,
    data: data,
    dataType: "json",
    beforeSend: function () {
      $("#" + button).attr("disabled", "disabled");
      $("#" + button).prepend('<span class="spinner-border spinner-border-sm me-1"></span>');
    },
    success: function (response) {
      // toastr["success"](response.message);
      var data = {
        response: response,
      };

      if (typeof callback === "function") {
        callback(data);
      }
    },
    error: function (xhr) {
      if (xhr.responseJSON) {
        toastr["error"](xhr.responseJSON.message);
        var data = {
          response: xhr.responseJSON,
        };

        if (typeof callback === "function") {
          callback(data);
        }
      } else {
        if (xhr.status == 403) {
          toastr["error"]("Session Tim Out.");
          window.location.reload();
        } else {
          toastr["error"](xhr.message);
          var data = {
            response: xhr,
          };
          if (typeof callback === "function") {
            callback(data);
          }
        }
      }
    },
  }).always(function () {
    $("#" + button).removeAttr("disabled");
    $("#" + button)
      .find("span.spinner-border")
      .remove();
  });
}

function toastGetDataExtended(url, button, data, callback) {
  toastr.options = {
    closeButton: false,
    debug: false,
    newestOnTop: true,
    progressBar: false,
    positionClass: "toast-top-right",
    preventDuplicates: false,
    onclick: null,
    showDuration: "300",
    hideDuration: "1000",
    timeOut: "5000",
    extendedTimeOut: "1000",
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut",
  };

  $.ajax({
    type: "GET",
    url: url,
    data: data,
    dataType: "json",
    beforeSend: function () {
      $("#" + button).attr("disabled", "disabled");
      $("#" + button).prepend('<span class="spinner-border spinner-border-sm me-1"></span> ');
    },
    success: function (response) {
      // toastr["success"](response.message);

      var data = {
        response: response,
      };

      if (typeof callback === "function") {
        callback(data);
      }
    },
    complete: function () {
      $("#" + button).removeAttr("disabled");
      $("#" + button)
        .find("span.spinner-border")
        .remove();
    },
    error: function (xhr) {
      if (xhr.responseJSON) {
        toastr["error"](xhr.responseJSON.message);

        var data = {
          response: xhr.responseJSON,
        };

        if (typeof callback === "function") {
          callback(data);
        }
      } else {
        if (xhr.status == 403) {
          toastr["error"]("Request not allowed");
        } else {
          toastr["error"](xhr.message);

          var data = {
            response: xhr,
          };

          if (typeof callback === "function") {
            callback(data);
          }
        }
      }

      $("#" + button).removeAttr("disabled");
      $("#" + button)
        .find("span.spinner-border")
        .remove();
    },
  }).always(function () {
    $("#" + button).removeAttr("disabled");
    $("#" + button)
      .find("span.spinner-border")
      .remove();
  });
}

$(".js-example-basic-single").select2();
