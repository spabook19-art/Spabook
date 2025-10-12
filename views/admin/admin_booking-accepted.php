<?php include_once '../../include/header.php'; ?>

<div class="content_wrapper">
  <div class="app_sidebar_container d-flex">
    <div class="app_sidebar_nav app_sidebar_bg_it_asset d-flex flex-column justify-content-between sidebar-hidden" id="app_sidebar_nav"></div>
    <div class="app_content_container">
      <nav class="navbar navbar-expand px-3 border-bottom" style="background-color: #C0967E;">
        <button class="btn app_open_sidebar_btn" type="button">
          <span class="navbar-toggler-icon"></span>
        </button>
        <span class="app_content_title fs-25 fw-bold pe-2">Booking Accepted</span>
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

        <div class="p-3">
          <table id="bookingAcceptTable" style="width:100%;">
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
    $('#admin_booking_accepted').addClass('active');
  }, 500);
  loadAcceptedBookings();

  function loadAcceptedBookings() {
    if ($.fn.DataTable.isDataTable('#bookingAcceptTable')) {
      $('#bookingAcceptTable').DataTable().clear().destroy();
    }

    let booking_accept_table = $('#bookingAcceptTable').DataTable({
      ajax: {
        url: '../../controller/admin_dashboard_contr.php',
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'load_booking_requests',
          status: 'Accepted'
        },
        dataSrc: function(json) {
          if (!json || json.length === 0) {
            $('#bookingAcceptTable').html(`
              <div class="card shadow-sm border rounded-3 mb-3">
                <div class="card-body text-center p-4">
                  <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
                  <h5 class="mt-3 text-muted">No Booking Requests</h5>
                  <p class="text-muted">There are no pending booking requests at the moment.</p>
                </div>
              </div>
            `);
          }
          return json;
        }
      },
      columns: [{
        data: null,
        render: function(row) {
          let profileImage = row.profile_picture ?
            `<img src="${row.profile_picture}" alt="Profile" class="rounded-circle" style="width:72px; height:72px; object-fit:cover;">` :
            `<i class="bi bi-person-circle user-avatar" style="font-size:4.5rem; min-height:72px; min-width:72px; display:flex; align-items:center; justify-content:center;"></i>`;

          let btn = ``;
          if (row.booking_status === 'Confirmed') {
            btn = `<button class="btn btn-primary btn-sm px-3" onclick="manageBooking('${row.bookingdetailsid}');"><i class="bi bi-list-check me-1"></i>Services</button>
                   <button class="btn btn-warning btn-sm px-3" onclick="proceedBooking('${row.bookingdetailsid}');"><i class="bi bi-arrow-right-circle me-1"></i> Proceed</button>
                  `;
          } else {
            btn = `<button class="btn btn-primary btn-sm px-3" onclick="manageBooking('${row.bookingdetailsid}');"><i class="bi bi-list-check me-1"></i>Services</button>
                   <button class="btn btn-success btn-sm px-3" onclick="completeBooking('${row.bookingdetailsid}');"><i class="bi bi-check-circle me-1"></i>Complete</button>`;
          }

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
                  <div class="small text-muted mb-1">Booking ID: ${row.bookingdetails_id}</div>
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
                ${btn}
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
    booking_accept_table.on('draw', function() {
      setTimeout(function() {
        $('[data-bs-toggle="tooltip"]').tooltip(); //* ======== Initialize tooltip ========
        $('[id^="tooltip"]').remove(); //* ======== Remove tooltip every table draw ========
        $('[data-bs-toggle="tooltip"]').on('click', function() { //* ======= Hide tooltip upon click =======
          $(this).tooltip('hide');
        });
      }, 1000);
    });
    setInterval(function() {
      booking_accept_table.ajax.reload(null, false); //* ======= Reload Table Data Every X seconds with pagination retained =======
    }, 30000);

  }

  function manageBooking(bookingdetailsid) {
    console.log('🔧 Opening service management for booking:', bookingdetailsid);
    showGlobalModal('../../views/modal/admin_modal-booking-services.php');
    $.ajax({
      url: '../../controller/admin_dashboard_contr.php',
      type: 'POST',
      data: {
        action: 'get_booking_details',
        bookingdetailsid: bookingdetailsid
      },
      dataType: 'json',
      success: function(response) {
        // Update booking header info
        $('#user_name').text(response.user_name);
        $('#booking_id').text(`ID: ${response.bookingdetails_id}`);
        $('#booking_date').html(`<i class="bi bi-calendar me-1"></i> ${response.date_schedule} ${response.time_schedule}`);
        $('#total_price').html(`<i class="bi bi-currency-dollar me-1"></i> ₱${response.totalprice}`);
        
        // Check if this is a stroke treatment service
        const serviceName = response.services_name || 'Unknown Service';
        const isStrokeTreatment = serviceName.toLowerCase().includes('stroke') || 
                                  serviceName.toLowerCase().includes('special treatment');
        
        // Hide both card templates first
        $('#regular-service-card').hide();
        $('#stroke-service-card').hide();
        
        // Show and populate the appropriate card
        if (isStrokeTreatment) {
          // Show stroke treatment card with progress tracking
          const $strokeCard = $('#stroke-service-card');
          $strokeCard.find('h6.fw-bold').first().text(serviceName);
          $strokeCard.find('p.text-muted').first().text(response.service_description || 'Therapy focused on stroke recovery');
          $strokeCard.find('.badge.bg-light:nth-child(1) span').text(response.service_duration || 'N/A');
          $strokeCard.find('.badge.bg-light:nth-child(2) span').text(response.serviceprice || '0');
          $strokeCard.find('.badge.bg-light:nth-child(3) span').text(`${response.date_schedule} ${response.time_schedule}`);
          $strokeCard.show();
        } else {
          // Show regular service card
          const $regularCard = $('#regular-service-card');
          $regularCard.find('#service_name').text(serviceName);
          $regularCard.find('#service_description').text(response.service_description || 'No description available');
          $regularCard.find('#service_duration').text(response.service_duration || 'N/A');
          $regularCard.find('#service_price').text(response.serviceprice || '0');
          $regularCard.find('#service_schedule').text(`${response.date_schedule} ${response.time_schedule}`);
          $regularCard.show();
        }
      },
      error: function() {
        alert('❌ Network error. Please try again.');
      }
    });

  }

  // $(document).ready(function() {
  //   setTimeout(function() {
  //     $('#admin_booking_accepted').addClass('active');
  //   }, 500);
  //   loadAcceptedBookings();
  //   //   // Handle Manage Services button click
  //   $(document).on('click', '.btn-manage-services', function(e) {
  //     e.preventDefault();
  //     const bookingid = $(this).data('bookingid');
  //     console.log('🔧 Opening service management for booking:', bookingid);
  //     showGlobalModal('../../modal/admin_modal-booking-services.php', {
  //       bookingid: bookingid
  //     });
  //   });

  //   // Handle Complete Booking button click
  //   $(document).on('click', '.btn-complete-booking', function(e) {
  //     e.preventDefault();
  //     const bookingid = $(this).data('bookingid');
  //     const userName = $(this).data('username');

  //     if (confirm(`Are you sure you want to mark ${userName}'s booking as completed? This will move it to appointment history.`)) {
  //       completeBooking(bookingid, $(this));
  //     }
  //   });
  // });

  // function loadAcceptedBookings() {
  //   console.log('Loading accepted bookings...');
  //   $.ajax({
  //     url: '../../controller/booking_contr.php',
  //     type: 'POST',
  //     data: {
  //       action: 'get_admin_booking_accepted'
  //     },
  //     dataType: 'json',
  //     success: function(response) {
  //       console.log('Accepted bookings response:', response);

  //       // Check if we have bookings in the response
  //       if (response && response.bookings && Array.isArray(response.bookings)) {
  //         renderAcceptedBookings(response.bookings);
  //       } else if (response && response.status === 'nodata') {
  //         // Handle no data case
  //         $('#booking-accepted-container').html(`
  //           <div class="text-center p-4">
  //             <i class="bi bi-check-circle" style="font-size: 3rem; color: #198754;"></i>
  //             <h5 class="mt-3 text-muted">No Accepted Bookings</h5>
  //             <p class="text-muted">There are no accepted bookings at the moment.</p>
  //           </div>
  //         `);
  //       } else {
  //         // Handle other response formats
  //         renderAcceptedBookings(response);
  //       }
  //     },
  //     error: function(xhr, status, error) {
  //       console.error('Error loading accepted bookings:', error);
  //       console.error('Response text:', xhr.responseText);
  //       $('#booking-accepted-container').html(`
  //         <div class="alert alert-danger text-center">
  //           <i class="bi bi-exclamation-triangle"></i>
  //           Failed to load accepted bookings. Please try again.
  //         </div>
  //       `);
  //     }
  //   });
  // }

  // function renderAcceptedBookings(bookings) {
  //   const container = $('#booking-accepted-container');

  //   console.log('Accepted bookings data:', bookings);

  //   // Check if bookings is an object with status property
  //   if (bookings && bookings.status === 'nodata') {
  //     container.html(`
  //       <div class="text-center p-4">
  //         <i class="bi bi-check-circle" style="font-size: 3rem; color: #198754;"></i>
  //         <h5 class="mt-3 text-muted">No Accepted Bookings</h5>
  //         <p class="text-muted">There are no accepted bookings at the moment.</p>
  //       </div>
  //     `);
  //     return;
  //   }

  //   // Check if bookings is an array with length 0
  //   if (!bookings || !Array.isArray(bookings) || bookings.length === 0) {
  //     container.html(`
  //       <div class="text-center p-4">
  //         <i class="bi bi-check-circle" style="font-size: 3rem; color: #198754;"></i>
  //         <h5 class="mt-3 text-muted">No Accepted Bookings</h5>
  //         <p class="text-muted">There are no accepted bookings at the moment.</p>
  //       </div>
  //     `);
  //     return;
  //   }

  //   let html = '';
  //   bookings.forEach(booking => {
  //     const servicesText = booking.services.map(s => s.name).join(', ');
  //     const bookingDate = new Date(booking.booking_date).toLocaleDateString();

  //     html += `
  //       <div class="container-sm bg-white rounded-3 p-3 shadow-sm mb-3">
  //         <!-- Desktop Version -->
  //         <div class="row g-3 align-items-center d-none d-md-flex">
  //           <!-- Image -->
  //           <div class="col-auto d-flex align-items-center justify-content-center" style="height:100%; min-height:80px;">
  //             <i class="bi bi-person-circle user-avatar" style="font-size:4.5rem; min-height:72px; min-width:72px; display:flex; align-items:center; justify-content:center;"></i>
  //           </div>
  //           <!-- Info Text -->
  //           <div class="col">
  //             <div class="fw-semibold user-name">${booking.user_name}</div>
  //             <div class="small text-muted mb-1">Booking ID: #${booking.bookingid}</div>
  //             <div class="d-flex flex-wrap gap-2 small mt-1">
  //               <span class="badge bg-light text-dark border border-1 px-2 py-1"><i class="bi bi-briefcase me-1"></i>${servicesText}</span>
  //               <span class="badge bg-light text-dark border border-1 px-2 py-1"><i class="bi bi-calendar-event me-1"></i>${bookingDate}</span>
  //               <span class="badge bg-success text-white px-2 py-1"><i class="bi bi-currency-dollar me-1"></i>₱${booking.total_price}</span>
  //               <span class="badge bg-success text-white px-2 py-1"><i class="bi bi-check-circle me-1"></i>Confirmed</span>
  //             </div>
  //           </div>
  //           <!-- Action Buttons -->
  //           <div class="col-auto d-flex flex-row gap-2">
  //             <button class="btn btn-primary btn-manage-services px-4" data-bookingid="${booking.bookingid}">
  //               <i class="bi bi-list-check me-1"></i>
  //               Manage Services
  //             </button>
  //             <button class="btn btn-success btn-complete-booking px-4" data-bookingid="${booking.bookingid}" data-username="${booking.user_name}">
  //               <i class="bi bi-check-circle me-1"></i>
  //               Complete
  //             </button>
  //           </div>
  //         </div>
  //         <!-- Mobile/Tablet Compact Version -->
  //         <div class="d-flex flex-column flex-sm-row align-items-center gap-2 gap-sm-3 d-flex d-md-none">
  //           <!-- Image -->
  //           <div class="flex-shrink-0 d-flex align-items-center justify-content-center" style="min-width:56px;">
  //             <i class="bi bi-person-circle user-avatar-compact"></i>
  //           </div>
  //           <!-- Info Text -->
  //           <div class="flex-grow-1 text-center text-sm-start">
  //             <div class="fw-semibold user-name-compact">${booking.user_name}</div>
  //             <div class="small text-muted">Booking ID: #${booking.bookingid}</div>
  //             <div class="d-flex flex-wrap justify-content-center justify-content-sm-start gap-2 small mt-1">
  //               <span class="badge bg-light text-dark border border-1 px-2 py-1"><i class="bi bi-briefcase me-1"></i>${servicesText}</span>
  //               <span class="badge bg-light text-dark border border-1 px-2 py-1"><i class="bi bi-calendar-event me-1"></i>${bookingDate}</span>
  //               <span class="badge bg-success text-white px-2 py-1"><i class="bi bi-currency-dollar me-1"></i>₱${booking.total_price}</span>
  //               <span class="badge bg-success text-white px-2 py-1"><i class="bi bi-check-circle me-1"></i>Confirmed</span>
  //             </div>
  //           </div>
  //           <!-- Action Buttons -->
  //           <div class="d-flex flex-row flex-sm-column gap-1 ms-sm-2 mt-2 mt-sm-0">
  //             <button class="btn btn-primary btn-manage-services btn-sm px-3" data-bookingid="${booking.bookingid}">
  //               <i class="bi bi-list-check me-1"></i>
  //               Services
  //             </button>
  //             <button class="btn btn-success btn-complete-booking btn-sm px-3" data-bookingid="${booking.bookingid}" data-username="${booking.user_name}">
  //               <i class="bi bi-check-circle me-1"></i>
  //               Complete
  //             </button>
  //           </div>
  //         </div>
  //       </div>
  //     `;
  //   });

  //   container.html(html);
  // }

  // function completeBooking(bookingid, button) {
  //   const originalText = button.text();
  //   const bookingCard = $(`#booking-${bookingid}`);

  //   // Disable all buttons in this booking card
  //   bookingCard.find('button').prop('disabled', true);
  //   button.text('Completing...').addClass('btn-warning').removeClass('btn-success');

  //   $.ajax({
  //     url: '../../controller/booking_contr.php',
  //     type: 'POST',
  //     data: {
  //       action: 'complete_booking',
  //       bookingid: bookingid
  //     },
  //     dataType: 'json',
  //     success: function(response) {
  //       if (response.status === 'success') {
  //         // Show success animation
  //         bookingCard.addClass('border-success').css('background-color', '#d4edda');

  //         // Show success message
  //         alert('✅ Booking completed successfully! It has been moved to appointment history.');

  //         // Remove the booking card with animation after 1 second
  //         setTimeout(() => {
  //           bookingCard.fadeOut(500, function() {
  //             $(this).remove();
  //             // Check if no more bookings exist
  //             if ($('#booking-accepted-container .container-sm').length === 0) {
  //               $('#booking-accepted-container').html(`
  //                 <div class="text-center p-4">
  //                   <i class="bi bi-check-circle" style="font-size: 3rem; color: #198754;"></i>
  //                   <h5 class="mt-3 text-muted">No Accepted Bookings</h5>
  //                   <p class="text-muted">There are no accepted bookings at the moment.</p>
  //                 </div>
  //               `);
  //             }
  //           });
  //         }, 1000);
  //       } else {
  //         alert('❌ Failed to complete booking. Please try again.');
  //         // Restore button state
  //         bookingCard.find('button').prop('disabled', false);
  //         button.text(originalText).removeClass('btn-warning').addClass('btn-success');
  //       }
  //     },
  //     error: function() {
  //       alert('❌ Network error. Please try again.');
  //       // Restore button state
  //       bookingCard.find('button').prop('disabled', false);
  //       button.text(originalText).removeClass('btn-warning').addClass('btn-success');
  //     }
  //   });
  // }
</script>