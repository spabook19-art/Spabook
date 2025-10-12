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

          <div class="card">
            <div class="card-body">
              <div class="d-flex gap-2 mb-4">
                <button class="btn btn-outline-primary active" data-period="daily">Daily</button>
                <button class="btn btn-outline-primary" data-period="weekly">Weekly</button>
                <button class="btn btn-outline-primary" data-period="monthly">Monthly</button>
              </div>

              <div id="summary" class="row g-3">
                <div class="col-xl-3 col-md-6">
                  <div class="card bg-light border-0">
                    <div class="card-body text-center">
                      <div class="text-muted small">Period Start</div>
                      <div id="sum-start" class="h5 mb-0">-</div>
                    </div>
                  </div>
                </div>
                <div class="col-xl-3 col-md-6">
                  <div class="card bg-success text-white">
                    <div class="card-body text-center">
                      <div class="text-white-50 small">Total Sales</div>
                      <div id="sum-sales" class="h4 mb-0">₱0.00</div>
                    </div>
                  </div>
                </div>
                <div class="col-xl-3 col-md-6">
                  <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                      <div class="text-dark-50 small">Commissions</div>
                      <div id="sum-commission" class="h4 mb-0">₱0.00</div>
                    </div>
                  </div>
                </div>
                <div class="col-xl-3 col-md-6">
                  <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                      <div class="text-white-50 small">Net Revenue</div>
                      <div id="sum-net" class="h4 mb-0">₱0.00</div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="mt-4">
                <div class="alert alert-info">
                  <i class="fas fa-info-circle me-2"></i>
                  <strong>Note:</strong> Commission is calculated at ₱50/hour based on actual logged hours from therapist time tracking.
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

    function loadSummary(period) {
      // Update button states
      $('[data-period]').removeClass('active');
      $('[data-period="' + period + '"]').addClass('active');

      $.post('../../controller/booking_contr.php', {
        action: 'get_sales_summary',
        period: period
      }, function(res) {
        if (res.status === 'success') {
          $('#sum-start').text(res.start);
          $('#sum-sales').text(peso(res.sales));
          $('#sum-commission').text(peso(res.commission));
          $('#sum-net').text(peso(res.net));
        } else {
          Swal.fire('Error', res.message || 'Failed to load sales data', 'error');
        }
      }, 'json').fail(function() {
        Swal.fire('Error', 'Network error loading sales data', 'error');
      });
    }

    $(document).on('click', '[data-period]', function() {
      loadSummary($(this).data('period'));
    });

    // Load daily data by default
    loadSummary('daily');
  });
</script>