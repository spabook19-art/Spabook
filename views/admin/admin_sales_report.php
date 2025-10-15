<?php include_once '../../include/header.php'; ?>

<div class="content_wrapper">
  <div class="app_sidebar_container d-flex">
    <div class="app_sidebar_nav app_sidebar_bg_it_asset d-flex flex-column justify-content-between sidebar-hidden" id="app_sidebar_nav"></div>
    <div class="app_content_container">
      <nav class="navbar navbar-expand px-3 border-bottom" style="background-color: #C0967E;">
        <button class="btn app_open_sidebar_btn" type="button">
          <span class="navbar-toggler-icon"></span>
        </button>
        <span class="app_content_title fs-25 fw-bold pe-2">Sales & Commission Report</span>
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
        </div>
      </nav>
      <div class="app_content_body">

        <div class="container-fluid py-3">

          <!-- Nav Tabs -->
          <ul class="nav nav-tabs mb-3" id="reportTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button" role="tab">
                <i class="fas fa-money-bill-wave me-2"></i>Sales Report
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="commission-tab" data-bs-toggle="tab" data-bs-target="#commission" type="button" role="tab">
                <i class="fas fa-percent me-2"></i>Commission Report
              </button>
            </li>
          </ul>

          <!-- Tab Content -->
          <div class="tab-content" id="reportTabContent">
            
            <!-- SALES TAB -->
            <div class="tab-pane fade show active" id="sales" role="tabpanel">
              <div class="card">
                <div class="card-body">
                  <!-- Sales Summary Cards -->
                  <div class="row g-3 mb-4">
                    <div class="col-md-4">
                      <div class="card bg-success text-white">
                        <div class="card-body text-center">
                          <div class="text-white-50 small">Total Sales</div>
                          <div id="total-sales" class="h3 mb-0">₱0.00</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="card bg-info text-white">
                        <div class="card-body text-center">
                          <div class="text-white-50 small">Total Bookings</div>
                          <div id="total-bookings" class="h3 mb-0">0</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                          <div class="text-white-50 small">Net Revenue</div>
                          <div id="net-revenue" class="h3 mb-0">₱0.00</div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Note:</strong> Sales data is sourced directly from completed booking details. Each row represents a completed service in a booking with quantity, price, and calculated total.
                  </div>

                  <!-- Sales Table -->
                  <div class="table-responsive">
                    <table id="salesTable" class="table table-striped table-hover">
                      <thead>
                        <tr>
                          <th>Booking ID</th>
                          <th>Service</th>
                          <th>Customer</th>
                          <th>Date</th>
                          <th>Quantity</th>
                          <th>Price</th>
                          <th>Total Amount</th>
                          <th>Status</th>
                          <th>Payment</th>
                        </tr>
                      </thead>
                      <tbody id="salesTableBody">
                        <!-- Data will be loaded here -->
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <!-- COMMISSION TAB -->
            <div class="tab-pane fade" id="commission" role="tabpanel">
              <div class="card">
                <div class="card-body">
                  <!-- Commission Summary Cards -->
                  <div class="row g-3 mb-4">
                    <div class="col-md-4">
                      <div class="card bg-warning text-dark">
                        <div class="card-body text-center">
                          <div class="text-dark-50 small">Total Commissions</div>
                          <div id="total-commission" class="h3 mb-0">₱0.00</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="card bg-secondary text-white">
                        <div class="card-body text-center">
                          <div class="text-white-50 small">Total Hours</div>
                          <div id="total-hours" class="h3 mb-0">0</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="card bg-dark text-white">
                        <div class="card-body text-center">
                          <div class="text-white-50 small">Rate per Hour</div>
                          <div class="h3 mb-0">₱50.00</div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Note:</strong> Commission is calculated at ₱50/hour based on completed services with assigned therapists. Hours are calculated from schedule_start to schedule_end timestamps in booking details.
                  </div>

                  <!-- Commission Table -->
                  <div class="table-responsive">
                    <table id="commissionTable" class="table table-striped table-hover">
                      <thead>
                        <tr>
                          <th>Therapist</th>
                          <th>Service</th>
                          <th>Date</th>
                          <th>Hours Logged</th>
                          <th>Commission Amount</th>
                        </tr>
                      </thead>
                      <tbody id="commissionTableBody">
                        <!-- Data will be loaded here -->
                      </tbody>
                    </table>
                  </div>
                </div>
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
  $(document).ready(function() {
    setTimeout(function() {
      $('#admin_sales_report').addClass('active');
    }, 500);

    function peso(v) {
      return '₱' + Number(v).toLocaleString('en-PH', {
        minimumFractionDigits: 2
      });
    }

    // Load Sales Data
    function loadSalesData() {
      $.post('../../controller/sales_report_contr.php', {
        action: 'get_sales_data'
      }, function(res) {
        if (res.status === 'success') {
          // Update summary cards
          $('#total-sales').text(peso(res.total_sales));
          $('#total-bookings').text(res.total_bookings);
          $('#net-revenue').text(peso(res.net_revenue));

          // Destroy existing DataTable if it exists
          if ($.fn.DataTable.isDataTable('#salesTable')) {
            $('#salesTable').DataTable().destroy();
          }

          // Populate sales table
          let salesHtml = '';
          if (res.sales && res.sales.length > 0) {
            res.sales.forEach(function(sale) {
              // Payment status badge
              let paymentBadge = '';
              if (sale.payment_status === true || sale.payment_status === 'Paid') {
                paymentBadge = '<span class="badge bg-success">Paid</span>';
              } else if (sale.payment_status === false || sale.payment_status === 'Unpaid') {
                paymentBadge = '<span class="badge bg-danger">Unpaid</span>';
              } else if (sale.payment_status === 'Down Payment') {
                paymentBadge = '<span class="badge bg-warning">Down Payment</span>';
              } else {
                paymentBadge = '<span class="badge bg-secondary">' + sale.payment_status + '</span>';
              }

              // Service status badge
              let statusBadge = '';
              if (sale.status === 'Completed') statusBadge = '<span class="badge bg-success">Completed</span>';
              else if (sale.status === 'Pending') statusBadge = '<span class="badge bg-warning">Pending</span>';
              else if (sale.status === 'Cancelled') statusBadge = '<span class="badge bg-danger">Cancelled</span>';
              else if (sale.status === 'Confirmed') statusBadge = '<span class="badge bg-info">Confirmed</span>';
              else statusBadge = '<span class="badge bg-secondary">' + sale.status + '</span>';

              salesHtml += `
                <tr>
                  <td>${sale.booking_id}</td>
                  <td>${sale.service_name}</td>
                  <td>${sale.customer_name}</td>
                  <td>${sale.date_created}</td>
                  <td>${sale.quantity}</td>
                  <td>${peso(sale.price)}</td>
                  <td>${peso(sale.total_amount)}</td>
                  <td>${statusBadge}</td>
                  <td>${paymentBadge}</td>
                </tr>
              `;
            });
          }
          $('#salesTableBody').html(salesHtml);

          // Initialize DataTable with proper configuration
          $('#salesTable').DataTable({
            order: [[3, 'desc']], // Sort by date descending
            pageLength: 25,
            language: {
              emptyTable: "No sales data available"
            }
          });
        } else {
          Swal.fire('Error', res.message || 'Failed to load sales data', 'error');
        }
      }, 'json').fail(function() {
        Swal.fire('Error', 'Network error loading sales data', 'error');
      });
    }

    // Load Commission Data
    function loadCommissionData() {
      $.post('../../controller/sales_report_contr.php', {
        action: 'get_commission_data'
      }, function(res) {
        if (res.status === 'success') {
          // Update summary cards
          $('#total-commission').text(peso(res.total_commission));
          $('#total-hours').text(res.total_hours);

          // Destroy existing DataTable if it exists
          if ($.fn.DataTable.isDataTable('#commissionTable')) {
            $('#commissionTable').DataTable().destroy();
          }

          // Populate commission table
          let commissionHtml = '';
          if (res.commissions && res.commissions.length > 0) {
            res.commissions.forEach(function(comm) {
              commissionHtml += `
                <tr>
                  <td>${comm.therapist_name}</td>
                  <td>${comm.service_name}</td>
                  <td>${comm.date}</td>
                  <td>${comm.hours_logged}</td>
                  <td>${peso(comm.commission_amount)}</td>
                </tr>
              `;
            });
          }
          $('#commissionTableBody').html(commissionHtml);

          // Initialize DataTable with proper configuration
          $('#commissionTable').DataTable({
            order: [[2, 'desc']], // Sort by date descending
            pageLength: 25,
            language: {
              emptyTable: "No commission data available"
            }
          });
        } else {
          Swal.fire('Error', res.message || 'Failed to load commission data', 'error');
        }
      }, 'json').fail(function() {
        Swal.fire('Error', 'Network error loading commission data', 'error');
      });
    }

    // Load sales data on page load
    loadSalesData();

    // Load commission data when tab is clicked
    $('#commission-tab').on('click', function() {
      loadCommissionData();
    });
  });
</script>