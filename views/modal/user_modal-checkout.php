<div class="modal-header">
    <h5 class="modal-title">
        <i class="bi bi-calendar-check me-2"></i>Complete Your Booking
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <!-- Services and Scheduling -->
    <div id="servicesSchedulingContainer">
        <!-- Services with time/therapist selection will be loaded here -->
    </div>

    <!-- Booking Summary -->
    <div class="card mt-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="bi bi-receipt me-1"></i>Booking Summary
            </h6>
        </div>
        <div class="card-body">
            <div id="bookingSummary">
                <!-- Summary will be populated here -->
            </div>
            <hr>
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Total Amount:</h5>
                <h5 class="mb-0 text-success" id="checkoutTotal">₱0</h5>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer d-flex justify-content-end">
    <button type="button" class="btn btn-secondary me-2" id="cancelBookingBtn">Cancel</button>
    <button type="button" class="btn btn-success" id="proceedToPaymentBtn" disabled>
        <i class="bi bi-credit-card me-1"></i>Proceed to Payment
    </button>
</div>

<script>
    $(document).ready(function() {
        // Initialize checkout when modal opens
        setTimeout(() => {
            initializeCheckout();
        }, 100);

        // Handle Cancel button - clear cart and close modal
        $('#cancelBookingBtn').on('click', function() {
            console.log('🚫 Cancel clicked - clearing cart...');
            
            // Check if cart contains stroke services and delete patient info
            const cart = window.serviceCart || [];
            const strokeService = cart.find(service => 
                service.name && service.name.toLowerCase().includes('stroke')
            );
            
            if (strokeService) {
                deletePatientInfoFromSession();
            }
            
            // Clear cart from storage
            if (typeof window.clearCartFromStorage === 'function') {
                window.clearCartFromStorage();
            }
            
            // Clear cart in memory
            window.serviceCart = [];
            
            // Update badge on main page
            if (typeof window.updateCheckoutBadge === 'function') {
                window.updateCheckoutBadge();
            }
            
            // Close the modal (using globalModal ID)
            $('#globalModal').modal('hide');
            
            console.log('✅ Cart cleared and modal closed');
        });
    });

    var checkoutData = {
        services: [],
        totalAmount: 0
    };

    function initializeCheckout() {
        const cart = window.serviceCart || [];

        if (cart.length === 0) {
            $('#servicesSchedulingContainer').html(`
            <div class="text-center py-5">
                <i class="bi bi-cart-x fs-1 text-muted mb-3"></i>
                <h5 class="text-muted">No services in cart</h5>
                <p class="text-muted">Please add services to your cart before checkout.</p>
            </div>
        `);
            return;
        }

        // Initialize checkout data
        checkoutData.services = cart.map((service, index) => ({
            ...service,
            index: index,
            selectedDate: '',
            selectedTime: '',
            availableTherapists: [],
            therapists: []  // Will be populated when therapist selection is made
        }));

        renderServicesScheduling();
        updateBookingSummary();
    }

    function renderServicesScheduling() {
        let html = '';

        checkoutData.services.forEach((service, index) => {
            const serviceTotal = service.price * service.people;
            const isStrokeService = (service.name || '').toLowerCase().includes('stroke');

            html += `
            <div class="card mb-3 service-scheduling-card" data-service-index="${index}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">
                            <i class="bi bi-spa me-1"></i>${service.name}
                        </h6>
                        <small class="text-muted">${service.people} person(s) × ₱${service.price} = ₱${serviceTotal}</small>
                    </div>
                    <button class="btn btn-sm btn-outline-danger" onclick="removeService(${index})" title="Remove service">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="card-body">
                    <!-- Date Selection -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar me-1"></i>Select Date:
                            </label>
                            <input type="date" class="form-control date-selector" 
                                   data-service-index="${index}" 
                                   min="${new Date().toISOString().split('T')[0]}"
                                   value="${service.selectedDate || ''}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-clock me-1"></i>Select Time:
                            </label>
                            <select class="form-select time-selector" data-service-index="${index}" disabled>
                                <option value="">Select a date first</option>
                            </select>
                        </div>
                    </div>
                    
                    ${isStrokeService ? `
                    <!-- Therapist Selection Container (ONLY for stroke therapy services) -->
                    <div class="therapist-selection-section" data-service-index="${index}" style="display: none;">
                        <hr class="my-3">
                        <div class="mb-2">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-person-fill me-1"></i>Select Therapist(s):
                            </label>
                        </div>
                        <div class="therapist-loading text-center py-3" style="display: none;">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <div class="mt-2 text-muted small">Loading available therapists...</div>
                        </div>
                        <div class="therapist-options-container">
                            <!-- Therapist options will be populated here -->
                        </div>
                    </div>
                    ` : ''}
                </div>
            </div>
        `;
        });

        $('#servicesSchedulingContainer').html(html);

        // Attach event handlers
        attachSchedulingEventHandlers();
    }

    function attachSchedulingEventHandlers() {
        // Date selection handler
        $('.date-selector').on('change', function() {
            const serviceIndex = $(this).data('service-index');
            const selectedDate = $(this).val();

            checkoutData.services[serviceIndex].selectedDate = selectedDate;
            checkoutData.services[serviceIndex].selectedTime = '';

            if (selectedDate) {
                loadTimeSlots(serviceIndex, selectedDate);
            } else {
                resetTime(serviceIndex);
            }

            updateBookingSummary();
            validateCheckout();
        });

        // Time selection handler
        $('.time-selector').on('change', function() {
            const serviceIndex = $(this).data('service-index');
            const selectedTime = $(this).val();

            checkoutData.services[serviceIndex].selectedTime = selectedTime;

            // Load therapists only for stroke services when time is selected
            if (selectedTime) {
                const service = checkoutData.services[serviceIndex];
                
                if (service.selectedDate) {
                    // Only load therapists for stroke therapy services
                    const isStrokeService = (service.name || '').toLowerCase().includes('stroke');
                    if (isStrokeService) {
                        loadAvailableTherapists(serviceIndex);
                    } else {
                        // Hide therapist section for non-stroke services
                        $(`.therapist-selection-section[data-service-index="${serviceIndex}"]`).hide();
                    }
                }
            }

            updateBookingSummary();
            validateCheckout();
        });
    }

    function loadTimeSlots(serviceIndex, selectedDate) {
        const timeSelector = $(`.time-selector[data-service-index="${serviceIndex}"]`);

        // Show loading state
        timeSelector.prop('disabled', false).html('<option value="">Loading times...</option>');

        // Generate time slots (9 AM to 8 PM, every hour)
        const timeSlots = [];
        for (let hour = 9; hour <= 20; hour++) {
            const time24 = `${hour.toString().padStart(2, '0')}:00`;
            const time12 = new Date(`2000-01-01 ${time24}`).toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
            timeSlots.push({
                value: time24,
                label: time12
            });
        }

        // Check for existing bookings on this date
        $.ajax({
            url: '../../controller/booking_contr.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_booked_times',
                date: selectedDate
            },
            success: function(bookedTimes) {
                let html = '<option value="">Select time</option>';

                timeSlots.forEach(slot => {
                    const isBooked = bookedTimes && bookedTimes.includes(slot.value);
                    const disabled = isBooked ? 'disabled' : '';
                    const label = isBooked ? `${slot.label} (Fully Booked)` : slot.label;

                    html += `<option value="${slot.value}" ${disabled}>${label}</option>`;
                });

                timeSelector.html(html);
            },
            error: function() {
                // If error, still show time slots
                let html = '<option value="">Select time</option>';
                timeSlots.forEach(slot => {
                    html += `<option value="${slot.value}">${slot.label}</option>`;
                });
                timeSelector.html(html);
            }
        });
    }

    function resetTime(serviceIndex) {
        $(`.time-selector[data-service-index="${serviceIndex}"]`).prop('disabled', true).html('<option value="">Select a date first</option>');
    }

    // Load available therapists for a service
    function loadAvailableTherapists(serviceIndex) {
        const service = checkoutData.services[serviceIndex];
        const therapistSection = $(`.therapist-selection-section[data-service-index="${serviceIndex}"]`);
        const loadingDiv = therapistSection.find('.therapist-loading');
        const optionsContainer = therapistSection.find('.therapist-options-container');

        // Show section and loading state
        therapistSection.show();
        loadingDiv.show();
        optionsContainer.html('');

        console.log('🔄 Loading therapists for service:', serviceIndex, service);

        $.ajax({
            url: '../../controller/therapist_contr.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_available_therapists',
                service_id: service.id || service.serviceId,
                date: service.selectedDate,
                time: service.selectedTime
            },
            success: function(result) {
                loadingDiv.hide();
                console.log('✅ Therapists loaded:', result);

                // Validate result is an array
                if (!Array.isArray(result)) {
                    if (result === 'nodata' || !result) {
                        result = [];
                    } else if (typeof result === 'object') {
                        result = [result];
                    } else {
                        console.error('❌ Invalid therapist data format:', result);
                        result = [];
                    }
                }

                renderTherapistSelection(serviceIndex, result);
            },
            error: function(xhr, status, error) {
                loadingDiv.hide();
                console.error('❌ Error loading therapists:', {xhr, status, error});
                
                optionsContainer.html(`
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <small>Unable to load therapists. You can proceed without selecting a specific therapist.</small>
                    </div>
                `);
            }
        });
    }

    // Render therapist selection UI
    function renderTherapistSelection(serviceIndex, therapists) {
        const service = checkoutData.services[serviceIndex];
        const optionsContainer = $(`.therapist-selection-section[data-service-index="${serviceIndex}"] .therapist-options-container`);

        if (!therapists || therapists.length === 0) {
            optionsContainer.html(`
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    <small>No therapists available for this time slot. A therapist will be assigned automatically.</small>
                </div>
            `);
            // Initialize empty therapists array for this service
            checkoutData.services[serviceIndex].therapists = [];
            return;
        }

        let html = '';

        // Create therapist selection for each person
        for (let person = 1; person <= service.people; person++) {
            html += `
                <div class="person-therapist-selection mb-3 p-3 border rounded bg-light" data-person="${person}">
                    <h6 class="mb-3">
                        <i class="bi bi-person me-1"></i>Person ${person} - Select Therapist
                    </h6>
                    <div class="row g-2">
            `;

            // Add "Any Available Therapist" option (default)
            html += `
                <div class="col-md-6">
                    <div class="form-check therapist-option">
                        <input class="form-check-input" 
                               type="radio" 
                               name="therapist-service-${serviceIndex}-person-${person}" 
                               id="therapist-any-${serviceIndex}-${person}"
                               value="any"
                               data-therapist-name="Any Available Therapist"
                               data-service-index="${serviceIndex}"
                               data-person="${person}"
                               checked>
                        <label class="form-check-label w-100" for="therapist-any-${serviceIndex}-${person}">
                            <div class="therapist-card p-2 border rounded">
                                <div class="fw-semibold text-primary">Any Available Therapist</div>
                                <div class="text-muted small">We'll assign the best therapist for you</div>
                            </div>
                        </label>
                    </div>
                </div>
            `;

            // Add each therapist option
            therapists.forEach(therapist => {
                html += `
                    <div class="col-md-6">
                        <div class="form-check therapist-option">
                            <input class="form-check-input" 
                                   type="radio" 
                                   name="therapist-service-${serviceIndex}-person-${person}" 
                                   id="therapist-${therapist.therapistid}-${serviceIndex}-${person}"
                                   value="${therapist.therapistid}"
                                   data-therapist-name="${therapist.therapist_name}"
                                   data-service-index="${serviceIndex}"
                                   data-person="${person}">
                            <label class="form-check-label w-100" for="therapist-${therapist.therapistid}-${serviceIndex}-${person}">
                                <div class="therapist-card p-2 border rounded">
                                    <div class="fw-semibold">${therapist.therapist_name}</div>
                                    <div class="text-muted small">${therapist.therapist_desc || 'Professional therapist'}</div>
                                    <div class="text-success small mt-1">
                                        <i class="bi bi-check-circle me-1"></i>Available
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                `;
            });

            html += `
                    </div>
                </div>
            `;
        }

        optionsContainer.html(html);

        // Initialize default therapists (all null for "any available")
        checkoutData.services[serviceIndex].therapists = [];
        for (let person = 1; person <= service.people; person++) {
            checkoutData.services[serviceIndex].therapists.push({
                person: person,
                therapistId: null,
                therapistName: 'Any Available Therapist'
            });
        }

        // Attach event handlers
        optionsContainer.find('.form-check-input').on('change', function() {
            updateSelectedTherapists(serviceIndex);
        });

        // Add click handler for therapist cards
        optionsContainer.find('.therapist-card').on('click', function() {
            $(this).closest('.therapist-option').find('.form-check-input').prop('checked', true).trigger('change');
        });
    }

    // Update selected therapists for a service
    function updateSelectedTherapists(serviceIndex) {
        checkoutData.services[serviceIndex].therapists = [];

        $(`.therapist-selection-section[data-service-index="${serviceIndex}"] .form-check-input:checked`).each(function() {
            const therapistId = $(this).val();
            const therapistName = $(this).data('therapist-name');
            const person = parseInt($(this).data('person'));

            checkoutData.services[serviceIndex].therapists.push({
                person: person,
                therapistId: therapistId,
                therapistName: therapistName
            });
        });

        console.log('✅ Updated therapists for service', serviceIndex, ':', checkoutData.services[serviceIndex].therapists);
        updateBookingSummary();
    }

    function updateBookingSummary() {
        let summaryHtml = '';
        let totalAmount = 0;

        checkoutData.services.forEach((service, index) => {
            const serviceTotal = service.price * service.people;
            totalAmount += serviceTotal;

            const dateTimeInfo = service.selectedDate && service.selectedTime ?
                `${formatDate(service.selectedDate)} at ${formatTime(service.selectedTime)}` :
                'Date & time not selected';

            summaryHtml += `
            <div class="summary-item mb-3 p-2 border-start border-3 border-primary">
                <div class="d-flex justify-content-between">
                    <strong>${service.name}</strong>
                    <span class="text-success fw-bold">₱${serviceTotal}</span>
                </div>
                <div class="text-muted small mt-1">
                    <div><i class="bi bi-people me-1"></i>${service.people} person(s)</div>
                    <div><i class="bi bi-calendar me-1"></i>${dateTimeInfo}</div>
                </div>
            </div>
        `;
        });

        $('#bookingSummary').html(summaryHtml);
        $('#checkoutTotal').text(`₱${totalAmount}`);
        checkoutData.totalAmount = totalAmount;
    }

    function validateCheckout() {
        let isValid = true;

        // Check if all services have date and time selected
        checkoutData.services.forEach(service => {
            if (!service.selectedDate || !service.selectedTime) {
                isValid = false;
            }
        });

        $('#proceedToPaymentBtn').prop('disabled', !isValid);
    }

    function removeService(index) {
        // Get the service being removed
        const removedService = checkoutData.services[index];
        
        // Check if this is a stroke service
        const isStrokeService = removedService.name && 
            removedService.name.toLowerCase().includes('stroke');
        
        // If it's a stroke service, delete the patient info
        if (isStrokeService) {
            deletePatientInfoFromSession();
        }
        
        // Remove from checkout data
        checkoutData.services.splice(index, 1);

        // Remove from global cart
        window.serviceCart.splice(index, 1);
        
        // Save updated cart to LocalStorage
        if (typeof window.saveCartToStorage === 'function') {
            window.saveCartToStorage();
        }

        // Update checkout button
        if (typeof window.updateCheckoutBadge === 'function') {
            window.updateCheckoutBadge();
        }

        if (checkoutData.services.length === 0) {
            $('#globalModal').modal('hide');
            Swal.fire({
                icon: 'info',
                title: 'Cart Empty',
                text: 'All services have been removed from your cart.',
            });
        } else {
            // Re-render with updated indices
            initializeCheckout();
        }
    }

    function formatDate(dateString) {
        return new Date(dateString + 'T00:00:00').toLocaleDateString('en-US', {
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    function formatTime(timeString) {
        return new Date(`2000-01-01 ${timeString}`).toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
    }

    // Handle proceed to payment (remove old listeners first, then attach once)
    $('#proceedToPaymentBtn').off('click').one('click', function() {
        // Disable button to prevent double clicks
        $(this).prop('disabled', true);
        
        // Prepare booking data for payment
        const bookingData = {
            services: checkoutData.services.map(service => ({
                serviceId: service.id,  // Use 'id' from cart
                serviceName: service.name,  // Use 'name' from cart
                people: service.people,
                price: service.price,
                selectedDate: service.selectedDate,
                selectedTime: service.selectedTime,
                therapists: service.therapists || []  // Use 'therapists' (where selections are stored)
            })),
            totalAmount: checkoutData.totalAmount
        };

        // Store booking data for payment modal
        window.pendingBookingData = bookingData;

        // Close checkout modal and open payment modal
        $('#globalModal').modal('hide');

        setTimeout(() => {
            showGlobalModal('../../views/modal/user_modal-payment.php', bookingData);
        }, 300);
    });
    
    /**
     * Delete patient information from database if stored in session
     */
    function deletePatientInfoFromSession() {
        const patientId = sessionStorage.getItem('temp_patient_id');
        
        if (patientId) {
            console.log('🗑️ Deleting patient info (ID: ' + patientId + ')...');
            
            $.ajax({
                url: '../../controller/patient_contr.php',
                type: 'POST',
                data: {
                    action: 'delete_patient_info',
                    patient_id: patientId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        console.log('✅ Patient info deleted successfully');
                        sessionStorage.removeItem('temp_patient_id');
                    } else {
                        console.error('❌ Failed to delete patient info:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error deleting patient info:', error);
                }
            });
        }
    }
</script>

<style>
    .service-scheduling-card {
        border-left: 4px solid #007bff;
    }

    .person-therapist-selection {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6 !important;
    }

    .therapist-info {
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .therapist-info:hover {
        background-color: #e9ecef !important;
    }

    .therapist-radio:checked+.form-check-label .therapist-info {
        background-color: #e3f2fd !important;
        border-color: #007bff !important;
    }

    .summary-item {
        background-color: #f8f9fa;
    }

    .modal-body {
        max-height: 80vh;
        overflow-y: auto;
    }

    .date-selector,
    .time-selector {
        border-radius: 0.375rem;
    }

    .date-selector:focus,
    .time-selector:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* Therapist Selection Styles */
    .therapist-selection-section {
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .therapist-option {
        transition: all 0.2s ease-in-out;
    }

    .therapist-card {
        transition: all 0.2s ease-in-out;
        cursor: pointer;
        background-color: #fff;
    }

    .therapist-card:hover {
        background-color: #f8f9fa;
        border-color: #007bff !important;
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.15);
    }

    .form-check-input:checked + .form-check-label .therapist-card {
        background-color: #e3f2fd;
        border-color: #007bff !important;
        border-width: 2px !important;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }

    .therapist-loading {
        padding: 2rem 1rem;
    }

    @media (max-width: 576px) {
        .person-therapist-selection {
            margin-bottom: 1rem;
        }

        .modal-body {
            padding: 1rem 0.5rem;
        }

        .therapist-card {
            font-size: 0.9rem;
        }
    }
</style>