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
                        <button class="btn btn-primary w-100 mb-3">Check-out</button>

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
    // Safe to do AJAX now
    setTimeout(function() {
        $('#user_home_page').addClass('active');
    }, 500);

    $(document).ready(function() {
        loadServices();
        loadBookingStatus();
        loadRecentServices();
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
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
                                <div class="service-card spa-card d-flex flex-column align-items-center w-100 p-3"
                                    style="cursor:pointer; border:2px solid #c0967e; min-width:0;"
                                    data-service-id="${service.id}"
                                    data-service-name="${service.service_name}"
                                    data-service-price="${service.price}"
                                    data-service-description="${service.description}"
                                    data-service-image="${imageSrc}">
                                    <img src="${imageSrc}"
                                        class="spa-card-img rounded-4 mb-3"
                                        style="width:140px; height:140px; object-fit:cover; background:#f8f8f8; border:3px solid #c0967e;"
                                        alt="${service.service_name}"
                                        onerror="this.src='../../vendor/images/headMassage.png'">
                                    <h4 class="mb-2 fw-bold spa-title text-center" style="font-size:1.25rem; letter-spacing:1px;">
                                        ${service.service_name}
                                    </h4>
                                    <div class="fw-bold text-success mb-2 text-center" style="font-size:1.1rem;">
                                        ₱${service.price} <span class="text-muted fw-normal" style="font-size:1rem;">/ ${service.per_minute} min</span>
                                    </div>
                                    <div class="spa-desc text-center" style="font-size:1.07rem; color:#444;">${service.description}</div>
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
    window.serviceCart = window.serviceCart || []; // Preserve existing cart or create new
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

        // Open the booking modal with service details
        showGlobalModal('../../views/modal/user_modal-booking.php', {
            id: serviceId,
            name: serviceName,
            price: servicePrice,
            description: serviceDescription,
            image: serviceImage
        });
    });

    // Function to update checkout badge (make it global)
    window.updateCheckoutBadge = function() {
        // Always use the global cart reference
        const cart = window.serviceCart || [];
        serviceCart = [...cart]; // Sync local reference with a fresh copy

        // Find checkout button with multiple selectors to ensure we get it
        let checkoutBtn = $('.btn-primary:contains("Check-out")');
        if (checkoutBtn.length === 0) {
            checkoutBtn = $('.btn-success:contains("Check-out")');
        }
        if (checkoutBtn.length === 0) {
            checkoutBtn = $('.btn:contains("Check-out")').first();
        }

        const totalItems = cart.length;
        const totalPrice = cart.reduce((sum, service) => sum + (service.price * service.people), 0);

        if (totalItems > 0) {
            checkoutBtn.html(`Check-out (${totalItems}) - ₱${totalPrice}`);
            checkoutBtn.removeClass('btn-primary').addClass('btn-success');
        } else {
            checkoutBtn.html('Check-out');
            checkoutBtn.removeClass('btn-success').addClass('btn-primary');
        }

        console.log('Cart updated - Items:', totalItems, 'Total:', totalPrice); // Debug log
    };

    // Checkout button click handler
    $(document).on('click', '.btn:contains("Check-out")', function(e) {
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
    .service-card {
        transition:
            box-shadow 0.3s cubic-bezier(.4, 2, .6, 1),
            transform 0.2s cubic-bezier(.4, 2, .6, 1),
            border-color 0.3s;
        box-shadow: 0 2px 12px rgba(192, 150, 126, 0.08);
        border: 2px solid #c0967e;
        background: #fff;
        position: relative;
        z-index: 1;
        min-width: 0;
    }

    .service-card:hover,
    .service-card:focus-within {
        box-shadow: 0 8px 32px 0 rgba(192, 150, 126, 0.25), 0 1.5px 8px 0 rgba(0, 0, 0, 0.08);
        transform: translateY(-6px) scale(1.03);
        border-color: #a87d4a;
        background: linear-gradient(135deg, #fff8f3 0%, #f9e7d3 100%);
        z-index: 2;
    }

    .service-card img {
        transition: box-shadow 0.3s, transform 0.3s;
    }

    .service-card:hover img {
        box-shadow: 0 4px 24px 0 rgba(200, 150, 100, 0.18);
        transform: scale(1.07) rotate(-2deg);
    }

    .service-card h4,
    .service-card h5 {
        transition: color 0.2s, text-shadow 0.2s;
    }

    .service-card:hover h4,
    .service-card:hover h5 {
        color: #a87d4a !important;
        text-shadow: 0 2px 8px #fff2e0;
    }

    .spa-card {
        background: #fffdfa;
        border-radius: 1.5rem;
        box-shadow: 0 2px 12px rgba(192, 150, 126, 0.08);
        transition: box-shadow 0.3s, transform 0.2s, border-color 0.3s, background 0.3s;
        border: 2px solid #c0967e;
        position: relative;
        z-index: 1;
    }

    .spa-card:hover,
    .spa-card:focus-within {
        box-shadow: 0 8px 32px 0 rgba(192, 150, 126, 0.18), 0 1.5px 8px 0 rgba(0, 0, 0, 0.08);
        transform: translateY(-4px) scale(1.025);
        border-color: #a87d4a;
        background: linear-gradient(135deg, #fff8f3 0%, #f9e7d3 100%);
        z-index: 2;
    }

    .spa-card-img {
        transition: box-shadow 0.3s, transform 0.3s;
        box-shadow: 0 2px 8px rgba(192, 150, 126, 0.10);
    }

    .spa-card:hover .spa-card-img {
        box-shadow: 0 8px 24px 0 rgba(200, 150, 100, 0.18);
        transform: scale(1.06) rotate(-1.5deg);
    }

    .spa-title {
        color: #a87d4a !important;
        background: linear-gradient(90deg, #fff8f3 60%, #f9e7d3 100%);
        padding: 0.25em 0.7em;
        border-radius: 0.7em;
        display: inline-block;
        box-shadow: 0 1px 4px #fff2e0;
        transition: color 0.2s, background 0.2s, box-shadow 0.2s;
    }

    .spa-card:hover .spa-title {
        color: #fff !important;
        background: linear-gradient(90deg, #a87d4a 60%, #c0967e 100%);
        box-shadow: 0 2px 12px #f9e7d3;
    }

    .spa-desc {
        min-height: 3em;
        white-space: pre-line;
    }

    @media (max-width: 767.98px) {
        .spa-card {
            flex-direction: column !important;
            align-items: center !important;
            text-align: center;
        }

        .spa-card-img {
            margin-right: 0 !important;
            margin-bottom: 1rem;
        }
    }
</style>