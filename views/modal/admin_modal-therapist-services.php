<!-- Therapist Services Modal -->
<div class="modal fade" id="therapistServicesModal" tabindex="-1" aria-labelledby="therapistServicesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-3">

            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="therapistServicesLabel">
                    Therapist Services
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <!-- Therapist Info -->
                <div class="mb-4">
                    <h6 class="fw-semibold mb-0" id="therapistName">Therapist Name</h6>
                    <small class="text-muted" id="therapistEmail">therapist@email.com</small>
                </div>

                <!-- Services List -->
                <div id="therapistServicesList" class="list-group">
                    <!-- Dynamically filled with JS -->
                </div>

                <!-- Empty State -->
                <div class="text-center text-muted d-none" id="noServicesMsg">
                    <p>No services available for this therapist.</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>