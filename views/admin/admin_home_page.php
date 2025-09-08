<?php include_once '../../include/header.php'; ?>

<div class="content_wrapper">
    <div class="app_sidebar_container d-flex">
        <div class="app_sidebar_nav app_sidebar_bg_it_asset d-flex flex-column justify-content-between sidebar-hidden" id="app_sidebar_nav"></div>
        <div class="app_content_container">
            <nav class="navbar navbar-expand px-3 border-bottom" style="background-color: #C0967E;">
                <button class="btn app_open_sidebar_btn" type="button">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <span class="app_content_title fs-25 fw-bold pe-2">Dashboard</span>
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
                <div class="dashboard-view">
                    <!-- Dashboard Stats Cards -->
                    <div class="row row-cols-1 row-cols-sm-3">
                        <div class="col mb-4">
                            <div class="card rounded-1 custom_card_hover border-0 shadow active" onclick="loadDashboardData('Bookings')" id="bookings-card">
                                <div class="card-body">
                                    <div class="d-flex flex-column">
                                        <span class="fs-6 mb-1">Total Bookings</span>
                                        <div class="fs-2 fw-bold mb-1" id="total_bookings_count">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                        <span class="fs-6" id="bookings_subtitle">
                                            <i class="pe-2 bi bi-hourglass-split"></i>Loading...
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col mb-4">
                            <div class="card rounded-1 custom_card_hover border-0 shadow" onclick="loadDashboardData('History')" id="history-card">
                                <div class="card-body">
                                    <div class="d-flex flex-column">
                                        <span class="fs-6 mb-1">Appointment History</span>
                                        <div class="fs-2 fw-bold mb-1" id="history_count">
                                            <div class="spinner-border spinner-border-sm text-success" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                        <span class="fs-6" id="history_subtitle">
                                            <i class="pe-2 bi bi-clock-history"></i>Loading...
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col mb-4">
                            <div class="card rounded-1 custom_card_hover border-0 shadow" onclick="loadDashboardData('Recovery')" id="recovery-card">
                                <div class="card-body">
                                    <div class="d-flex flex-column">
                                        <span class="fs-6 mb-1">Recovery Management</span>
                                        <div class="fs-2 fw-bold mb-1" id="recovery_count">
                                            <div class="spinner-border spinner-border-sm text-warning" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                        <span class="fs-6" id="recovery_subtitle">
                                            <i class="pe-2 bi bi-arrow-repeat"></i>Loading...
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Data Display Card -->
                    <div class="card rounded-1 shadow mb-4">
                        <div class="card-header rounded-top-1 d-flex justify-content-between align-items-center">
                            <div class="col-auto">
                                <h5 class="mb-0" id="active_dashboard">Dashboard Overview</h5>
                            </div>
                        </div>
                        <div class="card-body" style="max-height: 45vh; overflow-y: auto;">
                            <!-- Loading State -->
                            <div id="dashboard_loading" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-3 text-muted">Loading dashboard data...</p>
                            </div>

                            <!-- Data Table Container -->
                            <div id="dashboard_table_container" style="display: none;">
                                <!-- Items per page selector -->
                                <div class="d-flex justify-content-end mb-3">
                                    <div class="form-inline">
                                        <label for="itemsPerPage" class="me-2">Items per page:</label>
                                        <select id="itemsPerPage" class="form-select form-select-sm" style="width: auto;">
                                            <option value="5">5</option>
                                            <option value="10" selected>10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                        </select>
                                    </div>
                                </div>

                                <table id="dashboard_data_table" class="table table-striped w-100">
                                    <thead class="table-secondary" id="table_header">
                                        <!-- Dynamic headers will be inserted here -->
                                    </thead>
                                </table>
                                <div class="table-body-scroll">
                                    <table class="table table-striped w-100">
                                        <tbody id="table_body">
                                            <!-- Dynamic data will be inserted here -->
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Pagination will be inserted here -->
                            </div>

                            <!-- Recovery Actions Container -->
                            <div id="recovery_actions_container" style="display: none;">
                                <!-- Items per page selector for recovery -->
                                <div class="d-flex justify-content-end mb-3">
                                    <div class="form-inline">
                                        <label for="recoveryItemsPerPage" class="me-2">Items per page:</label>
                                        <select id="recoveryItemsPerPage" class="form-select form-select-sm" style="width: auto;">
                                            <option value="5">5</option>
                                            <option value="10" selected>10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card border-warning">
                                            <div class="card-header bg-warning text-white">
                                                <h6 class="mb-0">
                                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                                    Recoverable Bookings
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div id="recoverable_bookings">
                                                    <!-- Recoverable bookings will be loaded here -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-success">
                                            <div class="card-header bg-success text-white">
                                                <h6 class="mb-0">
                                                    <i class="bi bi-check-circle me-2"></i>
                                                    Recently Recovered
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div id="recovered_bookings">
                                                    <!-- Recently recovered bookings will be loaded here -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Empty State -->
                            <div id="dashboard_empty_state" style="display: none;" class="text-center py-5">
                                <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                                <h5 class="text-muted">No Data Available</h5>
                                <p class="text-muted">There's no data to display for this section yet.</p>
                            </div>
                        </div>
                    </div>
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
        $('#admin_home_page').addClass('active');
    }, 500);
    // // Global variables for pagination
    var currentPage = 1;
    var currentLimit = 10;
    var currentAction = 'get_total_bookings';
    // Track the latest in-flight AJAX to avoid overlapping renders
    var currentAjax = null;
    // Guard against stale responses overriding newer ones
    var currentRequestId = 0;

    // Global functions for dashboard

    // Show dashboard loading state
    function showDashboardLoading() {
        $('#dashboard_loading').show();
        $('#dashboard_table_container').hide();
        $('#recovery_actions_container').hide();
        $('#dashboard_empty_state').hide();
    }

    // Load total bookings data
    function loadTotalBookings(page = 1, limit = 10) {
        console.log('Loading total bookings...');
        currentPage = page;
        currentLimit = limit;
        currentAction = 'get_total_bookings';
        const requestId = ++currentRequestId;

        // Show loading state
        $('#dashboard_loading').show();
        $('#dashboard_table_container').hide();
        $('#recovery_actions_container').hide();
        $('#dashboard_empty_state').hide();

        // Abort any previous request to prevent multiple tables rendering
        if (currentAjax && currentAjax.readyState !== 4) {
            try {
                currentAjax.abort();
            } catch (e) {}
        }
        currentAjax = $.ajax({
            url: '../../controller/admin_dashboard_contr.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_total_bookings',
                page: page,
                limit: limit
            },
            success: function(response) {
                if (requestId !== currentRequestId) return; // ignore stale responses
                console.log('Total bookings response:', response);
                $('#dashboard_loading').hide();

                if (response.status === 'success' && response.data && Array.isArray(response.data) && response.data.length > 0) {
                    displayBookingsTable(response.data, response.pagination);
                } else if (response.status === 'success' && response.data && Array.isArray(response.data) && response.data.length === 0) {
                    // Show empty state for successful response with no data
                    $('#dashboard_empty_state').show();
                    console.log('No booking data available (empty array)');
                } else {
                    // Show empty state for other cases
                    $('#dashboard_empty_state').show();
                    console.log('No booking data available or invalid response format:', response);

                    // If there's an error message, display it
                    if (response.status === 'error' && response.message) {
                        console.error('Error from server:', response.message);
                        // You could display this error message to the user if needed
                    }
                }
            },
            error: function(xhr, status, error) {
                if (status === 'abort' || requestId !== currentRequestId) return; // ignore aborted/stale
                $('#dashboard_loading').hide();
                $('#dashboard_empty_state').show();
                console.error('Error loading total bookings:', error);
            }
        });
    }

    // Load appointment history data
    function loadAppointmentHistory(page = 1, limit = 10) {
        currentPage = page;
        currentLimit = limit;
        currentAction = 'get_appointment_history';
        const requestId = ++currentRequestId;

        if (currentAjax && currentAjax.readyState !== 4) {
            try {
                currentAjax.abort();
            } catch (e) {}
        }
        currentAjax = $.ajax({
            url: '../../controller/admin_dashboard_contr.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_appointment_history',
                page: page,
                limit: limit
            },
            success: function(response) {
                if (requestId !== currentRequestId) return;
                $('#dashboard_loading').hide();

                if (response.status === 'success' && response.data.length > 0) {
                    displayHistoryTable(response.data, response.pagination);
                } else {
                    $('#dashboard_empty_state').show();
                }
            },
            error: function(xhr, status, error) {
                if (status === 'abort' || requestId !== currentRequestId) return;
                $('#dashboard_loading').hide();
                $('#dashboard_empty_state').show();
                console.error('Error loading appointment history:', error);
            }
        });
    }

    // Load recovery data
    function loadRecoveryData(page = 1, limit = 10) {
        currentPage = page;
        currentLimit = limit;
        currentAction = 'get_recovery_data';
        const requestId = ++currentRequestId;

        if (currentAjax && currentAjax.readyState !== 4) {
            try {
                currentAjax.abort();
            } catch (e) {}
        }
        currentAjax = $.ajax({
            url: '../../controller/admin_dashboard_contr.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_recovery_data',
                page: page,
                limit: limit
            },
            success: function(response) {
                if (requestId !== currentRequestId) return;
                $('#dashboard_loading').hide();

                if (response.status === 'success') {
                    displayRecoveryData(response.data, response.pagination);
                } else {
                    $('#dashboard_empty_state').show();
                }
            },
            error: function(xhr, status, error) {
                if (status === 'abort' || requestId !== currentRequestId) return;
                $('#dashboard_loading').hide();
                $('#dashboard_empty_state').show();
                console.error('Error loading recovery data:', error);
            }
        });
    }

    // Handle pagination click
    function handlePaginationClick(page) {
        // Show loading state
        showDashboardLoading();

        // Clear current table to avoid multiple tables stacking
        $('#dashboard_table_container').empty().hide();
        $('#recovery_actions_container').hide();
        $('#dashboard_empty_state').hide();

        // Call the appropriate function based on current action
        switch (currentAction) {
            case 'get_total_bookings':
                loadTotalBookings(page, currentLimit);
                break;
            case 'get_appointment_history':
                loadAppointmentHistory(page, currentLimit);
                break;
            case 'get_recovery_data':
                loadRecoveryData(page, currentLimit);
                break;
            default:
                console.error("Unknown action for pagination: " + currentAction);
        }
    }

    // Helper function to get status class
    function getStatusClass(status) {
        switch (status) {
            case 'Confirmed':
                return 'bg-success';
            case 'Pending':
                return 'bg-warning text-dark';
            case 'Cancelled':
            case 'Rejected':
                return 'bg-danger';
            case 'Completed':
                return 'bg-primary';
            default:
                return 'bg-secondary';
        }
    }

    // Helper function to get potential class
    function getPotentialClass(potential) {
        switch (potential) {
            case 'High':
                return 'bg-success';
            case 'Medium':
                return 'bg-warning text-dark';
            case 'Low':
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }

    // Generate pagination HTML
    function generatePagination(pagination) {
        if (!pagination || pagination.total <= pagination.per_page) {
            return '';
        }

        let paginationHtml = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';

        // Previous button
        if (pagination.current_page > 1) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="javascript:void(0)" onclick="handlePaginationClick(${pagination.current_page - 1})" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            `;
        } else {
            paginationHtml += `
                <li class="page-item disabled">
                    <a class="page-link" href="javascript:void(0)" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            `;
        }

        // Page numbers
        const startPage = Math.max(1, pagination.current_page - 2);
        const endPage = Math.min(pagination.last_page, pagination.current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            if (i === pagination.current_page) {
                paginationHtml += `<li class="page-item active"><a class="page-link" href="javascript:void(0)">${i}</a></li>`;
            } else {
                paginationHtml += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="handlePaginationClick(${i})">${i}</a></li>`;
            }
        }

        // Next button
        if (pagination.current_page < pagination.last_page) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="javascript:void(0)" onclick="handlePaginationClick(${pagination.current_page + 1})" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            `;
        } else {
            paginationHtml += `
                <li class="page-item disabled">
                    <a class="page-link" href="javascript:void(0)" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            `;
        }

        paginationHtml += '</ul></nav>';

        return paginationHtml;
    }

    // Display bookings table
    function displayBookingsTable(bookings, pagination) {
        console.log('Displaying bookings table with data:', bookings);

        const headers = `
            <tr>
                <th>Client</th>
                <th>Services</th>
                <th>Date & Time</th>
                <th>Status</th>
                <th>Amount</th>
                <th>Payment</th>
            </tr>
        `;

        let rows = '';
        bookings.forEach(booking => {
            const statusClass = getStatusClass(booking.booking_status);
            const paymentClass = booking.payment_status === 'Paid' ? 'text-success' : 'text-warning';

            rows += `
                <tr>
                    <td data-label="Client">
                        <strong>${booking.user_name}</strong>
                    </td>
                    <td data-label="Services">${booking.services_text}</td>
                    <td data-label="Date & Time">${booking.booking_date}</td>
                    <td data-label="Status">
                        <span class="badge ${statusClass}">${booking.booking_status}</span>
                    </td>
                    <td data-label="Amount">₱${parseFloat(booking.total_price).toFixed(2)}</td>
                    <td data-label="Payment">
                        <span class="${paymentClass}">${booking.payment_status}</span>
                    </td>
                </tr>
            `;
        });

        // Add pagination
        const paginationHtml = generatePagination(pagination);
        $('#dashboard_table_container').html(`
            <div class="table-responsive">
                <table id="dashboard_data_table" class="table table-striped w-100">
                    <thead class="table-secondary">${headers}</thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>
            ${paginationHtml}
        `);

        $('#dashboard_table_container').show();
    }

    // Display history table
    function displayHistoryTable(history, pagination) {
        const headers = `
            <tr>
                <th>Client</th>
                <th>Services</th>
                <th>Completion Date</th>
                <th>Status</th>
                <th>Amount</th>
                <th>Duration</th>
            </tr>
        `;

        let rows = '';
        history.forEach(appointment => {
            const statusClass = getStatusClass(appointment.booking_status);

            rows += `
                <tr>
                    <td data-label="Client">
                        <strong>${appointment.user_name}</strong>
                    </td>
                    <td data-label="Services">${appointment.services_text}</td>
                    <td data-label="Completion Date">${appointment.completion_date}</td>
                    <td data-label="Status">
                        <span class="badge ${statusClass}">${appointment.booking_status}</span>
                    </td>
                    <td data-label="Amount">₱${parseFloat(appointment.total_price).toFixed(2)}</td>
                    <td data-label="Duration">${appointment.duration}</td>
                </tr>
            `;
        });

        // Add pagination
        const paginationHtml = generatePagination(pagination);
        $('#dashboard_table_container').html(`
            <div class="table-responsive">
                <table id="dashboard_data_table" class="table table-striped w-100">
                    <thead class="table-secondary">${headers}</thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>
            ${paginationHtml}
        `);

        $('#dashboard_table_container').show();
    }

    // Display recovery data
    function displayRecoveryData(data, pagination) {
        // Show recovery actions container
        $('#recovery_actions_container').show();

        // Display recoverable bookings
        let recoverableHtml = '';
        if (data.recoverable && data.recoverable.length > 0) {
            data.recoverable.forEach(booking => {
                const potentialClass = getPotentialClass(booking.recovery_potential);
                recoverableHtml += `
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${booking.user_name}</h6>
                                <p class="mb-1 text-muted">${booking.services_text}</p>
                                <small class="text-muted">Cancelled: ${booking.cancelled_date}</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">₱${parseFloat(booking.total_price).toFixed(2)}</div>
                                <span class="badge ${potentialClass}">${booking.recovery_potential}</span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button class="btn btn-success btn-sm" onclick="recoverBooking(${booking.bookingid})">
                                <i class="bi bi-arrow-repeat me-1"></i>Recover
                            </button>
                        </div>
                    </div>
                `;
            });

            // Add pagination for recoverable bookings
            if (pagination && pagination.recoverable) {
                const recoverablePagination = generatePagination(pagination.recoverable);
                recoverableHtml += recoverablePagination;
            }
        } else {
            recoverableHtml = '<p class="text-muted">No recoverable bookings found.</p>';
        }

        $('#recoverable_bookings').html(recoverableHtml);

        // Display recently recovered bookings
        let recoveredHtml = '';
        if (data.recently_recovered && data.recently_recovered.length > 0) {
            data.recently_recovered.forEach(booking => {
                recoveredHtml += `
                    <div class="border rounded p-3 mb-3 bg-light">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${booking.user_name}</h6>
                                <p class="mb-1 text-muted">${booking.services_text}</p>
                                <small class="text-success">Recovered: ${booking.recovery_date}</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success">₱${parseFloat(booking.total_price).toFixed(2)}</div>
                                <small class="text-muted">Value Recovered</small>
                            </div>
                        </div>
                    </div>
                `;
            });

            // Add pagination for recovered bookings
            if (pagination && pagination.recovered) {
                const recoveredPagination = generatePagination(pagination.recovered);
                recoveredHtml += recoveredPagination;
            }
        } else {
            recoveredHtml = '<p class="text-muted">No recently recovered bookings.</p>';
        }

        $('#recovered_bookings').html(recoveredHtml);
    }

    // Load dashboard stats on page load
    function loadDashboardStats() {
        console.log('Loading dashboard stats...');

        // Show loading indicators
        $('#total_bookings_count').html('<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div>');
        $('#history_count').html('<div class="spinner-border spinner-border-sm text-success" role="status"><span class="visually-hidden">Loading...</span></div>');
        $('#recovery_count').html('<div class="spinner-border spinner-border-sm text-warning" role="status"><span class="visually-hidden">Loading...</span></div>');

        $.ajax({
            url: '../../controller/admin_dashboard_contr.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_dashboard_stats'
            },
            success: function(response) {
                console.log('Dashboard stats response:', response);
                if (response && response.status === 'success' && response.data) {
                    updateDashboardStats(response.data);
                } else {
                    console.error('Error loading dashboard stats:', response ? response.message : 'No response');
                    showStatsError();
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error loading dashboard stats:', error);
                console.error('Status:', status);
                console.error('Response text:', xhr.responseText);

                // Try to parse the response if it's JSON
                try {
                    if (xhr.responseText) {
                        const errorResponse = JSON.parse(xhr.responseText);
                        console.error('Parsed error response:', errorResponse);
                    }
                } catch (e) {
                    console.error('Could not parse error response as JSON');
                }

                showStatsError();
            }
        });
    }

    // Update dashboard statistics cards
    function updateDashboardStats(data) {
        console.log("Updating dashboard stats with data:", data);

        // Total Bookings Card
        // Convert to number to ensure proper display
        const totalBookings = parseInt(data.total_bookings) || 0;
        $('#total_bookings_count').html(totalBookings);

        const acceptedBookings = parseInt(data.accepted_bookings) || 0;
        const pendingBookings = parseInt(data.pending_bookings) || 0;

        $('#bookings_subtitle').html(`
            <i class="pe-2 bi bi-check-circle text-success"></i>
            Accepted: ${acceptedBookings} | Pending: ${pendingBookings}
        `);

        // History Card
        const completedBookings = parseInt(data.completed_bookings) || 0;
        const cancelledBookings = parseInt(data.cancelled_bookings) || 0;
        const historyTotal = completedBookings + cancelledBookings;

        $('#history_count').html(historyTotal);
        $('#history_subtitle').html(`
            <i class="pe-2 bi bi-check-circle text-success"></i>
            Completed: ${completedBookings} | Cancelled: ${cancelledBookings}
        `);

        // Recovery Card
        const recoveryRate = parseFloat(data.recovery_rate) || 0;
        const recoveryCount = parseInt(data.recovery_count) || 0;

        $('#recovery_count').html(recoveryRate + '%');
        $('#recovery_subtitle').html(`
            <i class="pe-2 bi bi-arrow-repeat text-warning"></i>
            Recovery Rate: ${recoveryCount} recovered
        `);
    }

    // Show stats loading error
    function showStatsError() {
        $('#total_bookings_count').html('<i class="bi bi-exclamation-circle text-danger"></i>');
        $('#bookings_subtitle').html('<i class="pe-2 bi bi-exclamation-triangle text-danger"></i>Error loading');

        $('#history_count').html('<i class="bi bi-exclamation-circle text-danger"></i>');
        $('#history_subtitle').html('<i class="pe-2 bi bi-exclamation-triangle text-danger"></i>Error loading');

        $('#recovery_count').html('<i class="bi bi-exclamation-circle text-danger"></i>');
        $('#recovery_subtitle').html('<i class="pe-2 bi bi-exclamation-triangle text-danger"></i>Error loading');
    }

    // Function to recover a cancelled booking
    function recoverBooking(bookingId) {
        if (confirm('Are you sure you want to recover this booking?')) {
            $.ajax({
                url: '../../controller/admin_dashboard_contr.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'update_booking_status',
                    booking_id: bookingId,
                    status: 'Confirmed'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Reload recovery data
                        loadRecoveryData(currentPage, currentLimit);
                        // Show success message
                        alert('Booking successfully recovered!');
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error recovering booking:', error);
                    alert('Error recovering booking. Please try again.');
                }
            });
        }
    }

    // Dashboard data loading function
    function loadDashboardData(type) {
        // Update active card
        $('.custom_card_hover').removeClass('active');

        // Show loading state
        showDashboardLoading();

        // Reset to first page when changing dashboard type
        currentPage = 1;

        switch (type) {
            case 'Bookings':
                $('#active_dashboard').text('Total Bookings');
                $('#bookings-card').addClass('active');
                currentAction = 'get_total_bookings';
                loadTotalBookings(currentPage, currentLimit);
                break;
            case 'History':
                $('#active_dashboard').text('Appointment History');
                $('#history-card').addClass('active');
                currentAction = 'get_appointment_history';
                loadAppointmentHistory(currentPage, currentLimit);
                break;
            case 'Recovery':
                $('#active_dashboard').text('Recovery Management');
                $('#recovery-card').addClass('active');
                currentAction = 'get_recovery_data';
                loadRecoveryData(currentPage, currentLimit);
                break;
            default:
                console.error("Unknown type: " + type);
        }
    }



    // Dashboard data loading function
    function loadDashboardData(type) {
        // Update active card
        $('.custom_card_hover').removeClass('active');

        // Show loading state
        $('#dashboard_loading').show();
        $('#dashboard_table_container').hide();
        $('#recovery_actions_container').hide();
        $('#dashboard_empty_state').hide();

        // Reset to first page when changing dashboard type
        currentPage = 1;

        // Clear containers before loading new section
        $('#dashboard_table_container').empty().hide();
        $('#recovery_actions_container').hide();
        $('#dashboard_empty_state').hide();

        // Abort any previous in-flight request
        if (currentAjax && currentAjax.readyState !== 4) {
            try {
                currentAjax.abort();
            } catch (e) {}
        }

        switch (type) {
            case 'Bookings':
                $('#active_dashboard').text('Total Bookings');
                $('#bookings-card').addClass('active');
                currentAction = 'get_total_bookings';
                loadTotalBookings(currentPage, currentLimit);
                break;
            case 'History':
                $('#active_dashboard').text('Appointment History');
                $('#history-card').addClass('active');
                currentAction = 'get_appointment_history';
                loadAppointmentHistory(currentPage, currentLimit);
                break;
            case 'Recovery':
                $('#active_dashboard').text('Recovery Management');
                $('#recovery-card').addClass('active');
                currentAction = 'get_recovery_data';
                loadRecoveryData(currentPage, currentLimit);
                break;
            default:
                console.error("Unknown type: " + type);
        }
    }


    // Display bookings table
    function displayBookingsTable(bookings, pagination) {
        console.log('Displaying bookings table with data:', bookings);

        const headers = `
                <tr>
                    <th>Client</th>
                    <th>Services</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th>Payment</th>
                </tr>
            `;

        let rows = '';
        bookings.forEach(booking => {
            const statusClass = getStatusClass(booking.booking_status);
            const paymentClass = booking.payment_status === 'Paid' ? 'text-success' : 'text-warning';

            rows += `
                    <tr>
                        <td data-label="Client">
                            <strong>${booking.user_name}</strong><br>
                            <small class="text-muted">${booking.user_email}</small>
                        </td>
                        <td data-label="Services">${booking.services_text}</td>
                        <td data-label="Date & Time">${booking.booking_date}</td>
                        <td data-label="Status">
                            <span class="badge ${statusClass}">${booking.booking_status}</span>
                        </td>
                        <td data-label="Amount">₱${parseFloat(booking.total_price).toFixed(2)}</td>
                        <td data-label="Payment">
                            <span class="${paymentClass}">${booking.payment_status}</span>
                        </td>
                    </tr>
                `;
        });

        // Add pagination
        const paginationHtml = generatePagination(pagination);
        $('#dashboard_table_container').html(`
                <div class="table-responsive">
                    <table id="dashboard_data_table" class="table table-striped w-100">
                        <thead class="table-secondary">${headers}</thead>
                        <tbody>${rows}</tbody>
                    </table>
                </div>
                ${paginationHtml}
            `);

        $('#dashboard_table_container').show();
    }

    // Display history table
    function displayHistoryTable(history, pagination) {
        const headers = `
                <tr>
                    <th>Client</th>
                    <th>Services</th>
                    <th>Completion Date</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th>Duration</th>
                </tr>
            `;

        let rows = '';
        history.forEach(appointment => {
            const statusClass = getStatusClass(appointment.booking_status);

            rows += `
                    <tr>
                        <td data-label="Client">
                            <strong>${appointment.user_name}</strong><br>
                            <small class="text-muted">${appointment.user_email}</small>
                        </td>
                        <td data-label="Services">${appointment.services_text}</td>
                        <td data-label="Completion Date">${appointment.completion_date}</td>
                        <td data-label="Status">
                            <span class="badge ${statusClass}">${appointment.booking_status}</span>
                        </td>
                        <td data-label="Amount">₱${parseFloat(appointment.total_price).toFixed(2)}</td>
                        <td data-label="Duration">${appointment.duration}</td>
                    </tr>
                `;
        });

        // Add pagination
        const paginationHtml = generatePagination(pagination);
        $('#dashboard_table_container').html(`
                <div class="table-responsive">
                    <table id="dashboard_data_table" class="table table-striped w-100">
                        <thead class="table-secondary">${headers}</thead>
                        <tbody>${rows}</tbody>
                    </table>
                </div>
                ${paginationHtml}
            `);

        $('#dashboard_table_container').show();
    }

    // Display recovery data
    function displayRecoveryData(data, pagination) {
        // Show recovery actions container
        $('#recovery_actions_container').show();

        // Display recoverable bookings
        let recoverableHtml = '';
        if (data.recoverable && data.recoverable.length > 0) {
            data.recoverable.forEach(booking => {
                const potentialClass = getPotentialClass(booking.recovery_potential);
                recoverableHtml += `
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">${booking.user_name}</h6>
                                    <p class="mb-1 text-muted">${booking.services_text}</p>
                                    <small class="text-muted">Cancelled: ${booking.cancelled_date}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold">₱${parseFloat(booking.total_price).toFixed(2)}</div>
                                    <span class="badge ${potentialClass}">${booking.recovery_potential}</span>
                                </div>
                            </div>
                            <div class="mt-2">
                                <button class="btn btn-success btn-sm" onclick="recoverBooking(${booking.bookingid})">
                                    <i class="bi bi-arrow-repeat me-1"></i>Recover
                                </button>
                            </div>
                        </div>
                    `;
            });

            // Add pagination for recoverable bookings
            if (pagination && pagination.recoverable) {
                const recoverablePagination = generatePagination(pagination.recoverable);
                recoverableHtml += recoverablePagination;
            }
        } else {
            recoverableHtml = '<p class="text-muted">No recoverable bookings found.</p>';
        }

        $('#recoverable_bookings').html(recoverableHtml);

        // Display recently recovered bookings
        let recoveredHtml = '';
        if (data.recently_recovered && data.recently_recovered.length > 0) {
            data.recently_recovered.forEach(booking => {
                recoveredHtml += `
                        <div class="border rounded p-3 mb-3 bg-light">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">${booking.user_name}</h6>
                                    <p class="mb-1 text-muted">${booking.services_text}</p>
                                    <small class="text-success">Recovered: ${booking.recovery_date}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-success">₱${parseFloat(booking.total_price).toFixed(2)}</div>
                                    <small class="text-muted">Value Recovered</small>
                                </div>
                            </div>
                        </div>
                    `;
            });

            // Add pagination for recovered bookings
            if (pagination && pagination.recovered) {
                const recoveredPagination = generatePagination(pagination.recovered);
                recoveredHtml += recoveredPagination;
            }
        } else {
            recoveredHtml = '<p class="text-muted">No recently recovered bookings.</p>';
        }

        $('#recovered_bookings').html(recoveredHtml);
    }

    // Function to recover a cancelled booking
    function recoverBooking(bookingId) {
        if (confirm('Are you sure you want to recover this booking?')) {
            $.ajax({
                url: '../../controller/admin_dashboard_contr.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'update_booking_status',
                    booking_id: bookingId,
                    status: 'Confirmed'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Reload recovery data
                        loadRecoveryData(currentPage, currentLimit);
                        // Show success message
                        alert('Booking successfully recovered!');
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error recovering booking:', error);
                    alert('Error recovering booking. Please try again.');
                }
            });
        }
    }

    // Helper function to get status class
    function getStatusClass(status) {
        switch (status) {
            case 'Confirmed':
                return 'bg-success';
            case 'Pending':
                return 'bg-warning text-dark';
            case 'Cancelled':
            case 'Rejected':
                return 'bg-danger';
            case 'Completed':
                return 'bg-primary';
            default:
                return 'bg-secondary';
        }
    }

    // Helper function to get recovery potential class
    function getPotentialClass(potential) {
        switch (potential) {
            case 'High':
                return 'bg-success';
            case 'Medium':
                return 'bg-warning';
            case 'Low':
                return 'bg-secondary';
            default:
                return 'bg-secondary';
        }
    }

    // Recover booking function
    window.recoverBooking = function(bookingId) {
        if (confirm('Are you sure you want to recover this booking?')) {
            $.ajax({
                url: '../../controller/admin_dashboard_contr.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'update_booking_status',
                    booking_id: bookingId,
                    new_status: 'Confirmed'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        alert('Booking recovered successfully!');
                        loadRecoveryData(); // Reload recovery data
                        loadDashboardStats(); // Reload stats
                    } else {
                        alert('Error recovering booking: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error recovering booking: ' + error);
                }
            });
        }
    };

    // Load dashboard stats on page load
    loadDashboardStats();

    // Load default dashboard data (bookings)
    loadDashboardData('Bookings');

    // Function to open the therapist status modal
    window.openTherapistStatusModal = function() {
        showGlobalModal('../../views/modal/admin_modal-therapist-status.php', {}, function() {
            console.log('✅ Therapist Status Modal loaded successfully');
        });
    };
</script>