<div class="modal-header">
  <h5 class="modal-title">Book Service</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
  <!-- Service Information -->
  <div class="mb-3">
    <p class="mb-1">Selected Service:</p>
    <h5 id="selected-service-name" class="fw-bold mb-1"></h5>
    <p id="selected-service-price" class="text-muted mb-0" data-price=""></p>
    <input type="hidden" id="selected-service-id" value="">
  </div>

  <!-- Number of People -->
  <div class="mb-3" id="numPeopleContainer">
    <label for="numPeople" class="form-label fw-semibold">Number of People:</label>
    <input type="number" class="form-control" id="numPeople" min="1" max="10" value="1">
    <div class="form-text">Maximum 10 people per booking</div>
  </div>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
  <button type="button" class="btn btn-primary" id="confirmServiceBtn">Add</button>
</div>

<script>
// Global variables for modal
var selectedServiceData = {};

// Initialize modal when document is ready
$(document).ready(function() {
    // Wait for modal data to be available
    setTimeout(() => {
        initializeBookingModal();
    }, 100);
});

// Initialize booking modal with service data
function initializeBookingModal() {
    console.log('🔄 Initializing booking modal...');
    
    // Get service data from modal data or window
    const serviceData = window.modalData || {};
    console.log('📋 Service data:', serviceData);
    
    if (serviceData && serviceData.id) {
        // Populate service information
        selectedServiceData = serviceData;
        
        $('#selected-service-name').text(serviceData.name || 'Unknown Service');
        $('#selected-service-price').text('₱' + (serviceData.price || 0)).attr('data-price', serviceData.price || 0);
        $('#selected-service-id').val(serviceData.id);
        
        // Check if stroke treatment to set number of people to 1
        const serviceName = (serviceData.name || '').toLowerCase();
        const isStrokeTreatment = serviceName.includes('stroke');
        
        if (isStrokeTreatment) {
            console.log('🏥 Stroke treatment detected - setting to 1 person only');
            $('#numPeopleContainer').hide();
            $('#numPeople').val(1);
            $('#selected-service-price').text('₱' + (serviceData.price || 0));
        } else {
            console.log('ℹ️ Regular service - allowing multiple people');
            $('#numPeopleContainer').show();
        }
        
    } else {
        console.error('❌ No service data provided to modal');
    }
}

// Event handler for number of people change (remove old handlers first)
$('#numPeople').off('input change').on('input change', function() {
    const numPeople = parseInt($(this).val());
    
    // Update price display (only if visible)
    if ($('#numPeopleContainer').is(':visible')) {
        const basePrice = parseFloat($('#selected-service-price').data('price'));
        const totalPrice = basePrice * numPeople;
        $('#selected-service-price').text(`₱${basePrice} × ${numPeople} = ₱${totalPrice}`);
    }
});

// Handle confirm button click (remove old handlers first)
$('#confirmServiceBtn').off('click').on('click', function() {
    const numPeople = parseInt($('#numPeople').val());
    
    if (numPeople < 1 || numPeople > 10) {
        Swal.fire({
            icon: 'warning',
            title: 'Invalid Number',
            text: 'Please enter a valid number of people (1-10).',
        });
        return;
    }
    
    // Add service to cart
    addServiceToCart(selectedServiceData, numPeople);
});

// Add service to cart function
function addServiceToCart(serviceData, numPeople) {
    console.log('🛒 Adding to cart:', serviceData, 'for', numPeople, 'people');
    
    // Initialize cart if needed
    if (!window.serviceCart) {
        window.serviceCart = [];
    }
    
    // Check for duplicates - allow multiple of the same service
    // Only check if trying to add exact same configuration
    const existingIndex = window.serviceCart.findIndex(s => 
        s.id === serviceData.id && 
        s.people === numPeople
    );
    
    if (existingIndex >= 0) {
        Swal.fire({
            icon: 'info',
            title: 'Service Already Added',
            text: 'This exact service configuration is already in your cart.',
        });
        return;
    }
    
    // Create service object with correct property names
    const service = {
        id: serviceData.id,
        name: serviceData.name,
        price: parseFloat(serviceData.price),
        description: serviceData.description,
        image: serviceData.image,
        people: numPeople
    };
    
    // Add to global cart
    window.serviceCart.push(service);
    
    console.log('✅ Service added to cart:', service);
    console.log('🛒 Current cart:', window.serviceCart);
    
    // Save cart to LocalStorage if function exists
    if (typeof window.saveCartToStorage === 'function') {
        window.saveCartToStorage();
    }
    
    // Update checkout badge if function exists
    if (typeof window.updateCheckoutBadge === 'function') {
        window.updateCheckoutBadge();
    }
    
    // Show success message and close modal
    Swal.fire({
        icon: 'success',
        title: 'Added to Cart!',
        text: `${serviceData.name} for ${numPeople} ${numPeople === 1 ? 'person' : 'people'} added to cart.`,
        timer: 1500,
        showConfirmButton: false
    }).then(() => {
        $('#globalModal').modal('hide');
    });
}
</script>

<style>
.modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

#selected-service-name {
    color: #0d6efd;
}

.form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
</style>
