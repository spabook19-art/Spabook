<?php include_once '../../include/header.php'; ?>

<div class="content_wrapper">
  <div class="app_sidebar_container d-flex">
    <div class="app_sidebar_nav app_sidebar_bg_it_asset d-flex flex-column justify-content-between sidebar-hidden" id="app_sidebar_nav"></div>
    <div class="app_content_container">
      <nav class="navbar navbar-expand px-3 border-bottom" style="background-color: #C0967E;">
        <button class="btn app_open_sidebar_btn" type="button">
          <span class="navbar-toggler-icon"></span>
        </button>
        <span class="app_content_title fs-25 fw-bold pe-2">Booking</span>
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
        </div>
      </nav>
      <div class="app_content_body">
        <!-- Booking requests will be loaded here dynamically -->
        <div class="row row-cols-1 row-cols-md-3 row-cols-sm-3 row-cols-sm-3 row-cols-lg-3 row-cols-xl-5 g-3">

          <!-- Pending -->
          <div class="col">
            <div class="card shadow-sm border-0 rounded-4 text-warning bg-light-subtle active" style="cursor:pointer;" onclick="loadTableNavigation('Pending')">
              <div class="card-body py-4 px-3">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="fw-bold text-warning mb-1">PENDING</h6>
                    <h3 class="fw-bold mb-0" id="pending_count">0</h3>
                  </div>
                  <div class="fs-1">
                    <i class="fa-solid fa-hourglass-half text-warning"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Accepted -->
          <div class="col">
            <div class="card shadow-sm border-0 rounded-4 text-primary bg-light-subtle" style="cursor:pointer;" onclick="loadTableNavigation('Confirmed')">
              <div class="card-body py-4 px-3">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="fw-bold text-primary mb-1">ACCEPTED</h6>
                    <h3 class="fw-bold mb-0" id="accepted_count">0</h3>
                  </div>
                  <div class="fs-1">
                    <i class="fa-solid fa-thumbs-up text-primary"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- On Going -->
          <div class="col">
            <div class="card shadow-sm border-0 rounded-4 text-info bg-light-subtle" style="cursor:pointer;" onclick="loadTableNavigation('On-Going')">
              <div class="card-body py-4 px-3">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="fw-bold text-info mb-1">ON GOING</h6>
                    <h3 class="fw-bold mb-0" id="ongoing_count">0</h3>
                  </div>
                  <div class="fs-1">
                    <i class="fa-solid fa-spinner text-info"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>


          <!-- Completed -->
          <div class="col">
            <div class="card shadow-sm border-0 rounded-4 text-success bg-light-subtle" style="cursor:pointer;" onclick="loadTableNavigation('Completed')">
              <div class="card-body py-4 px-3">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="fw-bold text-success mb-1">COMPLETED</h6>
                    <h3 class="fw-bold mb-0" id="completed_count">0</h3>
                  </div>
                  <div class="fs-1">
                    <i class="fa-solid fa-circle-check text-success"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Cancelled -->
          <div class="col">
            <div class="card shadow-sm border-0 rounded-4 text-danger bg-light-subtle" style="cursor:pointer;" onclick="loadTableNavigation('Cancelled')">
              <div class="card-body py-4 px-3">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="fw-bold text-danger mb-1">CANCELLED</h6>
                    <h3 class="fw-bold mb-0" id="cancelled_count">0</h3>
                  </div>
                  <div class="fs-1">
                    <i class="fa-solid fa-ban text-danger"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="p-3">
          <div class="table-responsive" id="pending_table">
            <table id="bookingPendingTable" style="width:100%;">
              <tbody></tbody>
            </table>
          </div>
          <div class="table-responsive" id="confirmed_table">
            <table id="bookingConfirmedTable" style="width:100%;">
              <tbody></tbody>
            </table>
          </div>
          <div class="table-responsive" id="ongoing_table">
            <table id="bookingOngoingtable" style="width:100%;">
              <tbody></tbody>
            </table>
          </div>
          <div class="table-responsive" id="cancelled_table">
            <table id="bookingCancelledTable" style="width:100%;">
              <tbody></tbody>
            </table>
          </div>
          <div class="table-responsive" id="completedtable">
            <table id="bookingCompletedTable" style="width:100%;">
              <tbody></tbody>
            </table>
          </div>
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

  let rescheduleModalInstance;
  let rescheduleDurationMinutes = 60;

  $(document).ready(function() {
    rescheduleModalInstance = new bootstrap.Modal(document.getElementById('globalModal'));
  });

  function openRescheduleModal(bookingId, scheduleStart, scheduleEnd, serviceName, userName) {
    const modalMarkup = `
      <div class="modal-header" style="background-color: #C0967E;">
        <h5 class="modal-title text-white"><i class="bi bi-calendar-event me-2"></i>Reschedule Booking</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="rescheduleForm">
          <input type="hidden" id="rescheduleBookingId">
          <div class="card mb-3 border-0 bg-light">
            <div class="card-body">
              <h6 class="card-title mb-3"><i class="bi bi-info-circle me-2"></i>Booking Information</h6>
              <div class="mb-2">
                <strong><i class="bi bi-person-fill me-1"></i>Client:</strong> <span id="rescheduleUserName">${userName || ''}</span>
              </div>
              <div class="mb-2">
                <strong><i class="bi bi-briefcase-fill me-1"></i>Service:</strong> <span id="rescheduleServiceName">${serviceName || ''}</span>
              </div>
              <div class="mb-2">
                <strong><i class="bi bi-clock-history me-1"></i>Current Schedule:</strong>
                <span id="rescheduleCurrentDate" class="text-primary fw-semibold"></span>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label for="rescheduleDate" class="form-label"><i class="bi bi-calendar3 me-1"></i>New Date <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="rescheduleDate" required>
          </div>
          <div class="mb-3">
            <label for="rescheduleStartTime" class="form-label"><i class="bi bi-clock me-1"></i>New Start Time <span class="text-danger">*</span></label>
            <input type="time" class="form-control" id="rescheduleStartTime" required>
          </div>
          <div class="mt-3">
            <label for="rescheduleReason" class="form-label"><i class="bi bi-chat-left-text me-1"></i>Reason (optional)</label>
            <textarea class="form-control" id="rescheduleReason" rows="3"></textarea>
          </div>
          <div id="rescheduleAlert" class="alert d-none" role="alert"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i>Cancel</button>
        <button type="button" class="btn btn-primary" onclick="submitReschedule()"><i class="bi bi-check-circle me-1"></i>Save Changes</button>
      </div>
    `;

    $('#globalModalContent').html(modalMarkup);

    $('#rescheduleBookingId').val(bookingId);

    const normalizedStart = scheduleStart && scheduleStart !== 'null' ? scheduleStart.split(' ') : [];
    const normalizedEnd = scheduleEnd && scheduleEnd !== 'null' ? scheduleEnd.split(' ') : [];

    const currentDate = normalizedStart[0] || '';
    const currentStart = (normalizedStart[1] || '').slice(0, 5);
    const currentEnd = (normalizedEnd[1] || '').slice(0, 5);

    rescheduleDurationMinutes = 60;
    if (normalizedStart.length && normalizedEnd.length) {
      const startDateTime = new Date(`${normalizedStart[0]}T${(normalizedStart[1] || '').slice(0, 8)}`);
      const endDateTime = new Date(`${normalizedEnd[0]}T${(normalizedEnd[1] || '').slice(0, 8)}`);
      const diffMs = endDateTime - startDateTime;
      if (!Number.isNaN(diffMs) && diffMs > 0) {
        rescheduleDurationMinutes = Math.round(diffMs / 60000);
      }
    }

    const formattedCurrentDate = currentDate || 'No date set';
    const formattedCurrentStart = currentStart || 'No start time';
    const formattedCurrentEnd = currentEnd || 'No end time';
    $('#rescheduleCurrentDate').text(`${formattedCurrentDate} ${formattedCurrentStart} - ${formattedCurrentEnd}`.trim());

    $('#rescheduleDate').val(currentDate);
    $('#rescheduleStartTime').val(currentStart);
    $('#rescheduleReason').val('');

    const today = new Date().toISOString().split('T')[0];
    $('#rescheduleDate').attr('min', today);

    if (!rescheduleModalInstance) {
      rescheduleModalInstance = new bootstrap.Modal(document.getElementById('globalModal'));
    }

    rescheduleModalInstance.show();
  }

  function submitReschedule() {
    const bookingId = $('#rescheduleBookingId').val();
    const date = $('#rescheduleDate').val();
    const startTime = $('#rescheduleStartTime').val();
    const reason = $('#rescheduleReason').val();

    if (!date || !startTime) {
      Swal.fire('Missing information', 'Please choose a date and start time.', 'warning');
      return;
    }

    Swal.fire({
      title: 'Confirm reschedule?',
      text: 'The client will be moved to the new schedule.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes, reschedule',
      cancelButtonText: 'No'
    }).then(result => {
      if (!result.isConfirmed) {
        return;
      }

      $.ajax({
        url: '../../controller/admin_dashboard_contr.php',
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'reschedule_booking',
          bookingdetailsid: bookingId,
          schedule_start: `${date} ${startTime}:00`,
          schedule_end: `${date} ${endTime}:00`,
          reason: reason,
          therapist_id: therapistId
        },
        success: response => {
          if (response.status === 'success') {
            Swal.fire('Rescheduled', 'Booking schedule updated.', 'success');
            rescheduleModalInstance.hide();
            loadBookingRequests('Confirmed', '#bookingConfirmedTable');
          } else {
            Swal.fire('Error', response.message || 'Failed to reschedule.', 'error');
          }
        },
        error: () => {
          Swal.fire('Error', 'Could not contact the server.', 'error');
        }
      });
    });
  }

  // Load default tab on page load
  loadTableNavigation('Pending');
  loadBookingCounts();

  function loadTableNavigation(status) {
    // Hide all tables first
    $('#pending_table, #confirmed_table, #ongoing_table, #cancelled_table, #completedtable').hide();

    // Decide which table to show based on status
    let targetTable = null;
    let showtable = null;
    switch (status) {
      case 'Pending':
        showtable = '#pending_table';
        targetTable = '#bookingPendingTable';
        break;
      case 'Confirmed':
        showtable = '#confirmed_table';
        targetTable = '#bookingConfirmedTable';
        break;
      case 'On-Going':
        showtable = '#ongoing_table';
        targetTable = '#bookingOngoingtable';
        break;
      case 'Cancelled':
        showtable = '#cancelled_table';
        targetTable = '#bookingCancelledTable';
        break;
      case 'Completed':
        showtable = '#completedtable';
        targetTable = '#bookingCompletedTable';
        break;
    }

    if (showtable) {
      $(showtable).show();
      loadBookingRequests(status, targetTable);
    }
  }

  function loadBookingRequests(status, tableSelector) {
    // Destroy previous DataTable instance on the same table
    if ($.fn.DataTable.isDataTable(tableSelector)) {
      $(tableSelector).DataTable().clear().destroy();
    }

    // Initialize DataTable for that specific table
    let booking_request_table = $(tableSelector).DataTable({
      ajax: {
        url: '../../controller/admin_dashboard_contr.php',
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'load_booking_requests',
          status: status
        },
        // beforeSend: function() {
        //   Swal.fire({
        //     position: 'center',
        //     html: '<div class="mb-3"><img src="../../vendor/images/loadingspabook.gif" height="180" width="180"/></div><div><span class="fw-bold">Loading...</span></div>',
        //     heightAuto: false,
        //     showConfirmButton: false,
        //     allowOutsideClick: false
        //   });
        // },
        // complete: function() {
        //   Swal.close();
        // },
        dataSrc: function(json) {
          // If no data returned, show a clean "No Data" message
          if (!json || json.length === 0) {
            const noDataHTML = `
            <div class="card shadow-sm border rounded-3 my-4">
              <div class="card-body text-center p-4">
                <i class="bi bi-inbox" style="font-size:3rem; color:#6c757d;"></i>
                <h5 class="mt-3 text-muted">No ${status} Bookings Found</h5>
                <p class="text-muted small mb-0">There are currently no records for this status.</p>
              </div>
            </div>
          `;

            // Target the correct table container
            $(tableSelector).html(noDataHTML);
            return [];
          }
          return json;
        },
        error: function(xhr, status, error) {
          console.error('❌ AJAX Error loading booking requests:', {
            xhr,
            status,
            error
          });
          console.error('Response text:', xhr.responseText);
        }
      },
      columns: [{
        data: null,
        render: function(row) {
          let profileImage = row.profile_picture ?
            `<img src="${row.profile_picture}" alt="Profile" class="rounded-circle" style="width:72px; height:72px; object-fit:cover;">` :
            `<i class="bi bi-person-circle user-avatar" style="font-size:4.5rem; min-height:72px; min-width:72px; display:flex; align-items:center; justify-content:center;"></i>`;

          // Determine action buttons dynamically based on status
          let actions = '';
          switch (status) {
            case 'Pending':
              actions += `
                <button class="btn btn-success btn-sm px-3" onclick="updateStatus('Confirmed','${row.bookingdetailsid}');">Accept</button>
                <button class="btn btn-secondary btn-sm px-3" onclick="updateStatus('Cancelled','${row.bookingdetailsid}');">Decline</button>`;
              break;
            case 'Confirmed':
              actions += `
                <button class="btn btn-outline-secondary btn-sm px-3" onclick="openRescheduleModal('${row.bookingdetailsid}', '${row.schedule_start || ''}', '${row.schedule_end || ''}', '${row.services_name}', '${row.user_name || ''}');">Reschedule</button>
              `;
              actions += `<button class="btn btn-warning btn-sm px-3" onclick="updateStatus('On-Going','${row.bookingdetailsid}');">Proceed</button>`;
              break;
            case 'On-Going':
              actions += `<button class="btn btn-primary btn-sm px-3" onclick="updateBooking('${row.bookingdetailsid}');">Update</button>
              <button class="btn btn-success btn-sm px-3" onclick="updateStatus('Completed','${row.bookingdetailsid}');">Completed</button>`;
              break;
            case 'Completed':
              actions += `<button class="btn btn-primary btn-sm px-3" onclick="viewBookingDetails('${row.bookingdetailsid}');">View</button>`;
              break;
            case 'Cancelled':
              actions += `<button class="btn btn-outline-secondary btn-sm px-3" onclick="updateStatus('Pending','${row.bookingdetailsid}');">Restore</button>`;
              break;
          }

          return `
            <div class="card shadow-sm border rounded-3 mb-3">
              <div class="card-body">
                <div class="row g-3 align-items-center">
                  <div class="col-auto d-flex align-items-center justify-content-center">
                    ${profileImage}
                  </div>
                  <div class="col">
                    <div class="fw-semibold user-name">${row.user_name}</div>
                    <div class="small text-muted mb-1">Booking ID: #${row.bookingdetails_id}</div>
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
                  <div class="col-auto d-flex gap-2">
                    ${actions}
                  </div>
                </div>
              </div>
            </div>`;
        }
      }],
      paging: true,
      pageLength: 4,
      searching: true,
      ordering: false,
      info: false,
      dom: '<"top"f>rt<"bottom"p>'
    });

    // Tooltip + auto refresh
    booking_request_table.on('draw', function() {
      $('[data-bs-toggle="tooltip"]').tooltip();
    });

    setInterval(() => booking_request_table.ajax.reload(null, false), 30000);
  }


  function loadBookingCounts() {
    $.ajax({
      url: '../../controller/admin_dashboard_contr.php',
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'get_dashboard_stats'
      },
      success: result => {
        $('#pending_count').text(result.data.pending_bookings || 0);
        $('#accepted_count').text(result.data.accepted_bookings || 0);
        $('#ongoing_count').text(result.data.ongoing_bookings || 0);
        $('#completed_count').text(result.data.completed_bookings || 0);
        $('#cancelled_count').text(result.data.cancelled_bookings || 0);
      },
      error: (xhr, status, error) => {
        console.error('❌ AJAX Error loading booking counts:', {
          xhr,
          status,
          error
        });
        console.error('Response text:', xhr.responseText);
      }
    });
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
      beforeSend: function() {
        Swal.fire({
          position: 'center',
          html: '<div class="mb-3"><img src="../../vendor/images/loadingspabook.gif" height="180" width="180"/></div><div><span class="fw-bold">Loading...</span></div>',
          heightAuto: false,
          showConfirmButton: false,
          allowOutsideClick: false
        });
      },
      success: result => {
        Swal.close();
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



  function updateStatus(status, bookingid) {
    Swal.fire({
      title: 'Are you sure?',
      text: `You are about to change the status to ${status}.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: `Yes, ${status} it!`
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: '../../controller/admin_dashboard_contr.php',
          type: 'POST',
          dataType: 'json',
          data: {
            action: 'update_booking_status',
            new_status: status,
            bookingdetailsid: bookingid
          },
          success: result => {
            if (result.status === 'success') {
              Swal.fire(
                `${status}!`,
                `Booking status has been updated to ${status}.`,
                'success'
              );
              loadBookingRequests(status, `#booking${status.replace(' ', '')}Table`);
              loadBookingCounts();
              $('#globalModal').modal('hide');
            } else {
              Swal.fire(
                'Error!',
                'Failed to update booking status. Please try again.',
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