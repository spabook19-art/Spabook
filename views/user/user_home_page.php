<?php include_once '../../include/header.php'; ?>

<div class="content_wrapper"">
    <div class=" app_sidebar_container d-flex">
    <div class="app_sidebar_nav app_sidebar_bg_it_asset d-flex flex-column justify-content-between sidebar-hidden" id="app_sidebar_nav"></div>
    <div class="app_content_container">
        <nav class="navbar navbar-expand px-3 border-bottom" style="background-color: #C0967E;">
            <button class="btn app_open_sidebar_btn" type="button">
                <span class="navbar-toggler-icon"></span>
            </button>
            <span class="app_content_title fs-25 fw-bold pe-2">Book appointment</span>
        </nav>
        <div class="app_content_body">
            <div class="container-fluid h-100 overflow-hidden overflow-auto">
                <div class="row g-3 mt-2">


                    <!-- Left Side: Services Grid -->
                    <div class="col-lg-8 col-md-7 col-sm-12">
                        <div id="services_container" class="row g-3 overflow-auto" style="max-height: calc(100vh - 160px);"></div>
                    </div>

                    <!-- Right Side: Status and Recent Services -->
                    <div class="col-lg-4 col-md-5 col-sm-12">
                        <button class="btn btn-primary w-100 mb-2 position-relative" id="bookAppointmentBtn">
                            <i class="bi bi-calendar-check me-2"></i>Book-appointment
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="cartBadge">
                                0
                            </span>
                        </button>

                        <!-- Booking Status Section -->
                        <div class="card mb-3" style="background-color: transparent; border: none;">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Booking Status</h5>
                            </div>
                            <div class="booking-status-scroll">
                                <ul class="list-group list-group-flush" id="bookingStatusList" style="overflow-y: auto; max-height: calc(100vh - 400px);">
                                    <!-- Booking status will be loaded here -->
                                </ul>
                            </div>
                        </div>

                        <!-- Recent Services Section -->
                        <div class="card" style="background-color: transparent; border: none;">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0">Recent Services</h5>
                            </div>
                            <div class="recent-services-scroll">
                                <ul class="list-group list-group-flush" id="recentServicesList" style="overflow-y: auto; max-height: calc(100vh - 400px);">
                                    <!-- Recent services will be loaded here -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal container -->
<div id="modalContainer"></div>

<?php include_once '../../include/footer.php';
include_once '../../helper/user_apps.php' ?>

<script>
    // Initialize cart early (before anything else)
    window.serviceCart = window.serviceCart || [];
    console.log('🛒 Cart initialized:', window.serviceCart);

    // Safe to do AJAX now
    setTimeout(function() {
        $('#user_home_page').addClass('active');
    }, 500);

    $(document).ready(function() {
        loadServices();
        loadBookingStatus();
        loadRecentServices();
    });

    // Load cart AFTER user_id is ready in sessionStorage
    window.addEventListener('user_id_ready', function() {
        // Try to load cart with retry mechanism
        let attempts = 0;
        const maxAttempts = 10;
        
        function tryLoadCart() {
            attempts++;
            console.log('🔄 Attempt', attempts, 'to load cart...');
            
            if (typeof window.loadCartFromStorage === 'function') {
                const userId = sessionStorage.getItem('user_id');
                console.log('✅ Found loadCartFromStorage! Loading cart for user:', userId);
                
                window.serviceCart = window.loadCartFromStorage() || [];
                
                if (window.serviceCart.length > 0) {
                    console.log('✅ Cart loaded with items:', window.serviceCart);
                    if (typeof window.updateCheckoutBadge === 'function') {
                        window.updateCheckoutBadge();
                    }
                } else {
                    console.log('ℹ️ Cart is empty after loading');
                }
            } else {
                console.warn('⚠️ loadCartFromStorage not available yet, attempt', attempts);
                
                if (attempts < maxAttempts) {
                    setTimeout(tryLoadCart, 200); // Try again after 200ms
                } else {
                    console.error('❌ Failed to load cart after', maxAttempts, 'attempts');
                    console.error('❌ Available functions:', Object.keys(window).filter(k => k.includes('Cart')));
                }
            }
        }
        
        // Start trying after 100ms
        setTimeout(tryLoadCart, 100);
    });

    // Also load data when this page becomes visible
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            console.log('Page is now visible, reloading services');
            loadServices();
            loadBookingStatus();
            loadRecentServices();
        }
    });

    // Create a global function to reload services that can be called from other pages
    window.reloadBookingServices = function() {
        loadServices();
        loadBookingStatus();
        loadRecentServices();
    };
    // Views Script
    // Make loadServices globally accessible
    window.loadServices = function() {
        console.log('Loading services...');
        $.ajax({
            url: '../../controller/booking_services_contr.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'fetch_services'
            },
            success: result => {
                if (result === 'nodata') {
                    $('#services_container').html(`
                        <div class="card text-center border-0 shadow-sm p-4 rounded-4 bg-light w-100">
                            <div class="card-body">
                                <i class="bi bi-info-circle text-secondary mb-2" style="font-size: 2rem;"></i>
                                <h5 class="card-title mb-2">No Services Available</h5>
                                <p class="card-text text-muted">Please check back later or contact support for assistance.</p>
                            </div>
                        </div>
                    `);
                    return;
                }

                let html = '';
                result.forEach(service => {
                    let imageSrc;
                    if (service.service_picture.startsWith('http')) {
                        // It's a URL, use it directly
                        imageSrc = service.service_picture;
                    } else if (service.service_picture.startsWith('data:image')) {
                        // It's already a data URL, use it directly
                        imageSrc = service.service_picture;
                    } else {
                        // It's base64 data without prefix, add the prefix
                        imageSrc = `data:image/png;base64,${service.service_picture}`;
                    }

                    html += `
                            <div class="service-card" data-service-id="${service.id}" data-service-name="${service.service_name}" data-service-price="${service.price}" data-service-description="${service.description}" data-service-image="${imageSrc}">
                            <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden position-relative">
                                <img src="${imageSrc}"
                                    class="card-img-top img-fluid"
                                    style="height: 120px; object-fit: cover;"
                                    alt="${service.service_name}"
                                    onerror="this.src='../vendor/images/headMassage.png'">

                                <div class="card-body bg-white">
                                    <h5 class="card-title">${service.service_name}</h5>
                                    <p class="card-text small mb-2">${service.description}</p>
                                    <p class="card-text fw-bold text-primary mb-0">
                                        ₱ ${service.price} / ${service.per_minute} min
                                    </p>
                                </div>
                            </div>
                        </div>
                        `;
                });
                $('#services_container').html(html);
            }
        });
    }

    // Function to load booking status - make it globally accessible
    window.loadBookingStatus = function() {
        const userId = sessionStorage.getItem('user_id');
        if (!userId) return;
        $.ajax({
            url: '../../controller/booking_contr.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_user_booking_status',
                user_id: userId
            },
            success: function(result) {
                console.log('Booking status data:', result);
                let statusHtml = '';

                if (result === 'nodata' || !result || result.length === 0) {
                    statusHtml = `
                        <li class="list-group-item text-center text-muted py-4">
                            <i class="bi bi-calendar-x mb-2" style="font-size: 2rem;"></i>
                            <div>No active bookings</div>
                        </li>
                    `;
                } else {
                    result.forEach(booking => {
                        const statusClass = getStatusClass(booking.status);
                        const statusIcon = getStatusIcon(booking.status);

                        statusHtml += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">${booking.service_name || 'Service'}</h6>
                                    <small class="text-muted">${booking.booking_date || ''}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge ${statusClass} mb-1">
                                        <i class="bi ${statusIcon} me-1"></i>${booking.status}
                                    </span>
                                    <div class="text-muted small">₱${booking.total_amount || 0}</div>
                                </div>
                            </li>
                        `;
                    });
                }

                $('#bookingStatusList').html(statusHtml);
            },
            error: function() {
                $('#bookingStatusList').html(`
                    <li class="list-group-item text-center text-danger py-4">
                        <i class="bi bi-exclamation-circle mb-2" style="font-size: 2rem;"></i>
                        <div>Failed to load booking status</div>
                    </li>
                `);
            }
        });
    }

    // Function to load recent services - make it globally accessible
    window.loadRecentServices = function() {
        console.log('Loading recent services...');
        const userId = sessionStorage.getItem('user_id');
        if (!userId) return;

        $.ajax({
            url: '../../controller/booking_contr.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_user_recent_services',
                user_id: userId
            },
            success: function(result) {
                let servicesHtml = '';

                if (result === 'nodata' || !result || result.length === 0) {
                    servicesHtml = `
                        <li class="list-group-item text-center text-muted py-4">
                            <i class="bi bi-clock-history mb-2" style="font-size: 2rem;"></i>
                            <div>No recent services</div>
                        </li>
                    `;
                } else {
                    result.forEach(service => {
                        const serviceDate = new Date(service.booking_date).toLocaleDateString('en-US', {
                            month: 'short',
                            day: 'numeric',
                            year: 'numeric'
                        });

                        servicesHtml += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">${service.service_name}</div>
                                    <small class="text-muted">${serviceDate}</small>
                                </div>
                                <div class="text-end">
                                    <div class="text-primary fw-bold">₱${service.total_amount}</div>
                                    <small class="text-success">${service.status}</small>
                                </div>
                            </li>
                        `;
                    });
                }

                $('#recentServicesList').html(servicesHtml);
            },
            error: function() {
                $('#recentServicesList').html(`
                    <li class="list-group-item text-center text-danger py-4">
                        <i class="bi bi-exclamation-circle mb-2" style="font-size: 2rem;"></i>
                        <div>Failed to load recent services</div>
                    </li>
                `);
            }
        });
    }

    // Function to refresh booking data after successful booking
    window.refreshBookingData = function() {
        console.log('Refreshing booking data...');
        window.loadBookingStatus();
        window.loadRecentServices();
    }

    // Helper functions for status styling
    function getStatusClass(status) {
        switch (status?.toLowerCase()) {
            case 'pending':
                return 'bg-warning text-dark';
            case 'confirmed':
            case 'accepted':
                return 'bg-success';
            case 'completed':
                return 'bg-primary';
            case 'cancelled':
            case 'rejected':
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }

    function getStatusIcon(status) {
        switch (status?.toLowerCase()) {
            case 'pending':
                return 'bi-clock';
            case 'confirmed':
            case 'accepted':
                return 'bi-check-circle';
            case 'completed':
                return 'bi-check2-all';
            case 'cancelled':
            case 'rejected':
                return 'bi-x-circle';
            default:
                return 'bi-circle';
        }
    }

    // Initialize service cart (make it globally accessible)
    let serviceCart = [];
    window.serviceCart = window.serviceCart || []; // Initialize empty cart
    serviceCart = window.serviceCart; // Sync local reference

    // Event delegation for service card clicks
    $(document).on('click', '.service-card', function() {
        const serviceId = $(this).data('service-id');
        const serviceName = $(this).data('service-name');
        const servicePrice = $(this).data('service-price');
        const serviceDescription = $(this).data('service-description');
        const serviceImage = $(this).data('service-image');

        // Store the selected element reference for visual feedback
        window.selectedServiceElement = $(this);

        // Check if this is a stroke therapy service
        const isStrokeTreatment = serviceName.toLowerCase().includes('stroke');

        // If it's a stroke treatment, show patient info form first
        if (isStrokeTreatment) {
            console.log('🏥 Stroke treatment service detected - showing patient info form');
            showGlobalModal('../../views/modal/user_modal-patient-info.php', {
                id: serviceId,
                name: serviceName,
                price: servicePrice,
                description: serviceDescription,
                image: serviceImage
            });
        } else {
            // For other services, show booking modal directly
            showGlobalModal('../../views/modal/user_modal-booking.php', {
                id: serviceId,
                name: serviceName,
                price: servicePrice,
                description: serviceDescription,
                image: serviceImage
            });
        }
    });

    // Function to update checkout badge (make it global)
    window.updateCheckoutBadge = function() {
        // Always use the global cart reference
        const cart = window.serviceCart || [];
        serviceCart = [...cart]; // Sync local reference with a fresh copy

        // Use the button with ID
        const bookingBtn = $('#bookAppointmentBtn');
        
        const totalItems = cart.length;
        const totalPrice = cart.reduce((sum, service) => sum + (service.price * service.people), 0);

        if (totalItems > 0) {
            // Update button to show total price with badge showing item count
            bookingBtn.html(`
                <i class="bi bi-calendar-check me-2"></i>Book-appointment - ₱${totalPrice.toLocaleString()}
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cartBadge" style="animation: badgePulse 0.5s ease-in-out;">
                    ${totalItems}
                </span>
            `);
            bookingBtn.removeClass('btn-primary').addClass('btn-success');
        } else {
            // Reset button to default state with hidden badge
            bookingBtn.html(`
                <i class="bi bi-calendar-check me-2"></i>Book-appointment
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="cartBadge">
                    0
                </span>
            `);
            bookingBtn.removeClass('btn-success').addClass('btn-primary');
        }

        console.log('Cart updated - Items:', totalItems, 'Total:', totalPrice); // Debug log
    };

    // Checkout button click handler
    $(document).on('click', '.btn:contains("Book-appointment")', function(e) {
        e.preventDefault();
        const currentCart = window.serviceCart || [];

        if (currentCart.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'No Services Selected',
                text: 'Please select at least one service to proceed with checkout.',
            });
            return;
        }

        // Show checkout modal or proceed to checkout
        showGlobalModal('../../views/modal/user_modal-checkout.php', {
            cart: currentCart
        });
    });

    // Initialize cart on page load
    window.updateCheckoutBadge();

    // Function to refresh all data sections
    function refreshAllData() {
        loadBookingStatus();
        loadRecentServices();
        window.updateCheckoutBadge();
    }

    // Make refresh function globally accessible
    window.refreshBookingData = refreshAllData;

    // Override the global modal ready function for checkout modal
    window.originalOnGlobalModalReady = window.onGlobalModalReady;
    window.onGlobalModalReady = function() {
        // Check if this is the new checkout modal with scheduling
        if ($('#servicesSchedulingContainer').length > 0) {
            // The new checkout modal will initialize itself
            console.log('New checkout modal detected - letting it self-initialize');
        }
        // Check if this is the old checkout modal (fallback)
        else if ($('#checkoutServiceList').length > 0) {
            populateOldCheckoutModal();
        } else if (window.originalOnGlobalModalReady) {
            // Call original function for other modals
            window.originalOnGlobalModalReady();
        }
    };

    // Legacy checkout modal population (fallback)
    function populateOldCheckoutModal() {
        const cart = window.serviceCart || [];

        let serviceListHtml = '';
        let total = 0;

        cart.forEach((service, index) => {
            const serviceTotal = service.price * service.people;
            total += serviceTotal;

            serviceListHtml += `
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${service.name}</h6>
                        <small class="text-muted">${service.people} person(s) × ₱${service.price}</small>
                    </div>
                    <div class="d-flex align-items-center ms-2">
                        <span class="badge bg-primary me-2">₱${serviceTotal}</span>
                        <button class="btn btn-sm btn-outline-danger remove-service-btn" data-index="${index}" title="Remove service">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </li>
            `;
        });

        $('#checkoutServiceList').html(serviceListHtml);
        $('#checkoutTotal').text(`₱${total}`);

        // Handle remove service buttons
        $('.remove-service-btn').off('click').on('click', function() {
            const index = $(this).data('index');
            window.serviceCart.splice(index, 1);
            window.updateCheckoutBadge();

            if (window.serviceCart.length === 0) {
                $('#globalModal').modal('hide');
                Swal.fire({
                    icon: 'info',
                    title: 'Cart Empty',
                    text: 'All services have been removed from your cart.',
                });
            } else {
                populateOldCheckoutModal();
            }
        });
    }


    $.ajax({
        type: 'POST',
        url: '../../controller/user_contr.php',
        data: {
            action: 'get_user_profile',
            id: sessionStorage.getItem('user_id')
        },
        dataType: 'json',
        success: function(data) {
            if (data.profile_image && data.profile_image !== "") {
                $('#navProfileImage').attr('src', data.profile_image);
            } else {
                $('#navProfileImage').attr('src', '../../vendor/images/default_profile.png');
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'AJAX error: ' + error,
            });
        }
    });
</script>

<style>
    /* Modern hover effect for service cards */

    .responsive-col {
        flex: 0 0 100%;
    }

    .service-img-wrapper {
        width: 100%;
        height: 150px;
        overflow: hidden;
        border-radius: 0.5rem 0.5rem 0 0;
    }

    .service-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    @media (min-width: 576px) {
        .responsive-col {
            flex: 0 0 50%;
        }

        /* .service-img-wrapper {
        height: 100%;
    } */
    }


    @media (min-width: 768px) {
        .responsive-col {
            flex: 0 0 33.3333%;
        }

        /* .service-img-wrapper {
        height: 100%
    } */
    }


    @media (min-width: 992px) {
        .responsive-col {
            flex: 0 0 33.3333%;
        }
    }

    @media (min-width: 1200px) {
        .responsive-col {
            flex: 0 0 33.3333%;
        }
    }

    #services_container {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .service-card {
        flex: 1 1 250px;
        /* grow, shrink, base width */
        max-width: 100%;
        min-width: 200px;
    }

    .card:hover {
        border-radius: 1.5rem;
        box-shadow: 0 0 20px rgba(0, 0, 0, 1) !important;
        transition: box-shadow 0.2s ease-in-out;
    }

    .card-img-top {
        height: 120px;
        object-fit: cover;
    }

    .card-body {
        padding: 1rem;
    }

    .booking-status-scroll ul,
    .recent-services-scroll ul {
        overflow-y: auto;
        max-height: 25vh;
        padding-right: 8px;
        scrollbar-width: thin;
        scrollbar-color: #ccc transparent;
    }

    .booking-status-scroll ul::-webkit-scrollbar,
    .recent-services-scroll ul::-webkit-scrollbar {
        width: 6px;
    }

    .booking-status-scroll ul::-webkit-scrollbar-thumb,
    .recent-services-scroll ul::-webkit-scrollbar-thumb {
        background-color: #ccc;
        border-radius: 3px;
    }

    /* Empty state styling */
    .list-group-item.text-center.text-muted {
        border: 1px dashed #dee2e6;
        background-color: #f8f9fa;
    }

    .list-group-item.text-center.text-danger {
        border: 1px dashed #dc3545;
        background-color: #f8d7da;
    }

    @media (max-width: 991.98px) {

        .col-lg-8,
        .col-lg-4 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .service-card {
            flex: 1 1 100%;
        }

        /* Adjust section heights on mobile */
        .booking-status-scroll ul,
        .recent-services-scroll ul {
            max-height: 20vh;
        }
    }

    /* Status badge animations */
    .badge {
        transition: all 0.2s ease-in-out;
    }

    .badge:hover {
        transform: scale(1.05);
    }

    /* Cart badge styling and animation */
    #cartBadge {
        font-size: 0.75rem;
        font-weight: 600;
        min-width: 20px;
        height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        animation: badgePulse 0.5s ease-in-out;
    }

    @keyframes badgePulse {
        0% {
            transform: translate(-50%, -50%) scale(0.5);
            opacity: 0;
        }
        50% {
            transform: translate(-50%, -50%) scale(1.2);
        }
        100% {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }
    }

    #bookAppointmentBtn {
        position: relative;
        overflow: visible;
        transition: all 0.3s ease-in-out;
    }

    #bookAppointmentBtn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    /* List item hover effects */
    .list-group-item {
        transition: background-color 0.2s ease-in-out;
    }

    .list-group-item:hover {
        background-color: #f8f9fa;
    }

    /* Service card hover effects */
    .service-card {
        cursor: pointer;
        transition: all 0.3s ease-in-out;
    }

    .service-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .service-card.selected {
        border: 2px solid #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
    }

    /* Checkout button enhancement */
    .btn:contains("Check-out") {
        font-weight: 600;
        transition: all 0.2s ease-in-out;
    }

    .btn-success:contains("Check-out") {
        box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
        }

        50% {
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.5);
        }

        100% {
            box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
        }
    }
</style>