<div class="modal fade" id="globalModal" tabindex="-1" aria-labelledby="globalModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #C0967E;">
        <h5 class="modal-title text-white" id="globalModalLabel">
          <i class="bi bi-calendar-event me-2"></i>Reschedule Booking
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="rescheduleForm">
          <input type="hidden" id="reschedule_booking_id" name="bookingdetailsid">
          
          <!-- Booking Information -->
          <div class="card mb-3 border-0 bg-light">
            <div class="card-body">
              <h6 class="card-title mb-3"><i class="bi bi-info-circle me-2"></i>Booking Information</h6>
              <div class="mb-2">
                <strong><i class="bi bi-person-fill me-1"></i>Client:</strong> <span id="reschedule_user_name">Loading...</span>
              </div>
              <div class="mb-2">
                <strong><i class="bi bi-briefcase-fill me-1"></i>Service:</strong> <span id="reschedule_service_name">Loading...</span>
              </div>
              <div class="mb-2">
                <strong><i class="bi bi-clock-history me-1"></i>Current Schedule:</strong> 
                <span id="reschedule_current_date" class="text-primary fw-semibold">Loading...</span>
              </div>
            </div>
          </div>

          <!-- New Date and Time -->
          <div class="mb-3">
            <label for="reschedule_date" class="form-label">
              <i class="bi bi-calendar3 me-1"></i>New Date <span class="text-danger">*</span>
            </label>
            <input type="date" class="form-control" id="reschedule_date" name="new_date" required>
          </div>

          <div class="mb-3">
            <label for="reschedule_time" class="form-label">
              <i class="bi bi-clock me-1"></i>New Time <span class="text-danger">*</span>
            </label>
            <input type="time" class="form-control" id="reschedule_time" name="new_time" required>
          </div>

          <!-- Reason for Reschedule (Optional) -->
          <div class="mb-3">
            <label for="reschedule_reason" class="form-label">
              <i class="bi bi-chat-left-text me-1"></i>Reason for Reschedule (Optional)
            </label>
            <textarea class="form-control" id="reschedule_reason" name="reason" rows="3" placeholder="Enter reason for rescheduling..."></textarea>
          </div>

          <!-- Alert message container -->
          <div id="reschedule_alert" class="alert d-none" role="alert"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i>Cancel
        </button>
        <button type="button" class="btn btn-primary" id="confirmRescheduleBtn">
          <i class="bi bi-check-circle me-1"></i>Confirm Reschedule
        </button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  // Set minimum date to today
  const today = new Date().toISOString().split('T')[0];
  $('#reschedule_date').attr('min', today);

  // Handle confirm reschedule button click
  $('#confirmRescheduleBtn').on('click', function() {
    const bookingdetailsid = $('#reschedule_booking_id').val();
    const newDate = $('#reschedule_date').val();
    const newTime = $('#reschedule_time').val();
    const reason = $('#reschedule_reason').val();

    // Validate inputs
    if (!newDate || !newTime) {
      showAlert('Please select both date and time.', 'danger');
      return;
    }

    // Combine date and time
    const newScheduleStart = `${newDate} ${newTime}:00`;
    
    // Calculate end time (1 hour later)
    const startDateTime = new Date(`${newDate}T${newTime}`);
    const endDateTime = new Date(startDateTime.getTime() + (60 * 60 * 1000)); // Add 1 hour
    const endDateString = endDateTime.toISOString().slice(0, 10);
    const endTimeString = endDateTime.toTimeString().slice(0, 8);
    const newScheduleEnd = `${endDateString} ${endTimeString}`;

    // Disable button and show loading state
    const $btn = $(this);
    const originalText = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Rescheduling...');

    // Send AJAX request
    $.ajax({
      url: '../../controller/admin_dashboard_contr.php',
      type: 'POST',
      data: {
        action: 'reschedule_booking',
        bookingdetailsid: bookingdetailsid,
        schedule_start: newScheduleStart,
        schedule_end: newScheduleEnd,
        reason: reason
      },
      dataType: 'json',
      success: function(response) {
        if (response.status === 'success') {
          showAlert('✅ Booking rescheduled successfully!', 'success');
          
          // Reload the bookings table after 1.5 seconds
          setTimeout(function() {
            $('#globalModal').modal('hide');
            loadAcceptedBookings(); // Reload the DataTable
            
            // Show success notification
            Swal.fire({
              icon: 'success',
              title: 'Rescheduled!',
              text: 'The booking has been successfully rescheduled.',
              timer: 2000,
              showConfirmButton: false
            });
          }, 1500);
        } else {
          showAlert('❌ ' + (response.message || 'Failed to reschedule booking. Please try again.'), 'danger');
          $btn.prop('disabled', false).html(originalText);
        }
      },
      error: function(xhr, status, error) {
        console.error('Reschedule error:', error);
        console.error('Response:', xhr.responseText);
        showAlert('❌ Network error. Please try again.', 'danger');
        $btn.prop('disabled', false).html(originalText);
      }
    });
  });

  function showAlert(message, type) {
    const $alert = $('#reschedule_alert');
    $alert.removeClass('d-none alert-success alert-danger alert-warning alert-info')
         .addClass(`alert-${type}`)
         .html(message)
         .fadeIn();
    
    // Auto-hide after 5 seconds for non-success messages
    if (type !== 'success') {
      setTimeout(function() {
        $alert.fadeOut();
      }, 5000);
    }
  }
});
</script>