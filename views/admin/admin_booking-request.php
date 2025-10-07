<?php include_once '../../include/header.php'; ?>

<div class="content_wrapper">
  <div class="app_sidebar_container d-flex">
    <div class="app_sidebar_nav app_sidebar_bg_it_asset d-flex flex-column justify-content-between sidebar-hidden" id="app_sidebar_nav"></div>
    <div class="app_content_container">
      <nav class="navbar navbar-expand px-3 border-bottom" style="background-color: #C0967E;">
        <button class="btn app_open_sidebar_btn" type="button">
          <span class="navbar-toggler-icon"></span>
        </button>
        <span class="app_content_title fs-25 fw-bold pe-2">Booking Request</span>
        <div class="ms-auto d-flex align-items-center">
          <!-- Notification Bell with Dropdown -->
          <div class="dropdown" id="notificationDropdownWrapper">
            <button class="btn position-relative me-2" style="background: none;" id="notificationBell">
              <i class="fa-regular fa-bell fs-5"></i>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationBadge" style="font-size:10px;">
                3
              </span>
            </button>
            <div class="dropdown-menu dropdown-menu-end p-0 shadow" style="min-width: 340px; max-width: 400px; max-height: 400px; overflow-y: auto;" id="notificationDropdown">
              <div class="d-flex justify-content-between align-items-center px-3 pt-2 pb-1 border-bottom">
                <span class="fw-bold">Notifications</span>
                <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2" id="clearReadBtn" style="font-size: 0.85rem;">Clear Read</button>
              </div>
              <ul class="list-group list-group-flush" id="notificationList" style="cursor:pointer;">
                <!-- Notifications will be injected here -->
              </ul>
            </div>
          </div>
          <button class="btn" style="background: none;">
            <i class="pe-2 bi bi-person-square fs-5"></i>Admin
          </button>
        </div>
      </nav>
      <div class="app_content_body">
        <!-- Booking requests will be loaded here dynamically -->
        <div class="p-3">
          <table id="bookingRequestTable" style="width:100%;">
            <tbody></tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>

<div id="modalContainer"></div>
<?php include_once '../../include/footer.php';
include_once '../../helper/admin_apps.php' ?>
<script>
  setTimeout(function() {
    $('#admin_booking_request').addClass('active');
  }, 500);

  loadBookingRequests();

  function loadBookingRequests() {
    if ($.fn.DataTable.isDataTable('#bookingRequestTable')) {
      $('#bookingRequestTable').DataTable().clear().destroy();
    }

    let booking_request_table = $('#bookingRequestTable').DataTable({
      ajax: {
        url: '../../controller/admin_dashboard_contr.php',
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'load_booking_requests',
          status: 'Pending'
        },
        dataSrc: function(json) {
          console.log('📊 Booking requests received:', json);
          console.log('📊 Total records:', json ? json.length : 0);
          
          if (!json || json.length === 0) {
            console.warn('⚠️ No pending booking requests found');
            $('#bookingRequestTable').html(`
              <div class="card shadow-sm border rounded-3 mb-3">
                <div class="card-body text-center p-4">
                  <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
                  <h5 class="mt-3 text-muted">No Booking Requests</h5>
                  <p class="text-muted">There are no pending booking requests at the moment.</p>
                </div>
              </div>
            `);
          } else {
            console.log('✅ Displaying ' + json.length + ' booking requests');
          }
          return json;
        },
        error: function(xhr, status, error) {
          console.error('❌ AJAX Error loading booking requests:', {xhr, status, error});
          console.error('Response text:', xhr.responseText);
        }
      },
      columns: [{
        data: null,
        render: function(row) {
          let profileImage = row.profile_picture ?
            `<img src="${row.profile_picture}" alt="Profile" class="rounded-circle" style="width:72px; height:72px; object-fit:cover;">` :
            `<i class="bi bi-person-circle user-avatar" style="font-size:4.5rem; min-height:72px; min-width:72px; display:flex; align-items:center; justify-content:center;"></i>`;

          return `
          <div class="card shadow-sm border rounded-3 mb-3">
            <div class="card-body">
              <div class="row g-3 align-items-center">
                <!-- Image -->
                <div class="col-auto d-flex align-items-center justify-content-center">
                  ${profileImage}
                </div>
                <!-- Info -->
                <div class="col">
                  <div class="fw-semibold user-name">${row.user_name}</div>
                  <div class="small text-muted mb-1">Booking ID: #${row.bookingdetailsid}</div>
                  <div class="d-flex flex-wrap gap-2 small mt-1">
                    <span class="badge bg-light text-dark border px-2 py-1">
                      <i class="bi bi-briefcase me-1"></i>${row.services_name}
                    </span>
                    <span class="badge bg-light text-dark border px-2 py-1">
                      <i class="bi bi-calendar-event me-1"></i>${row.booking_date}
                    </span>
                    <span class="badge bg-success text-white px-2 py-1">
                      ₱${row.price}
                    </span>
                  </div>
                </div>
                <!-- Actions -->
                <div class="col-auto d-flex gap-2">
                  <button class="btn btn-primary btn-sm  px-3" onclick="viewBookingDetails('${row.bookingdetailsid}');">View</button>
                  <button class="btn btn-secondary btn-sm  px-3" onclick="declineRequest('${row.bookingdetailsid}');">Decline</button>
                  <button class="btn btn-success btn-sm  px-3" onclick="acceptRequest('${row.bookingdetailsid}');">Accept</button>
                </div>
              </div>
            </div>
          </div>`;
        }
      }],
      paging: true,
      pageLength: 5,
      searching: true,
      ordering: false,
      info: false,
      dom: '<"top"f>rt<"bottom"p>',
    });
    booking_request_table.on('draw', function() {
      setTimeout(function() {
        $('[data-bs-toggle="tooltip"]').tooltip(); //* ======== Initialize tooltip ========
        $('[id^="tooltip"]').remove(); //* ======== Remove tooltip every table draw ========
        $('[data-bs-toggle="tooltip"]').on('click', function() { //* ======= Hide tooltip upon click =======
          $(this).tooltip('hide');
        });
      }, 1000);
    });
    setInterval(function() {
      booking_request_table.ajax.reload(null, false); //* ======= Reload Table Data Every X seconds with pagination retained =======
    }, 30000);
  }

  function viewBookingDetails(bookingdetailsid) {
    showGlobalModal('../../views/modal/admin_modal-booking-details.php');
    $.ajax({
      url: '../../controller/admin_dashboard_contr.php',
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'get_booking_details',
        bookingdetailsid: bookingdetailsid
      },
      success: result => {
        console.log('Booking details result:', result);
        $('.declinefrommodal').val(bookingdetailsid);
        $('.acceptfrommodal').val(bookingdetailsid);

        $('#detail-booking-id').text(bookingdetailsid);
        $('#detail-user-name').text(result.user_name);
        $('#detail-user-email').text(result.email);
        $('#detail-user-phone').text(result.contact);
        $('#detail-booking-id').text(result.bookingdetailsid);
        $('#detail-booking-date').text(result.date_schedule);
        $('#detail-booking-time').text(result.time_schedule);
        $('#detail-booking-status').text(result.booking_status);
        $('#detail-total-price').text(result.totalprice);
        $('#service-name').text(result.services_name);
        $('#service-description').text(result.description);
        $('#service-duration').text(result.service_duration);
        $('#service-quantity').text(result.service_quantity);
        $('#service-price').text(result.serviceprice);
        $('#detail-payment-img').attr('src', result.payment_img);
      }

    });

  }

  function declineRequest(bokkingid) {
    Swal.fire({
      title: 'Are you sure?',
      text: "You are about to decline this booking request.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, decline it!'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: '../../controller/admin_dashboard_contr.php',
          type: 'POST',
          dataType: 'json',
          data: {
            action: 'decline_booking_request',
            bookingdetailsid: bokkingid
          },
          success: result => {
            if (result.status === 'success') {
              Swal.fire(
                'Declined!',
                'Booking request has been declined.',
                'success'
              );
              loadBookingRequests();
              $('#globalModal').modal('hide');
            } else {
              Swal.fire(
                'Error!',
                'Failed to decline booking request. Please try again.',
                'error'
              );
            }
          },
          error: () => {
            Swal.fire(
              'Error!',
              'Network error. Please try again.',
              'error'
            );
          }
        });
      }
    });
  }

  function acceptRequest(bookingid) {
    Swal.fire({
      title: 'Are you sure?',
      text: "You are about to accept this booking request.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, accept it!'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: '../../controller/admin_dashboard_contr.php',
          type: 'POST',
          dataType: 'json',
          data: {
            action: 'accept_booking_request',
            bookingdetailsid: bookingid
          },
          success: result => {
            if (result.status === 'success') {
              Swal.fire(
                'Accepted!',
                'Booking request has been accepted.',
                'success'
              );
              loadBookingRequests();
              $('#globalModal').modal('hide');
            } else {
              Swal.fire(
                'Error!',
                'Failed to accept booking request. Please try again.',
                'error'
              );
            }
          },
          error: () => {
            Swal.fire(
              'Error!',
              'Network error. Please try again.',
              'error'
            );
          }
        });
      }
    });
  }
</script>