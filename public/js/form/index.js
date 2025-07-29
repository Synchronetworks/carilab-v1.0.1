function tableReload() {
    if ($.fn.DataTable && $.fn.DataTable.isDataTable('#datatable')) {
        $('#datatable').DataTable().ajax.reload();
    } else {
        setTimeout(function() {
            window.location.href = window.location.href; // Reload after 3 seconds
        }, 3000); 
    }
}
function getCsrfToken() {
  return $('meta[name="csrf-token"]').attr('content');
}
function handleAction(URL, method, confirmationMessage, successMessage) {
Swal.fire({
  title: confirmationMessage,
  icon: "warning",
  showCancelButton: true,
  confirmButtonColor: "#3085d6",
  cancelButtonColor: "#d33",
  confirmButtonText: "Yes"
}).then((result) => {
  if (result.isConfirmed) {
      $.ajax({
          url: URL,
          method: method,
          data: {
              _token: getCsrfToken()
          },
          dataType: 'json',
          success: function(res) {
              Swal.fire({
                  title: "Success!",
                  text: successMessage,
                  icon: "success"
              });
              tableReload();
          },
      });
  }
});
}

$(document).on('click', '.delete-tax', function(event) {
event.preventDefault();
const URL = $(this).attr('href');
handleAction(URL, 'DELETE', "Are you sure you want to delete ?", "Deleted Successfully!");
});

$(document).on('click', '.reject', function(event) {
    event.preventDefault();
    const URL = $(this).attr('href');
    handleAction(URL, 'DELETE', "Are you sure you want to reject ?", "Rejected Successfully!");
    });

$(document).on('click', '.approve', function(event) {
    event.preventDefault();
    const URL = $(this).attr('href');
    handleAction(URL, 'POST', "Are you sure you want to approve", "Approved Successfully!");
    });

$(document).on('click', '.restore-tax', function(event) {
event.preventDefault();
const URL = $(this).attr('href');

let confirmMessage = "Are you sure you want to restore ?";
let successMessage = "Restored Successfully!";

if ($(this).data('confirm-message')) {
    confirmMessage = $(this).data('confirm-message');
}

if ($(this).data('success-message')) {
    successMessage = $(this).data('success-message');
}

handleAction(URL, 'POST', confirmMessage, successMessage);
});

$(document).on('click', '.force-delete-tax', function(event) {
event.preventDefault();
const URL = $(this).attr('href');
handleAction(URL, 'DELETE', "Are you sure you want reject this Cellector?", "Entry permanently deleted!");
});

document.addEventListener("DOMContentLoaded", function() {
    function showSnackbar() {
        var snackbar = document.getElementById("snackbar");
  
        if (snackbar) {
            snackbar.classList.add("show");
            setTimeout(function() {
                 
                snackbar.classList.remove("show");
            }, 3000);
        }
    }
    showSnackbar();
});

function dismissSnackbar(event) {
  event.preventDefault(); // Prevent the default behavior of the anchor tag
  var snackbar = document.getElementById("snackbar");
  if (snackbar) {
      snackbar.style.display = "none"; // Hide the snackbar
  }
}
