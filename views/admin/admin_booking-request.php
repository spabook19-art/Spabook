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

    $('#bookingRequestTable').DataTable({
      ajax: {
        url: '../../controller/admin_dashboard_contr.php',
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'load_booking_requests'
        },
        dataSrc: '' // ✅ PHP returns array
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
                  <button class="btn btn-primary btn-view px-3" onclick="viewBookingDetails('${row.bookingdetailsid}');">View</button>
                  <button class="btn btn-secondary btn-decline px-3" onclick="declineRequest('${row.bookingdetailsid}');">Decline</button>
                  <button class="btn btn-success btn-accept px-3" onclick="acceptRequest('${row.bookingdetailsid}');">Accept</button>
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

  // $(document).ready(function() {


  //   // Handle View button click
  //   $(document).on('click', '.btn-view', function(e) {
  //     e.preventDefault();
  //     const bookingid = $(this).data('bookingid');
  //     console.log('View button clicked for booking ID:', bookingid);

  //     // Use the new modal file in the admin/modal directory
  //     showGlobalModal('../../views/modal/admin_modal-booking-details.php', {
  //       bookingid: bookingid
  //     });
  //   });

  //   // Add toast notification function
  //   window.showToast = function(message, type = 'info') {
  //     // Remove existing toasts
  //     $('.toast-notification').remove();

  //     const toastClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-info';
  //     const toast = $(`
  //       <div class="toast-notification position-fixed top-0 end-0 m-3 p-3 rounded text-white ${toastClass}" style="z-index: 9999;">
  //         ${message}
  //       </div>
  //     `);

  //     $('body').append(toast);

  //     // Auto remove after 3 seconds
  //     setTimeout(() => {
  //       toast.fadeOut(300, function() {
  //         $(this).remove();
  //       });
  //     }, 3000);
  //   };

  //   // Handle Accept button click
  //   $(document).on('click', '.btn-accept', function(e) {
  //     e.preventDefault();
  //     const bookingid = $(this).data('bookingid');
  //     const userName = $(this).data('username');

  //     if (confirm(`Are you sure you want to accept the booking request from ${userName}?`)) {
  //       acceptBooking(bookingid, $(this));
  //     }
  //   });

  //   // Handle Decline button click
  //   $(document).on('click', '.btn-decline', function(e) {
  //     e.preventDefault();
  //     const bookingid = $(this).data('bookingid');
  //     const userName = $(this).data('username');

  //     if (confirm(`Are you sure you want to decline the booking request from ${userName}?`)) {
  //       declineBooking(bookingid, $(this));
  //     }
  //   });
  // });

  // function loadBookingRequests() {
  //   console.log('Loading booking requests...');
  //   $.ajax({
  //     url: '../../controller/booking_contr.php',
  //     type: 'POST',
  //     data: {
  //       action: 'get_admin_booking_requests'
  //     },
  //     dataType: 'json',
  //     success: function(response) {
  //       console.log('Booking requests response:', response);

  //       // Check if we have bookings in the response
  //       if (response && response.bookings && Array.isArray(response.bookings)) {
  //         renderBookingRequests(response.bookings);
  //       } else if (response && response.status === 'nodata') {
  //         // Handle no data case
  //         $('#booking-requests-container').html(`
  //           <div class="text-center p-4">
  //             <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
  //             <h5 class="mt-3 text-muted">No Booking Requests</h5>
  //             <p class="text-muted">There are no pending booking requests at the moment.</p>
  //           </div>
  //         `);
  //       } else {
  //         // Handle other response formats
  //         renderBookingRequests(response);
  //       }
  //     },
  //     error: function(xhr, status, error) {
  //       console.error('Error loading booking requests:', error);
  //       console.error('Response text:', xhr.responseText);
  //       $('#booking-requests-container').html(`
  //         <div class="alert alert-danger text-center">
  //           <i class="bi bi-exclamation-triangle"></i>
  //           Failed to load booking requests. Please try again.
  //         </div>
  //       `);
  //     }
  //   });
  // }

  // function renderBookingRequests(bookings) {
  //   const container = $('#booking-requests-container');

  //   console.log('Booking requests data:', bookings);

  //   // Check if bookings is an object with status property
  //   if (bookings && bookings.status === 'nodata') {
  //     container.html(`
  //       <div class="text-center p-4">
  //         <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
  //         <h5 class="mt-3 text-muted">No Booking Requests</h5>
  //         <p class="text-muted">There are no pending booking requests at the moment.</p>
  //       </div>
  //     `);
  //     return;
  //   }

  //   // Check if bookings is an array with length 0
  //   if (!bookings || !Array.isArray(bookings) || bookings.length === 0) {
  //     container.html(`
  //       <div class="text-center p-4">
  //         <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
  //         <h5 class="mt-3 text-muted">No Booking Requests</h5>
  //         <p class="text-muted">There are no pending booking requests at the moment.</p>
  //       </div>
  //     `);
  //     return;
  //   }

  //   let html = '';
  //   bookings.forEach(booking => {
  //     const servicesText = booking.services.map(s => s.name).join(', ');
  //     const bookingDate = new Date(booking.booking_date).toLocaleDateString();

  //     html += `
  //       <div class="container-sm bg-white rounded-3 p-3 shadow-sm mb-3" id="booking-${booking.bookingid}">
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
  //             </div>
  //           </div>
  //           <!-- Action Buttons -->
  //           <div class="col-auto d-flex flex-row gap-2">
  //             <button class="btn btn-primary btn-view px-4" data-bookingid="${booking.bookingid}">View</button>
  //             <button class="btn btn-secondary btn-decline px-4" data-bookingid="${booking.bookingid}" data-username="${booking.user_name}">Decline</button>
  //             <button class="btn btn-success btn-accept px-4" data-bookingid="${booking.bookingid}" data-username="${booking.user_name}">Accept</button>
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
  //             </div>
  //           </div>
  //           <!-- Action Buttons -->
  //           <div class="d-flex flex-row flex-sm-column gap-1 ms-sm-2 mt-2 mt-sm-0">
  //             <button class="btn btn-primary btn-view btn-sm px-3" data-bookingid="${booking.bookingid}">View</button>
  //             <button class="btn btn-secondary btn-decline btn-sm px-3" data-bookingid="${booking.bookingid}" data-username="${booking.user_name}">Decline</button>
  //             <button class="btn btn-success btn-accept btn-sm px-3" data-bookingid="${booking.bookingid}" data-username="${booking.user_name}">Accept</button>
  //           </div>
  //         </div>
  //       </div>
  //     `;
  //   });

  //   container.html(html);
  // }

  // function acceptBooking(bookingid, button) {
  //   const originalText = button.text();
  //   const bookingCard = $(`#booking-${bookingid}`);

  //   // Disable all buttons in this booking card
  //   bookingCard.find('button').prop('disabled', true);
  //   button.text('Accepting...').addClass('btn-warning').removeClass('btn-success');

  //   $.ajax({
  //     url: '../../controller/booking_contr.php',
  //     type: 'POST',
  //     data: {
  //       action: 'accept_booking',
  //       bookingid: bookingid
  //     },
  //     dataType: 'json',
  //     success: function(response) {
  //       if (response.status === 'success') {
  //         // Show success animation
  //         bookingCard.addClass('border-success').css('background-color', '#d4edda');

  //         // Show success message
  //         showToast('✅ Booking accepted successfully!', 'success');

  //         // Remove the booking card with animation after 1 second
  //         setTimeout(() => {
  //           bookingCard.fadeOut(500, function() {
  //             $(this).remove();
  //             // Check if no more bookings exist
  //             if ($('#booking-requests-container .container-sm').length === 0) {
  //               $('#booking-requests-container').html(`
  //                 <div class="text-center p-4">
  //                   <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
  //                   <h5 class="mt-3 text-muted">No Booking Requests</h5>
  //                   <p class="text-muted">There are no pending booking requests at the moment.</p>
  //                 </div>
  //               `);
  //             }
  //           });
  //         }, 1000);
  //       } else {
  //         alert('❌ Failed to accept booking. Please try again.');
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

  // function declineBooking(bookingid, button) {
  //   const originalText = button.text();
  //   const bookingCard = $(`#booking-${bookingid}`);

  //   // Disable all buttons in this booking card
  //   bookingCard.find('button').prop('disabled', true);
  //   button.text('Declining...').addClass('btn-warning').removeClass('btn-secondary');

  //   $.ajax({
  //     url: '../../controller/booking_contr.php',
  //     type: 'POST',
  //     data: {
  //       action: 'decline_booking',
  //       bookingid: bookingid
  //     },
  //     dataType: 'json',
  //     success: function(response) {
  //       if (response.status === 'success') {
  //         // Show decline animation
  //         bookingCard.addClass('border-danger').css('background-color', '#f8d7da');

  //         // Show success message
  //         showToast('✅ Booking declined successfully!', 'success');

  //         // Remove the booking card with animation after 1 second
  //         setTimeout(() => {
  //           bookingCard.fadeOut(500, function() {
  //             $(this).remove();
  //             // Check if no more bookings exist
  //             if ($('#booking-requests-container .container-sm').length === 0) {
  //               $('#booking-requests-container').html(`
  //                 <div class="text-center p-4">
  //                   <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
  //                   <h5 class="mt-3 text-muted">No Booking Requests</h5>
  //                   <p class="text-muted">There are no pending booking requests at the moment.</p>
  //                 </div>
  //               `);
  //             }
  //           });
  //         }, 1000);
  //       } else {
  //         alert('❌ Failed to decline booking. Please try again.');
  //         // Restore button state
  //         bookingCard.find('button').prop('disabled', false);
  //         button.text(originalText).removeClass('btn-warning').addClass('btn-secondary');
  //       }
  //     },
  //     error: function() {
  //       alert('❌ Network error. Please try again.');
  //       // Restore button state
  //       bookingCard.find('button').prop('disabled', false);
  //       button.text(originalText).removeClass('btn-warning').addClass('btn-secondary');
  //     }
  //   });
  // }
</script>

<style>
  .user-avatar-compact {
    font-size: 2.2rem;
    border-radius: 50%;
    background: #f3f3f3;
    padding: 6px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    color: #888;
  }

  .user-name-compact {
    font-size: 1.08rem;
    letter-spacing: 0.2px;
    margin-bottom: 2px;
  }

  .user-avatar {
    font-size: 4.5rem;
    min-height: 72px;
    min-width: 72px;
    border-radius: 50%;
    background: #f3f3f3;
    padding: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    color: #888;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .user-name {
    font-size: 1.18rem;
    letter-spacing: 0.5px;
    margin-bottom: 2px;
  }

  @media (max-width: 767.98px) {
    .user-avatar {
      font-size: 48px !important;
      margin-bottom: 8px;
    }

    .user-name {
      font-size: 1.1rem !important;
    }

    .container-sm {
      padding: 1rem 0.5rem !important;
    }

    .row.g-3 {
      gap: 0.5rem !important;
    }

    .btn {
      font-size: 0.95rem;
      padding-left: 1.5rem !important;
      padding-right: 1.5rem !important;
    }
  }

  @media (min-width: 768px) {
    .col-auto.d-flex.flex-row.gap-2 {
      flex-direction: row !important;
      align-items: center !important;
      justify-content: flex-end !important;
    }
  }

  @media (max-width: 575.98px) {
    .user-avatar-compact {
      font-size: 1.6rem;
      padding: 4px;
    }

    .user-name-compact {
      font-size: 1rem;
    }

    .user-avatar {
      font-size: 1.6rem !important;
      padding: 4px;
    }

    .user-name {
      font-size: 1rem !important;
    }

    .container-sm {
      padding: 0.5rem 0.2rem !important;
    }

    .btn {
      font-size: 0.9rem;
      padding-left: 1rem !important;
      padding-right: 1rem !important;
    }

    .badge {
      font-size: 0.93rem;
      padding: 0.35em 0.7em;
      display: block;
      margin: 0.1rem auto;
      width: fit-content;
    }

    .row.g-3 {
      gap: 0.3rem !important;
    }

    .col-auto.d-flex.flex-column.gap-2 {
      flex-direction: row !important;
      gap: 0.5rem !important;
      margin-top: 0.5rem !important;
      justify-content: center !important;
    }

    .col {
      text-align: center !important;
    }
  }
</style>