<div class="modal-header bg-primary text-white">
  <h5 class="modal-title">
    <i class="bi bi-person-lines-fill me-2"></i>Patient Information
  </h5>
  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
  <div class="alert alert-info mb-4">
    <i class="bi bi-info-circle me-2"></i>
    <strong>Important:</strong> Please provide patient information before booking this therapy service.
  </div>

  <form id="patientInfoForm">
    <!-- Hidden field for service details -->
    <input type="hidden" id="patient-service-id" value="">
    <input type="hidden" id="patient-service-name" value="">
    <input type="hidden" id="patient-service-price" value="">
    <input type="hidden" id="patient-service-description" value="">
    <input type="hidden" id="patient-service-image" value="">

    <!-- Full Name -->
    <div class="mb-3">
      <label for="patient-full-name" class="form-label fw-semibold">
        <i class="bi bi-person me-1"></i>Full Name <span class="text-danger">*</span>
      </label>
      <input type="text" 
             class="form-control" 
             id="patient-full-name" 
             placeholder="Enter patient's full name"
             required>
      <div class="invalid-feedback">Please enter the patient's full name.</div>
    </div>

    <!-- Age -->
    <div class="mb-3">
      <label for="patient-age" class="form-label fw-semibold">
        <i class="bi bi-calendar-event me-1"></i>Age <span class="text-danger">*</span>
      </label>
      <input type="number" 
             class="form-control" 
             id="patient-age" 
             min="1" 
             max="150" 
             placeholder="Enter patient's age"
             required>
      <div class="invalid-feedback">Please enter a valid age (1-150).</div>
    </div>

    <!-- Gender -->
    <div class="mb-3">
      <label class="form-label fw-semibold">
        <i class="bi bi-gender-ambiguous me-1"></i>Gender <span class="text-danger">*</span>
      </label>
      <div class="row">
        <div class="col-md-4">
          <div class="form-check">
            <input class="form-check-input" 
                   type="radio" 
                   name="patient-gender" 
                   id="gender-male" 
                   value="Male"
                   required>
            <label class="form-check-label" for="gender-male">
              <i class="bi bi-gender-male text-primary"></i> Male
            </label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-check">
            <input class="form-check-input" 
                   type="radio" 
                   name="patient-gender" 
                   id="gender-female" 
                   value="Female"
                   required>
            <label class="form-check-label" for="gender-female">
              <i class="bi bi-gender-female text-danger"></i> Female
            </label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-check">
            <input class="form-check-input" 
                   type="radio" 
                   name="patient-gender" 
                   id="gender-other" 
                   value="Other"
                   required>
            <label class="form-check-label" for="gender-other">
              <i class="bi bi-gender-ambiguous text-secondary"></i> Other
            </label>
          </div>
        </div>
      </div>
      <div class="invalid-feedback d-block" id="gender-error" style="display: none !important;">
        Please select a gender.
      </div>
    </div>

    <!-- Additional Notes (Optional) -->
    <div class="mb-3">
      <label for="patient-notes" class="form-label fw-semibold">
        <i class="bi bi-pencil-square me-1"></i>Additional Notes (Optional)
      </label>
      <textarea class="form-control" 
                id="patient-notes" 
                rows="3" 
                placeholder="Any additional information about the patient..."></textarea>
      <div class="form-text">This information will help us provide better care</div>
    </div>
  </form>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
    <i class="bi bi-x-circle me-1"></i>Cancel
  </button>
  <button type="button" class="btn btn-primary" id="confirmPatientInfoBtn">
    <i class="bi bi-check-circle me-1"></i>Confirm & Continue
  </button>
</div>

<script>
$(document).ready(function() {
    // Wait for modal data to be available
    setTimeout(() => {
        initializePatientInfoModal();
    }, 100);
});

function initializePatientInfoModal() {
    console.log('🔄 Initializing patient info modal...');
    
    // Get service data from modal data
    const serviceData = window.modalData || {};
    console.log('📋 Service data:', serviceData);
    
    if (serviceData && serviceData.id) {
        $('#patient-service-id').val(serviceData.id);
        $('#patient-service-name').val(serviceData.name);
        $('#patient-service-price').val(serviceData.price);
        $('#patient-service-description').val(serviceData.description);
        $('#patient-service-image').val(serviceData.image);
    }

    // Load cached patient data if exists
    const cachedPatientData = sessionStorage.getItem('patient_cache');
    if (cachedPatientData) {
        try {
            const patientData = JSON.parse(cachedPatientData);
            $('#patient-full-name').val(patientData.full_name || '');
            $('#patient-age').val(patientData.age || '');
            if (patientData.gender) {
                $(`input[name="patient-gender"][value="${patientData.gender}"]`).prop('checked', true);
            }
            $('#patient-notes').val(patientData.notes || '');
            console.log('✅ Loaded cached patient data');
        } catch (e) {
            console.error('❌ Error loading cached patient data:', e);
        }
    }
}

// Handle form submission (remove old handlers first to prevent duplicates)
$('#confirmPatientInfoBtn').off('click').on('click', function() {
    const form = $('#patientInfoForm')[0];
    
    // Validate form
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        
        // Check if gender is selected
        if (!$('input[name="patient-gender"]:checked').val()) {
            $('#gender-error').show();
        }
        
        Swal.fire({
            icon: 'warning',
            title: 'Incomplete Form',
            text: 'Please fill in all required fields.',
        });
        return;
    }

    // Get form data
    const patientData = {
        full_name: $('#patient-full-name').val().trim(),
        age: parseInt($('#patient-age').val()),
        gender: $('input[name="patient-gender"]:checked').val(),
        notes: $('#patient-notes').val().trim()
    };

    // Get service data
    const serviceData = {
        id: $('#patient-service-id').val(),
        name: $('#patient-service-name').val(),
        price: $('#patient-service-price').val(),
        description: $('#patient-service-description').val(),
        image: $('#patient-service-image').val()
    };

    console.log('Patient data to cache:', patientData);

    // Save patient data to sessionStorage (cache)
    sessionStorage.setItem('patient_cache', JSON.stringify(patientData));
    console.log('💾 Patient data cached to sessionStorage');

    // Automatically add to cart for stroke services
    const cartItem = {
        id: serviceData.id,
        name: serviceData.name,
        price: parseFloat(serviceData.price),
        description: serviceData.description,
        image: serviceData.image,
        people: 1, // Stroke services are always 1 person
        patientData: patientData // Attach patient data to cart item
    };
    
    // Add to global cart (window.serviceCart)
    if (!window.serviceCart) {
        window.serviceCart = [];
    }
    window.serviceCart.push(cartItem);
    
    console.log('🛒 Added to cart:', cartItem);
    console.log('🛒 Current cart:', window.serviceCart);
    
    // Update checkout badge if function exists
    if (typeof window.updateCheckoutBadge === 'function') {
        window.updateCheckoutBadge();
    }
    
    // Show success message and close modal
    Swal.fire({
        icon: 'success',
        title: 'Added to Cart!',
        text: 'Patient information saved and service added to cart.',
        timer: 2000,
        showConfirmButton: false
    }).then(() => {
        // Close patient info modal
        $('#globalModal').modal('hide');
    });
});

// Real-time validation
$('#patient-full-name').on('input', function() {
    this.setCustomValidity('');
});

$('#patient-age').on('input', function() {
    const age = parseInt($(this).val());
    if (age < 1 || age > 150) {
        this.setCustomValidity('Age must be between 1 and 150');
    } else {
        this.setCustomValidity('');
    }
});

$('input[name="patient-gender"]').on('change', function() {
    $('#gender-error').hide();
});
</script>

<style>
.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

.form-control:focus, .form-check-input:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.was-validated .form-control:invalid,
.was-validated .form-check-input:invalid {
    border-color: #dc3545;
}

.was-validated .form-control:valid,
.was-validated .form-check-input:valid {
    border-color: #198754;
}
</style>