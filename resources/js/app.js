import 'select2';

(function(){
  "use strict";
  const isRTL = document.documentElement.getAttribute('dir') === 'rtl';
  customSlider(isRTL);

  $(document).on('change', '.datatable-filter [data-filter="select"]', function() {
    window.renderedDataTable.ajax.reload(null, false)
  })

  $(document).on('input', '.dt-search', function() {
    window.renderedDataTable.ajax.reload(null, false)
  })

  const confirmSwal = async (message) => {
    return await Swal.fire({
      title: message,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#858482',
      confirmButtonText: 'Yes, do it!',
      showClass: {
        popup: 'animate__animated animate__zoomIn'
      },
      hideClass: {
        popup: 'animate__animated animate__zoomOut'
      }
    }).then((result) => {
      return result
    })
  }

  window.confirmSwal = confirmSwal

  $('#quick-action-form').on('submit', function(e) {
    e.preventDefault()
    const form = $(this)
    const url = form.attr('action')
    const message = $('[name="message_'+$('[name="action_type"]').val()+'"]').val()
    const rowdIds = $("#datatable_wrapper .select-table-row:checked").map(function() {
        return $(this).val();
    }).get();

    confirmSwal(message).then((result) => {
      if(!result.isConfirmed) return
      callActionAjax({url: `${url}?rowIds=${rowdIds}`,body: form.serialize()})
      //
    })
  })

  // Update status on switch
  $(document).on('change', '#datatable_wrapper .switch-status-featured', function() {
    let url = $(this).attr('data-url')
    let body = {
      featured: $(this).prop('checked') ? 1 : 0,
      _token: $(this).attr('data-token')
    }
    callActionAjax({url: url, body: body})
  })

    // Update status on switch
    $(document).on('change', '#datatable_wrapper .switch-status-change', function() {
      let url = $(this).attr('data-url')
      let body = {
        status: $(this).prop('checked') ? 1 : 0,
        _token: $(this).attr('data-token')
      }
      callActionAjax({url: url, body: body})
    })


  $(document).on('change', '#datatable_wrapper .change-select', function() {
    let url = $(this).attr('data-url')
    let body = {
      value: $(this).val(),
      _token: $(this).attr('data-token')
    }
    callActionAjax({url: url, body: body})
  })

  function callActionAjax ({url, body}) {
    $.ajax({
      type: 'POST',
      url: url,
      data: body,
      success: function(res) {
        if (res.status) {
          window.successSnackbar(res.message)
          window.renderedDataTable.ajax.reload(resetActionButtons, false)
          const event = new CustomEvent('update_quick_action', {detail: {value: true}})
          document.dispatchEvent(event)
        } else {
          Swal.fire({
            title: 'Error',
            text: res.message,
            icon: "error",
            showClass: {
              popup: 'animate__animated animate__zoomIn'
            },
            hideClass: {
              popup: 'animate__animated animate__zoomOut'
            }
          })
          // window.errorSnackbar(res.message)
        }
      }
    })
  }

  // Update status on button click
  $(document).on('click', '#datatable_wrapper .button-status-change', function() {

    let url = $(this).attr('data-url')
    let body = {
      status: 1,
      _token: $(this).attr('data-token')
    }
    callActionAjax({url: url, body: body})
  })

  function callActionAjax ({url, body}) {
    $.ajax({
      type: 'POST',
      url: url,
      data: body,
      success: function(res) {
        if (res.status) {
          window.successSnackbar(res.message)
          window.renderedDataTable.ajax.reload(resetActionButtons, false)
          const event = new CustomEvent('update_quick_action', {detail: {value: true}})
          document.dispatchEvent(event)
        } else {
          window.errorSnackbar(res.message)
        }
      }
    })
  }

  //select row in datatable
  const dataTableRowCheck = (id, source = null) => {
    var dataType = source ? source.getAttribute('data-type') : null;
    checkRow();
    const actionDropdown = document.getElementById('quick-action-type');
    if ($(".select-table-row:checked").length > 0) {
        $("#quick-action-form").removeClass('form-disabled');
        //if at-least one row is selected
        document.getElementById("select-all-table").indeterminate = true;
        $("#quick-actions").find("input, textarea, button, select").removeAttr("disabled");
    } else {
        //if no row is selected
        document.getElementById("select-all-table").indeterminate = false;
        $("#select-all-table").attr("checked", false);
        resetActionButtons();
    }

    if ($("#datatable-row-" + id).is(":checked")) {
        $("#row-" + id).addClass("table-active");
    } else {
        $("#row-" + id).removeClass("table-active");
    }

    const rowdIds = $("#datatable_wrapper .select-table-row:checked").map(function() {
      return $(this).val();
    }).get();

    if(dataType !== null){

      if(dataType === 'review'){
        if (actionDropdown.options[2] !== undefined) {
          actionDropdown.options[2].disabled = true;  // Restore option
        }
        if (actionDropdown.options[3] !== undefined) {
          actionDropdown.options[3].disabled = true;  // Permanently Delete option
        }
        if (actionDropdown.options[1] !== undefined) {
          actionDropdown.options[1].disabled = false;
        }
      }else{
        if (actionDropdown.options[3] !== undefined) {
          actionDropdown.options[3].disabled = true;  // Restore option
        }
        if (actionDropdown.options[4] !== undefined) {
          actionDropdown.options[4].disabled = true;  // Permanently Delete option
        }
        if (actionDropdown.options[2] !== undefined) {
          actionDropdown.options[2].disabled = false;
        }
      }

    }


    const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
      type: 'POST',
      headers: {
        'X-CSRF-Token': csrfToken,
    },
      url: baseUrl + "/app/check-in-trash",
      data: { ids: rowdIds, datatype: dataType },
      success: function(response) {
          if(response.all_in_trash == true){

            if(dataType === 'review'){
                actionDropdown.options[2].disabled = false;  // Restore option
                actionDropdown.options[3].disabled = false;  // Permanently Delete option
                actionDropdown.options[1].disabled = true;
            }else{
              actionDropdown.options[3].disabled = false; // Restore option
              actionDropdown.options[4].disabled = false; // Permanently Delete option
              actionDropdown.options[2].disabled = true;
            }
          }
      }
    });
    checkRow();
  };
  window.dataTableRowCheck = dataTableRowCheck

  const selectAllTable = (source) => {
    var dataType = source.getAttribute('data-type');
    const checkboxes = document.getElementsByName("datatable_ids[]");
    const actionDropdown = document.getElementById('quick-action-type');
    const selectedIds = [];
    for (var i = 0, n = checkboxes.length; i < n; i++) {
        // if disabled property is given to checkbox, it won't select particular checkbox.
        if (!$("#" + checkboxes[i].id).prop('disabled')){
            checkboxes[i].checked = source.checked;
            if (checkboxes[i].checked) {
              selectedIds.push(checkboxes[i].value);
            }else{
              document.getElementById("select-all-table").indeterminate = false;
              $("#select-all-table").attr("checked", false);
              resetActionButtons();
            }
        }
      
    }
    if(dataType !== null){
      if(dataType === 'review'){

        if (actionDropdown.options[2] !== undefined) {
          actionDropdown.options[2].disabled = true;  // Restore option
        }
        if (actionDropdown.options[3] !== undefined) {
          actionDropdown.options[3].disabled = true;  // Permanently Delete option
        }
        if (actionDropdown.options[1] !== undefined) {
          actionDropdown.options[1].disabled = false;
        }
      }else{
        if (actionDropdown.options[3] !== undefined) {
          actionDropdown.options[3].disabled = true;  // Restore option
        }
        if (actionDropdown.options[4] !== undefined) {
          actionDropdown.options[4].disabled = true;  // Permanently Delete option
        }
        if (actionDropdown.options[2] !== undefined) {
          actionDropdown.options[2].disabled = false;
        }
      }
    }

    const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
      type: 'POST',
      headers: {
        'X-CSRF-Token': csrfToken,
    },
      url: baseUrl + "/app/check-in-trash",
      data: { ids: selectedIds, datatype: dataType },
      success: function(response) {
          if(response.all_in_trash == true){

            if(dataType === 'review'){
              actionDropdown.options[2].disabled = false;  // Restore option
              actionDropdown.options[3].disabled = false;  // Permanently Delete option
              actionDropdown.options[1].disabled = true;
            }else{
              actionDropdown.options[3].disabled = false; // Restore option
              actionDropdown.options[4].disabled = false; // Permanently Delete option
              actionDropdown.options[2].disabled = true;
            }

          }
      }
    });


    checkRow();
};


  window.selectAllTable = selectAllTable

  const checkRow = () => {
    if ($(".select-table-row:checked").length > 0) {
      $("#quick-action-type").prop('disabled', false);
      $("#quick-action-form").removeClass('form-disabled');
      // $("#quick-action-apply").removeClass("btn-primary").addClass("btn-primary");
    } else {
      $("#quick-action-type").prop('disabled', true);
      $("#quick-action-form").addClass('form-disabled');
      document.getElementById("select-all-table").indeterminate = false;
      // $("#quick-action-apply").removeClass("btn-primary").addClass("btn-primary");
    }
  }

  window.checkRow = checkRow

  //reset table action form elements
  const resetActionButtons = () => {
    checkRow()
    const quickActionForm = $("#quick-action-form")[0];
    if(document.getElementById("select-all-table") !== undefined && document.getElementById("select-all-table") !== null) {
      document.getElementById("select-all-table").checked = false;
      if (quickActionForm !== undefined && quickActionForm !== null) {
        quickActionForm.reset();  // Only reset if the form exists
    }
      $("#quick-actions")
          .find("input, textarea, button, select")
          .attr("disabled", "disabled");
      $("#quick-action-form").find("select").select2("destroy").select2().val(null).trigger("change")
    }
  };

  window.resetActionButtons = resetActionButtons



  const initDatatable = ({url, finalColumns, advanceFilter, drawCallback = undefined, orderColumn}) => {

    const data_table_limit = $('meta[name="data_table_limit"]').attr('content');

    window.renderedDataTable = $('#datatable').DataTable({
      processing: true,
      serverSide: true,
      autoWidth: false,
      responsive: true,
      fixedHeader: true,
      lengthMenu: [
        [5, 10, 15, 20, 25, 100, -1],
        [5, 10, 15, 20, 25, 100, 'All'],
      ],
      order: orderColumn,
      pageLength : data_table_limit,
      language: {
        emptyTable: "No Data Found",
        zeroRecords: "No matching records found",
        // processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
        search: "Search:",
        lengthMenu: "Show _MENU_ entries",
        info: "Showing _START_ to _END_ of _TOTAL_ entries",
        infoEmpty: "Showing 0 to 0 of 0 entries",
        infoFiltered: "(filtered from _MAX_ total entries)",
        paginate: {
            first: "First",
            last: "Last",
            next: "Next",
            previous: "Previous"
        }
    },
      dom: '<"row align-items-center"><"table-responsive my-3 mt-3 mb-2" rt><"row align-items-center data_table_widgets mt-3" <"col-md-6" <"d-flex align-items-center flex-wrap gap-3" l i>><"col-md-6" p>><"clear">',
      ajax: {
        "type"   : "GET",
        "url"    : url,
        "data"   : function( d ) {
          d.search = {
            value: $('.dt-search').val()
          };
          d.filter = {
            column_status: $('#column_status').val()
          }
          if(typeof advanceFilter == 'function' && advanceFilter() !== undefined) {
            d.filter = {...d.filter,...advanceFilter()}
          }
        },
      },
      drawCallback: function() {
          if(laravel !== undefined) {
              window.laravel.initialize();
          }
          // $('.select2').select2();
          if(drawCallback !== undefined && typeof drawCallback == 'function') {
            drawCallback()
          }
      },
      columns: finalColumns,
     });
  }

window.initDatatable = initDatatable;


  // window.initDatatable = initDatatable

  function formatCurrency(number, noOfDecimal, decimalSeparator, thousandSeparator, currencyPosition, currencySymbol) {
    // Convert the number to a string with the desired decimal places
    let formattedNumber = number.toFixed(noOfDecimal)

    // Split the number into integer and decimal parts
    let [integerPart, decimalPart] = formattedNumber.split('.')

    // Add thousand separators to the integer part
    integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, thousandSeparator)

    // Set decimalPart to an empty string if it is undefined
    decimalPart = decimalPart || ''

    // Construct the final formatted currency string
    let currencyString = ''

    if (currencyPosition === 'left' || currencyPosition === 'left_with_space') {
      currencyString += currencySymbol
      if (currencyPosition === 'left_with_space') {
        currencyString += ' '
      }
      currencyString += integerPart
      // Add decimal part and decimal separator if applicable
      if (noOfDecimal > 0) {
        currencyString += decimalSeparator + decimalPart
      }
    }

    if (currencyPosition === 'right' || currencyPosition === 'right_with_space') {
      // Add decimal part and decimal separator if applicable

      if (noOfDecimal > 0) {
        currencyString += integerPart + decimalSeparator + decimalPart
      }
      if (currencyPosition === 'right_with_space') {
        currencyString += ' '
      }
      currencyString += currencySymbol
    }

    return currencyString
  }

  window.formatCurrency = formatCurrency

})()
  // custom slider
  function customSlider(isRTL) {
    if (document.querySelectorAll(".custom-tab-slider").length) {
        const sliders = document.querySelectorAll('.custom-tab-slider');
  
        function slide(direction, e) {
            const container = e.target.closest("div").parentElement.getElementsByClassName("custom-tab-slider");
            const parent = e.target.closest("div").parentElement;
            slidescroll(container, direction, parent);
        }
  
        function slidescroll(container, direction, parent, is_vertical = false) {
            let scrollCompleted = 0;
            const rightArrow = parent ? parent.getElementsByClassName("right")[0] : null;
            const leftArrow = parent ? parent.getElementsByClassName("left")[0] : null;
            const maxScroll = parent ? container[0].scrollWidth - container[0].offsetWidth - 30 : null;
  
            const slideVar = setInterval(() => {
                if (direction === 'left') {
                    if (is_vertical) {
                        container[0].scrollTop -= 5;
                    } else {
                        container[0].scrollLeft -= 20;
                    }
                    if (parent) {
                        rightArrow.style.display = "block";
                        if (container[0].scrollLeft === 0)
                            leftArrow.style.display = "none";
                    }
                } else {
                    if (is_vertical) {
                        container[0].scrollTop += 5;
                    } else {
                        container[0].scrollLeft += 20;
                    }
                    if (parent) {
                        leftArrow.style.display = "block";
                        if (container[0].scrollLeft > maxScroll)
                            rightArrow.style.display = "none";
                    }
                }
                scrollCompleted += 10;
                if (scrollCompleted >= 100) {
                    clearInterval(slideVar);
                }
            }, 40);
        }
  
        function enableSliderNav() {
            sliders.forEach((element) => {
                const left = element.parentElement.querySelector(".left");
                const right = element.parentElement.querySelector(".right");
  
                if (element.scrollWidth - element.clientWidth > 0) {
                    right.style.display = "block";
                    left.style.display = "block";
                } 
  
                // Attach event listeners to the left and right arrows
                if (left && right) {
                    left.addEventListener('click', (e) => slide('left', e));
                    right.addEventListener('click', (e) => slide('right', e));
                }
            });
        }
  
        function slideDrag(eslider) {
            let isDown = false;
            let startX;
            let scrollLeft;
            const maxScroll = eslider.scrollWidth - eslider.clientWidth - 20;
            const rightArrow = eslider.parentElement.getElementsByClassName("right")[0];
            const leftArrow = eslider.parentElement.getElementsByClassName("left")[0];
  
            eslider.addEventListener('mousedown', (e) => {
                isDown = true;
                eslider.classList.add('active');
                startX = e.pageX - eslider.offsetLeft;
                scrollLeft = eslider.scrollLeft;
            });
  
            eslider.addEventListener('mouseleave', () => {
                isDown = false;
                eslider.classList.remove('active');
            });
  
            eslider.addEventListener('mouseup', () => {
                isDown = false;
                eslider.classList.remove('active');
            });
  
            eslider.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - eslider.offsetLeft;
                const walk = (x - startX) * 3; //scroll-fast
                eslider.scrollLeft = scrollLeft - walk;
  
                if (eslider.scrollLeft > maxScroll) {
                    rightArrow.style.display = "none";
                } else {
                    leftArrow.style.display = eslider.scrollLeft === 0 ? "none" : "block";
                    rightArrow.style.display = "block";
                }
            });
        }
  
        // Initialize slider drag and navigation
        sliders.forEach((element) => {
            slideDrag(element);
        });
        enableSliderNav();
  
        // Re-enable navigation on resize
        window.addEventListener('resize', enableSliderNav);
    }
  }
  