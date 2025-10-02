<!-- Only the inner modal-content part -->
<div class="modal-header bg-light border-0">
  <h5 class="modal-title fw-bold text-primary">
    <i class="bi bi-calendar-check me-2"></i> Booking Details
  </h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
  <div id="booking-details-content" class="p-3">
    <div class="row g-4">
      <!-- Customer Info -->
      <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h6 class="fw-bold mb-3 text-secondary">
              <i class="bi bi-person-circle me-2"></i> Customer Information
            </h6>
            <p class="mb-2"><strong>Name:</strong> <span id="detail-user-name">—</span></p>
            <p class="mb-2"><strong>Email:</strong> <span id="detail-user-email">—</span></p>
            <p class="mb-0"><strong>Phone:</strong> <span id="detail-user-phone">—</span></p>
          </div>
        </div>
      </div>

      <!-- Booking Info -->
      <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h6 class="fw-bold mb-3 text-secondary">
              <i class="bi bi-info-circle me-2"></i> Booking Information
            </h6>
            <p class="mb-2"><strong>Booking ID:</strong> #<span id="detail-booking-id">—</span></p>
            <p class="mb-2"><strong>Date:</strong> <span id="detail-booking-date">—</span></p>
            <p class="mb-2"><strong>Time:</strong> <span id="detail-booking-time">—</span></p>
            <p class="mb-2"><strong>Status:</strong> <span id="detail-booking-status">—</span></p>
            <p class="mb-0"><strong>Total:</strong>
              <span class="text-success fw-bold">₱<span id="detail-total-price">0</span></span>
            </p>
          </div>
        </div>
      </div>
    </div>

    <hr class="my-4">

    <!-- Services -->
    <h6 class="fw-bold text-secondary mb-3">
      <i class="bi bi-list-check me-2"></i> Services Requested
    </h6>
    <div id="detail-services-list" class="row g-3">
      <!-- Example service card -->
      <div class="col-md-6">
        <div class="card border shadow-sm rounded-3">
          <div class="card-body">
            <h6 class="mb-1" id="service-name">Service Name</h6>
            <p class="text-muted small mb-2" id="service-description">Description goes here</p>
            <div class="d-flex justify-content-between small">
              <span><i class="bi bi-clock me-1"></i> <strong>Duration:</strong> <span id="service-duration">—</span> min</span>
              <span><i class="bi bi-123 me-1"></i> <strong>Qty:</strong> <span id="service-quantity">—</span></span>
              <span><i class="bi bi-cash me-1"></i> <strong>₱</strong><span id="service-price">0</span></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Receipt -->
    <div id="detail-payment" class="mt-4">
      <h6 class="fw-bold text-secondary mb-3">
        <i class="bi bi-receipt me-2"></i> Payment Receipt
      </h6>
      <div class="text-center">
        <img id="detail-payment-img" src=""
          class="img-fluid rounded shadow-sm border"
          style="max-height: 300px; cursor: pointer;"
          alt="Payment Receipt">
        <p class="small text-muted mt-2">Click image to view full size</p>
      </div>
    </div>
  </div>
</div>

<div class="modal-footer bg-light border-0">
  <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
    <i class="bi bi-x-lg me-1"></i> Close
  </button>
  <div id="booking-actions">
    <button type="button" class="btn btn-danger me-2 declinefrommodal" onclick="declineRequest(this.value)">
      <i class="bi bi-x-circle me-1"></i> Decline
    </button>
    <button type="button" class="btn btn-success acceptfrommodal" onclick="acceptRequest(this.value)">
      <i class="bi bi-check-circle me-1"></i> Accept
    </button>
  </div>
</div>