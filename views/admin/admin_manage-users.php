<?php include_once '../../include/header.php'; ?>

<div class="content_wrapper">
  <div class="app_sidebar_container d-flex">
    <div class="app_sidebar_nav app_sidebar_bg_it_asset d-flex flex-column justify-content-between sidebar-hidden" id="app_sidebar_nav"></div>
    <div class="app_content_container">
      <nav class="navbar navbar-expand px-3 border-bottom" style="background-color: #C0967E;">
        <button class="btn app_open_sidebar_btn" type="button">
          <span class="navbar-toggler-icon"></span>
        </button>
        <span class="app_content_title fs-25 fw-bold pe-2">User Management</span>
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


        <div class="container">
          <!-- Tabs Navigation -->
          <ul class="nav nav-tabs nav-fill mb-2" id="userTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#regular-users" type="button" role="tab" aria-controls="regular-users" aria-selected="true">
                <i class="fas fa-users me-2"></i>Customers <span class="badge bg-primary ms-1" id="userCount">0</span>
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin-users" type="button" role="tab" aria-controls="admin-users" aria-selected="false">
                <i class="fas fa-user-shield me-2"></i>Administrators <span class="badge bg-danger ms-1" id="adminCount">0</span>
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="therapist-tab" data-bs-toggle="tab" data-bs-target="#therapist-users" type="button" role="tab" aria-controls="therapist-users" aria-selected="false">
                <i class="fas fa-spa me-2"></i>Therapists <span class="badge bg-success ms-1" id="therapistCount">0</span>
              </button>
            </li>
          </ul>

          <!-- Tab Content -->
          <div class="tab-content" id="userTabsContent">
            <!-- Regular Users Tab -->
            <div class="tab-pane fade show active" id="regular-users" role="tabpanel" aria-labelledby="users-tab">
              <div class="card">
                <div class="card-header">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h5 class="mb-0"><i class="fas fa-users me-2"></i>Customer Accounts</h5>
                    </div>
                    <div class="d-flex gap-2">
                      <button class="btn btn-outline-secondary btn-sm" onclick="loadManageUser('User', 'userTable')">
                        <i class="fas fa-sync-alt me-1"></i>Refresh
                      </button>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <!-- Desktop Table -->
                  <div class="table-responsive">
                    <table class="table table-hover mb-0" id="userTable">
                      <thead class="table-light">
                        <tr>
                          <th class="text-center">Avatar</th>
                          <th>Name</th>
                          <th>Email</th>
                          <th>Contact</th>
                          <th>Status</th>
                          <th>Join Date</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <!-- Admin Users Tab -->
            <div class="tab-pane fade" id="admin-users" role="tabpanel" aria-labelledby="admin-tab">
              <div class="card">
                <div class="card-header">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>Administrator Accounts</h5>
                      <small class="text-muted">Manage system administrators with full access</small>
                    </div>
                    <div class="d-flex gap-2">
                      <button class="btn btn-outline-secondary btn-sm" onclick="loadManageUser('Admin', 'adminTable')">
                        <i class="fas fa-sync-alt me-1"></i>Refresh
                      </button>
                      <button class="btn btn-danger btn-sm" onclick="addNewAdmin()">
                        <i class="fas fa-user-plus me-1"></i>Add Administrator
                      </button>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <!-- Desktop Table -->
                  <div class="table-responsive">
                    <table class="table table-hover mb-0" id="adminTable">
                      <thead class="table-light">
                        <tr>
                          <th class="text-center">Avatar</th>
                          <th>Name</th>
                          <th>Email</th>
                          <th>Contact</th>
                          <th>Status</th>
                          <th>Join Date</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <!-- Therapist Users Tab -->
            <div class="tab-pane fade" id="therapist-users" role="tabpanel" aria-labelledby="therapist-tab">
              <div class="card">
                <div class="card-header">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h5 class="mb-0"><i class="fas fa-spa me-2"></i>Therapist Accounts</h5>
                      <small class="text-muted">Manage therapists and their specializations</small>
                    </div>
                    <div class="d-flex gap-2">
                      <button class="btn btn-outline-secondary btn-sm" onclick="loadManageUser('Therapist', 'therapistTable')">
                        <i class="fas fa-sync-alt me-1"></i>Refresh
                      </button>
                      <button class="btn btn-success btn-sm" onclick="addNewTherapist()">
                        <i class="fas fa-user-plus me-1"></i>Add Therapist
                      </button>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <!-- Desktop Table -->
                  <div class="table-responsive">
                    <table class="table table-hover mb-0" id="therapistTable">
                      <thead class="table-light">
                        <tr>
                          <th class="text-center">Avatar</th>
                          <th>Name</th>
                          <th>Email</th>
                          <th>Contact</th>
                          <th>Status</th>
                          <th>Join Date</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
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
  setTimeout(function() {
    $('#admin_manage_users').addClass('active');
  }, 500);

  loadManageUser('User', 'userTable');
  loadCounts();

  $('#users-tab').on('click', function() {
    loadManageUser('User', 'userTable');
  });

  $('#admin-tab').on('click', function() {
    loadManageUser('Admin', 'adminTable');
  });

  $('#therapist-tab').on('click', function() {
    loadManageUser('Therapist', 'therapistTable');
  });


  function loadManageUser(role, tableId) {
    loadCounts();
    if ($.fn.DataTable.isDataTable('#' + tableId)) {
      $('#' + tableId).DataTable().clear().destroy();
    }
    let inTable = $('#' + tableId).DataTable({
      responsive: true,
      autoWidth: false,
      serverSide: false,
      deferRender: true,
      processing: true,
      ajax: {
        url: '../../controller/user_contr.php',
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'fetch_manage_users',
          role: role
        }
      },
      columns: [{ // Avatar
          data: 'profile_picture',
          render: function(data, type, row) {
            if (!data) {
              return `<img src="../../vendor/images/default_profile.png" class="rounded-circle" width="40" height="40">`;
            }
            return `<img src="${data}" class="rounded-circle" width="40" height="40">`;
          },
          className: 'dt-body-middle-center',
          orderable: false
        },
        {
          data: 'full_name',
          className: 'dt-body-middle-left'
        }, // Name
        {
          data: 'email',
          className: 'dt-body-middle-left'
        }, // Email
        { // Contact
          data: 'contact_number',
          render: function(data) {
            return data ? data : '<span class="text-muted">N/A</span>';
          },
          className: 'dt-body-middle-left'
        },
        { // Status
          data: 'is_active',
          render: function(data, type, row) {
            let userStatus = data ?
              `<span class="badge bg-success">Active</span>` :
              `<span class="badge bg-secondary">Inactive</span>`;
            return userStatus;
          },
          className: 'dt-body-middle-center'
        },
        { // Join Date
          data: 'created_at',
          render: function(data) {
            return new Date(data).toLocaleDateString();
          },
          className: 'dt-body-middle-center'
        },
        { // Actions
          data: null,
          render: function(data, type, row) {
            let buttonAction = ``;

            buttonAction += `<div class="btn-group btn-group-sm" role="group">
                         <button class="btn btn-outline-primary" onclick="editUser('${btoa(row.user_id)}')" title="Edit">
                             <i class="fas fa-edit"></i>
                         </button>
                         <button class="btn btn-outline-info" onclick="viewUser('${btoa(row.user_id)}')" title="View">
                             <i class="fas fa-eye"></i>
                         </button>
                         <button class="btn btn-outline-danger" onclick="deleteUser('${btoa(row.user_id)}')" title="Delete">
                             <i class="fas fa-trash"></i>
                         </button>
                     `;
            if (role === 'Admin') {
              buttonAction += ` <button class="btn btn-outline-warning" onclick="demoteAdmin('${btoa(row.user_id)}', '${row.full_name}')" title="Demote to Customer">
                            <i class="fas fa-arrow-down"></i>
                        </button>`;
            }
            if (role === 'Therapist') {
              buttonAction += ` <button class="btn btn-outline-success" onclick="manageTherapistSchedule('${btoa(row.user_id)}')" title="Manage Schedule">
                            <i class="fas fa-calendar-alt"></i>
                        </button>
                        <button class="btn btn-outline-warning" onclick="manageTherapistServices('${btoa(row.user_id)}')" title="Manage Services">
                            <i class="fas fa-spa"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="demoteTherapist('${btoa(row.user_id)}', '${row.full_name}')" title="Demote to Customer">
                            <i class="fas fa-arrow-down"></i>
                        </button>`;
            }
            buttonAction += `</div>`;
            return buttonAction;
          },
          className: 'dt-nowrap-center',
          orderable: false
        }
      ]
    });
    inTable.on('draw', function() {
      setTimeout(function() {
        $('[data-bs-toggle="tooltip"]').tooltip(); //* ======== Initialize tooltip ========
        $('[id^="tooltip"]').remove(); //* ======== Remove tooltip every table draw ========
        $('[data-bs-toggle="tooltip"]').on('click', function() { //* ======= Hide tooltip upon click =======
          $(this).tooltip('hide');
        });
      }, 1000);
    });
    setInterval(function() {
      inTable.ajax.reload(null, false); //* ======= Reload Table Data Every X seconds with pagination retained =======
    }, 30000);
  }


  function loadCounts() {
    $.ajax({
      url: '../../controller/user_contr.php',
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'fetch_user_counts'
      },
      success: function(response) {
        console.log('User counts response:', response);
        $('#userCount').text(response['User'] || 0);
        $('#adminCount').text(response['Admin'] || 0);
        $('#therapistCount').text(response['Therapist'] || 0);
      },
      error: function(xhr, status, error) {
        console.error('Error fetching user counts:', error);
      }
    });
  }

  function editUser(userId) {
    // const user = regularUsers.find(u => u.user_id === userId);
    showGlobalModal('../../views/modal/admin_modal-edit-user.php');
    $.ajax({
      url: '../../controller/user_contr.php',
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'get_user_details',
        user_id: atob(userId)
      },
      success: function(response) {
        $('#userId').val(response.user_id);
        $('#gender').val(response.gender || '');
        $('#fullName').val(response.full_name);
        $('#email').val(response.email);
        $('#address').val(response.address || '');
        $('#contactNumber').val(response.contact_number || '');
        $('#address').val(response.address || '');
        $('#role').val(response.role);
        $('#status').val(response.is_active ? '1' : '0');
        $('#isEmailVerified').attr('checked', response.is_email_verified ? true : false);
        $('#isNumberVerified').attr('checked', response.is_number_verified ? true : false);
        $('#createdAt').val(new Date(response.created_at).toLocaleString());
        $('#updatedAt').val(new Date(response.updated_at).toLocaleString());
        $('#agreedToTerms').prop('checked', response.agreed_to_terms ? true : false);
        $('#profileView').attr('src', response.profile_picture ? response.profile_picture : '../../vendor/images/default_profile.png');
      }
    });
  }

  function viewUser(userId) {
    showGlobalModal('../../views/modal/admin_modal-view-user.php');


    $.ajax({
      url: '../../controller/user_contr.php',
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'get_user_details',
        user_id: atob(userId)
      },
      success: function(result) {

        const avatar = result.profile_picture ?
          `<img src="${result.profile_picture}" class="rounded-circle mb-3" width="80" height="80">` :
          `<img src="../../vendor/images/default_profile.png" class="rounded-circle mb-3" width="80" height="80">`;

        const roleColor = {
          'Admin': 'danger',
          'Therapist': 'success',
          'User': 'primary'
        } [result.role] || 'secondary';

        let html = `
        <div class="text-center mb-4">
            ${avatar}
            <h4 class="mb-1">${result.full_name || 'N/A'}</h4>
            <span class="badge bg-${roleColor} mb-3">${result.role || 'User'}</span>
        </div>
        
        <div class="row g-3">
            <div class="col-sm-6">
                <label class="form-label text-muted">Email Address</label>
                <div class="fw-semibold">${result.email || 'N/A'}</div>
            </div>
            <div class="col-sm-6">
                <label class="form-label text-muted">Contact Number</label>
                <div class="fw-semibold">${result.contact_number || 'N/A'}</div>
            </div>
            <div class="col-12">
                <label class="form-label text-muted">Address</label>
                <div class="fw-semibold">${result.address || result.user_address || 'N/A'}</div>
            </div>
            <div class="col-sm-6">
                <label class="form-label text-muted">Member Since</label>
                <div class="fw-semibold">${result.created_at ? new Date(result.created_at).toLocaleDateString() : 'N/A'}</div>
            </div>
            <div class="col-sm-6">
                <label class="form-label text-muted">Email Verified</label>
                <div class="fw-semibold">
                    ${result.is_email_verified ? 
                        '<span class="badge bg-success">Verified</span>' : 
                        '<span class="badge bg-warning">Not Verified</span>'}
                </div>
            </div>
        </div>
    `;

        // Add therapist-specific information if applicable
        if (result.role === 'Therapist') {
          html += `
            <hr class="my-4">
            <h6 class="text-primary mb-3">
                <i class="fas fa-spa me-2"></i>Therapist Information
            </h6>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label text-muted">Services</label>
                    <div class="fw-semibold">Massage Therapy, Spa Treatments</div>
                </div>
                <div class="col-sm-6">
                    <label class="form-label text-muted">Status</label>
                    <div class="fw-semibold">
                        <span class="badge bg-success">Active</span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <label class="form-label text-muted">Experience</label>
                    <div class="fw-semibold">5+ years</div>
                </div>
            </div>
        `;
        }

        $('#userDetailsContent').html(html);
      },
      error: function() {
        $('#userDetailsContent').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Failed to load user details. Please try again.
                </div>
            `);
      }
    });
  }


  function manageTherapistServices(userId) {
    showGlobalModal('../../views/modal/admin_modal-therapist-services.php');
    $.ajax({
      url: '../../controller/user_contr.php',
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'get_therapist_services',
        therapist_id: atob(userId)
      },
      success: function(response) {
        // Fill therapist info
        $('#therapistName').text(response.therapist.full_name);
        $('#therapistEmail').text(response.therapist.email);

        // Fill services
        let services = response.services || [];
        let list = $('#therapistServicesList');
        list.empty();

        if (services.length > 0) {
          $('#noServicesMsg').addClass('d-none');
          services.forEach(service => {
            list.append(`
            <div class="list-group-item">
              <div class="d-flex w-100 justify-content-between">
                <h6 class="mb-1">${service.name}</h6>
                <small class="text-muted">₱${service.price}</small>
              </div>
              <p class="mb-1 text-muted">${service.description || '-'}</p>
              <small class="text-muted">${service.duration} mins</small>
            </div>
          `);
          });
        } else {
          $('#noServicesMsg').removeClass('d-none');
        }

        // Show modal
        let modal = new bootstrap.Modal(document.getElementById('therapistServicesModal'));
        modal.show();
      }
    });
  }

  // // let allUsers = [];
  // // let regularUsers = [];
  // // let adminUsers = [];
  // // let therapistUsers = [];

  // // Pagination state
  // const pageSize = 10;
  // let userPage = 1;
  // let adminPage = 1;
  // let therapistPage = 1;

  // $(document).ready(function() {
  //   // Load data when page loads
  //   loadAllUsers();

  //   // Tab change events
  //   $('#users-tab').on('click', function() {
  //     if (regularUsers.length === 0) loadRegularUsers();
  //   });

  //   $('#admin-tab').on('click', function() {
  //     if (adminUsers.length === 0) loadAdminUsers();
  //   });

  //   $('#therapist-tab').on('click', function() {
  //     if (therapistUsers.length === 0) loadTherapistUsers();
  //   });
  // });

  // // Load all users and therapists from their respective sources
  // function loadAllUsers() {
  //   showLoading('users');

  //   // Fetch users from users table
  //   $.ajax({
  //     url: '../../controller/user_contr.php',
  //     type: 'POST',
  //     dataType: 'json',
  //     data: {
  //       action: 'fetch_unified_users'
  //     },
  //     success: function(userResult) {
  //       allUsers = userResult.all_users || [];
  //       regularUsers = userResult.regular_user || [];
  //       adminUsers = userResult.admin || [];
  //       therapistUsers = userResult.therapist || [];

  //       updateCounts();

  //       renderRegularUsers();
  //       renderAdminUsers();
  //       renderTherapistUsers();

  //     },
  //     error: function(xhr, status, error) {
  //       hideLoading('users');
  //       console.error('Error loading users:', error);
  //       Swal.fire('Error', 'Failed to load users', 'error');
  //     }
  //   });
  // }

  // function loadRegularUsers() {
  //   renderRegularUsers();
  // }

  // function loadAdminUsers() {
  //   renderAdminUsers();
  // }

  // function loadTherapistUsers() {
  //   renderTherapistUsers();
  // }

  // function renderRegularUsers() {
  //   const tbody = $('#userTableBody');
  //   const mobileCards = $('#userMobileCards');

  //   tbody.empty();
  //   mobileCards.empty();

  //   if (regularUsers.length === 0) {
  //     tbody.append(`
  //           <tr>
  //               <td colspan="7" class="text-center py-4">
  //                   <div class="text-muted">
  //                       <i class="fas fa-users fa-3x mb-3"></i>
  //                       <h5>No Customers Found</h5>
  //                       <p>Customers sign up through the registration page.</p>
  //                   </div>
  //               </td>
  //           </tr>
  //       `);
  //     $('#userPagination').empty();
  //     return;
  //   }

  //   // Pagination slice
  //   const start = (userPage - 1) * pageSize;
  //   const paged = regularUsers.slice(start, start + pageSize);

  //   paged.forEach(user => {
  //     const avatar = user.profile_picture ?
  //       `<img src="${user.profile_picture}" class="rounded-circle" width="35" height="35">` :
  //       `<div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width:35px;height:35px;"><i class="fas fa-user text-white"></i></div>`;

  //     const statusBadge = user.is_active !== false ?
  //       '<span class="badge bg-success">Active</span>' :
  //       '<span class="badge bg-danger">Inactive</span>';

  //     const joinDate = user.created_at ? new Date(user.created_at).toLocaleDateString() : 'N/A';
  //     const contact = user.contact_number || user.phone_number || 'Not provided';

  //     // Desktop table row
  //     tbody.append(`
  //           <tr>
  //               <td>${avatar}</td>
  //               <td>
  //                   <div class="fw-semibold">${user.full_name}</div>
  //                   <small class="text-muted">Customer</small>
  //               </td>
  //               <td><span class="text-muted">${user.email}</span></td>
  //               <td><small class="text-muted">${contact}</small></td>
  //               <td>${statusBadge}</td>
  //               <td><small class="text-muted">${joinDate}</small></td>
  //               <td>
  //                   <div class="btn-group btn-group-sm" role="group">
  //                       <button class="btn btn-outline-primary" onclick="editUser('${user.user_id}','${user.role}')" title="Edit">
  //                           <i class="fas fa-edit"></i>
  //                       </button>
  //                       <button class="btn btn-outline-info" onclick="viewUser('${user.user_id}')" title="View">
  //                           <i class="fas fa-eye"></i>
  //                       </button>
  //                       <button class="btn btn-outline-danger" onclick="deleteUser('${user.user_id}', 'User')" title="Delete">
  //                           <i class="fas fa-trash"></i>
  //                       </button>
  //                   </div>
  //               </td>
  //           </tr>
  //       `);

  //     // Mobile card
  //     mobileCards.append(`
  //           <div class="card shadow-sm mb-3">
  //               <div class="card-body">
  //                   <div class="d-flex align-items-center mb-3">
  //                       ${avatar}
  //                       <div class="ms-3">
  //                           <div class="fw-semibold">${user.full_name}</div>
  //                           <div class="text-muted small">${user.email}</div>
  //                           <div class="text-muted small">${contact}</div>
  //                           ${statusBadge}
  //                       </div>
  //                   </div>
  //                   <div class="d-flex gap-2">
  //                       <button class="btn btn-sm btn-outline-primary" onclick="editUser('${user.user_id}')">
  //                           <i class="fas fa-edit me-1"></i>Edit
  //                       </button>
  //                       <button class="btn btn-sm btn-outline-info" onclick="viewUser('${user.user_id}')">
  //                           <i class="fas fa-eye me-1"></i>View
  //                       </button>
  //                       <button class="btn btn-sm btn-outline-danger" onclick="deleteUser('${user.user_id}', 'User')">
  //                           <i class="fas fa-trash me-1"></i>Delete
  //                       </button>
  //                   </div>
  //               </div>
  //           </div>
  //       `);
  //   });
  //   // Build pagination
  //   // buildPagination('#userPagination', userPage, Math.ceil(regularUsers.length / pageSize), (p) => {
  //   //   userPage = p;
  //   //   renderRegularUsers();
  //   // });
  // }

  // function renderAdminUsers() {
  //   const tbody = $('#adminTableBody');
  //   const mobileCards = $('#adminMobileCards');

  //   tbody.empty();
  //   mobileCards.empty();

  //   if (adminUsers.length === 0) {
  //     tbody.append(`
  //           <tr>
  //               <td colspan="6" class="text-center py-4">
  //                   <div class="text-muted">
  //                       <i class="fas fa-user-shield fa-3x mb-3"></i>
  //                       <h5>No Administrators Found</h5>
  //                       <p>Click "Add Admin" to create administrator accounts.</p>
  //                       <small class="text-warning">Debug: Check browser console for data loading info</small>
  //                   </div>
  //               </td>
  //           </tr>
  //       `);
  //     $('#adminPagination').empty();
  //     return;
  //   }

  //   const start = (adminPage - 1) * pageSize;
  //   const paged = adminUsers.slice(start, start + pageSize);

  //   paged.forEach(user => {
  //     const avatar = user.profile_picture ?
  //       `<img src="${user.profile_picture}" class="rounded-circle" width="35" height="35">` :
  //       `<div class="rounded-circle bg-danger d-flex align-items-center justify-content-center" style="width:35px;height:35px;"><i class="fas fa-user-shield text-white"></i></div>`;

  //     const statusBadge = user.is_active !== false ?
  //       '<span class="badge bg-success">Active</span>' :
  //       '<span class="badge bg-danger">Inactive</span>';

  //     const joinDate = user.created_at ? new Date(user.created_at).toLocaleDateString() : 'N/A';

  //     // Desktop table row
  //     tbody.append(`
  //           <tr>
  //               <td>${avatar}</td>
  //               <td>
  //                   <div class="fw-semibold">${user.full_name}</div>
  //                   <small class="text-muted">${user.email}</small>
  //               </td>
  //               <td><span class="text-muted">${user.email}</span></td>
  //               <td>${statusBadge}</td>
  //               <td><small class="text-muted">${joinDate}</small></td>
  //               <td>
  //                   <div class="btn-group btn-group-sm" role="group">
  //                       <button class="btn btn-outline-primary" onclick="editAdmin('${user.user_id}')" title="Edit">
  //                           <i class="fas fa-edit"></i>
  //                       </button>
  //                       <button class="btn btn-outline-info" onclick="viewAdmin('${user.user_id}')" title="View">
  //                           <i class="fas fa-eye"></i>
  //                       </button>
  //                       <button class="btn btn-outline-danger" onclick="deleteUser('${user.user_id}', 'Admin')" title="Delete">
  //                           <i class="fas fa-trash"></i>
  //                       </button>
  //                   </div>
  //               </td>
  //           </tr>
  //       `);

  //     // Mobile card
  //     mobileCards.append(`
  //           <div class="card shadow-sm mb-3">
  //               <div class="card-body">
  //                   <div class="d-flex align-items-center mb-3">
  //                       ${avatar}
  //                       <div class="ms-3">
  //                           <div class="fw-semibold">${user.full_name}</div>
  //                           <div class="text-muted small">${user.email}</div>
  //                           ${statusBadge}
  //                       </div>
  //                   </div>
  //                   <div class="d-flex gap-2">
  //                       <button class="btn btn-sm btn-outline-primary" onclick="editAdmin('${user.user_id}')">
  //                           <i class="fas fa-edit me-1"></i>Edit
  //                       </button>
  //                       <button class="btn btn-sm btn-outline-info" onclick="viewAdmin('${user.user_id}')">
  //                           <i class="fas fa-eye me-1"></i>View
  //                       </button>
  //                       <button class="btn btn-sm btn-outline-danger" onclick="deleteUser('${user.user_id}', 'Admin')">
  //                           <i class="fas fa-trash me-1"></i>Delete
  //                       </button>
  //                   </div>
  //               </div>
  //           </div>
  //       `);
  //   });
  //   // Build pagination
  //   // buildPagination('#adminPagination', adminPage, Math.ceil(adminUsers.length / pageSize), (p) => {
  //   //   adminPage = p;
  //   //   renderAdminUsers();
  //   // });
  // }

  // function renderTherapistUsers() {
  //   const tbody = $('#therapistTableBody');
  //   const mobileCards = $('#therapistMobileCards');

  //   tbody.empty();
  //   mobileCards.empty();

  //   if (therapistUsers.length === 0) {
  //     tbody.append(`
  //           <tr>
  //               <td colspan="8" class="text-center py-4">
  //                   <div class="text-muted">
  //                       <i class="fas fa-spa fa-3x mb-3"></i>
  //                       <h5>No Therapists Found</h5>
  //                       <p>Click "Add Therapist" to create therapist accounts.</p>
  //                       <small class="text-warning">Debug: Check browser console for data loading info</small>
  //                   </div>
  //               </td>
  //           </tr>
  //       `);
  //     return;
  //   }

  //   therapistUsers.forEach(user => {
  //     const avatar = user.profile_picture ?
  //       `<img src="${user.profile_picture}" class="rounded-circle" width="35" height="35">` :
  //       `<div class="rounded-circle bg-success d-flex align-items-center justify-content-center" style="width:35px;height:35px;"><i class="fas fa-spa text-white"></i></div>`;

  //     const scheduleStatus = user.is_active !== false ?
  //       '<span class="badge bg-success">Available</span>' :
  //       '<span class="badge bg-warning">Unavailable</span>';

  //     const joinDate = user.created_at ? new Date(user.created_at).toLocaleDateString() : 'N/A';
  //     const specialization = user.bio || 'General Therapy';
  //     const contact = user.contact_number || user.phone_number || 'Not provided';
  //     const servicesOffered = user.therapist_services || 'Multiple Services';

  //     // Desktop table row
  //     tbody.append(`
  //           <tr>
  //               <td>${avatar}</td>
  //               <td>
  //                   <div class="fw-semibold">${user.full_name}</div>
  //                   <small class="text-muted">${contact}</small>
  //               </td>
  //               <td><span class="text-muted">${user.email}</span></td>
  //               <td><span class="badge bg-info text-white">${specialization}</span></td>
  //               <td><small class="text-primary fw-semibold">${servicesOffered}</small></td>
  //               <td>${scheduleStatus}</td>
  //               <td><small class="text-muted">${joinDate}</small></td>
  //               <td>
  //                   <div class="btn-group btn-group-sm" role="group">
  //                       <button class="btn btn-outline-primary" onclick="editTherapist('${user.user_id}')" title="Edit Profile">
  //                           <i class="fas fa-edit"></i>
  //                       </button>
  //                       <button class="btn btn-outline-info" onclick="viewTherapist('${user.user_id}')" title="View Details">
  //                           <i class="fas fa-eye"></i>
  //                       </button>
  //                       <button class="btn btn-outline-success" onclick="manageTherapistSchedule('${user.user_id}')" title="Manage Schedule">
  //                           <i class="fas fa-calendar-alt"></i>
  //                       </button>
  //                       <button class="btn btn-outline-warning" onclick="manageTherapistServices('${user.user_id}')" title="Manage Services">
  //                           <i class="fas fa-spa"></i>
  //                       </button>
  //                       <button class="btn btn-outline-danger" onclick="deleteUser('${user.user_id}', 'Therapist')" title="Delete">
  //                           <i class="fas fa-trash"></i>
  //                       </button>
  //                   </div>
  //               </td>
  //           </tr>
  //       `);

  //     // Mobile card
  //     mobileCards.append(`
  //           <div class="card shadow-sm mb-3">
  //               <div class="card-body">
  //                   <div class="d-flex align-items-center mb-3">
  //                       ${avatar}
  //                       <div class="ms-3">
  //                           <div class="fw-semibold">${user.full_name}</div>
  //                           <div class="text-muted small">${user.email}</div>
  //                           <div class="text-muted small">${contact}</div>
  //                           <div class="mb-1">
  //                               <span class="badge bg-info text-white">${specialization}</span>
  //                           </div>
  //                           <div class="mb-1">
  //                               <small class="text-primary fw-semibold">${servicesOffered}</small>
  //                           </div>
  //                           ${scheduleStatus}
  //                       </div>
  //                   </div>
  //                   <div class="d-flex gap-1 flex-wrap">
  //                       <button class="btn btn-sm btn-outline-primary" onclick="editTherapist('${user.user_id}')" title="Edit">
  //                           <i class="fas fa-edit"></i>
  //                       </button>
  //                       <button class="btn btn-sm btn-outline-info" onclick="viewTherapist('${user.user_id}')" title="View">
  //                           <i class="fas fa-eye"></i>
  //                       </button>
  //                       <button class="btn btn-sm btn-outline-success" onclick="manageTherapistSchedule('${user.user_id}')" title="Schedule">
  //                           <i class="fas fa-calendar-alt"></i>
  //                       </button>
  //                       <button class="btn btn-sm btn-outline-warning" onclick="manageTherapistServices('${user.user_id}')" title="Services">
  //                           <i class="fas fa-spa"></i>
  //                       </button>
  //                       <button class="btn btn-sm btn-outline-danger" onclick="deleteUser('${user.user_id}', 'Therapist')" title="Delete">
  //                           <i class="fas fa-trash"></i>
  //                       </button>
  //                   </div>
  //               </div>
  //           </div>
  //       `);
  //   });
  // }

  // function updateCounts() {
  //   $('#userCount').text(regularUsers.length);
  //   $('#adminCount').text(adminUsers.length);
  //   $('#therapistCount').text(therapistUsers.length);
  // }

  // function showLoading(tab) {
  //   let target, colspan;

  //   switch (tab) {
  //     case 'users':
  //       target = '#userTableBody';
  //       colspan = '7';
  //       break;
  //     case 'admin':
  //       target = '#adminTableBody';
  //       colspan = '6';
  //       break;
  //     case 'therapist':
  //       target = '#therapistTableBody';
  //       colspan = '8';
  //       break;
  //     default:
  //       target = '#userTableBody';
  //       colspan = '7';
  //   }

  //   $(target).html(`
  //       <tr>
  //           <td colspan="${colspan}" class="text-center py-4">
  //               <div class="spinner-border text-primary" role="status"></div>
  //               <div class="mt-2 text-muted">Loading ${tab}...</div>
  //           </td>
  //       </tr>
  //   `);
  // }

  // function hideLoading(tab) {
  //   // Loading will be cleared by render functions
  // }

  // Action Functions
  function getModalUrl(filename) {
    const path = window.location.pathname;
    if (path.includes('/views/admin/')) return '../modal/' + filename; // when loaded directly under /views/admin/
    if (path.includes('/views/')) return './modal/' + filename; // when embedded under /views/
    return 'views/modal/' + filename; // fallback
  }

  function addNewAdmin() {
    showGlobalModal(getModalUrl('admin_modal-add-user.php'), {
      role: 'Admin',
      title: 'Add New Administrator'
    });
  }

  function demoteAdmin(userId, userName) {
    Swal.fire({
      title: 'Demote Administrator?',
      html: `Are you sure you want to demote <strong>${userName}</strong> to Customer role?<br><br>They will lose all administrator privileges.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, Demote',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: '../../controller/user_contr.php',
          type: 'POST',
          dataType: 'json',
          data: {
            action: 'update_role',
            id: atob(userId),
            role: 'User'
          },
          success: function(response) {
            if (response && response.status === 'success') {
              Swal.fire({
                icon: 'success',
                title: 'Demoted!',
                text: `${userName} has been demoted to Customer role.`,
                timer: 2000,
                showConfirmButton: false
              }).then(() => {
                // Refresh both Admin and User tables
                loadManageUser('Admin', 'adminTable');
                loadManageUser('User', 'userTable');
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Failed',
                text: response.message || 'Failed to demote administrator'
              });
            }
          },
          error: function() {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'An error occurred while demoting the administrator'
            });
          }
        });
      }
    });
  }

  function demoteTherapist(userId, userName) {
    Swal.fire({
      title: 'Demote Therapist?',
      html: `Are you sure you want to demote <strong>${userName}</strong> to Customer role?<br><br>They will lose therapist privileges and won't be available for bookings.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, Demote',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: '../../controller/user_contr.php',
          type: 'POST',
          dataType: 'json',
          data: {
            action: 'update_role',
            id: atob(userId),
            role: 'User'
          },
          success: function(response) {
            if (response && response.status === 'success') {
              Swal.fire({
                icon: 'success',
                title: 'Demoted!',
                text: `${userName} has been demoted to Customer role.`,
                timer: 2000,
                showConfirmButton: false
              }).then(() => {
                // Refresh both Therapist and User tables
                loadManageUser('Therapist', 'therapistTable');
                loadManageUser('User', 'userTable');
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Failed',
                text: response.message || 'Failed to demote therapist'
              });
            }
          },
          error: function() {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'An error occurred while demoting the therapist'
            });
          }
        });
      }
    });
  }

  function addNewTherapist() {
    showGlobalModal(getModalUrl('admin_modal-promote-therapist.php'), {
      role: 'Therapist',
      title: 'Promote to Therapist'
    });
  }

  // function editUser(userId, UserRole) {
  //   // const user = regularUsers.find(u => u.user_id === userId);
  //   showGlobalModal('../../views/modal/admin_modal-edit-user.php');
  //   $('#createdAt').val(userId);
  // }

  // function editAdmin(userId) {
  //   const user = adminUsers.find(u => u.user_id === userId);
  //   showGlobalModal('../../modal/admin_modal-edit-user.php', {
  //     id: userId,
  //     name: user?.full_name,
  //     email: user?.email,
  //     role: 'Admin'
  //   });
  // }

  // function editTherapist(userId) {
  //   const user = therapistUsers.find(u => u.user_id === userId);
  //   showGlobalModal('../../modal/admin_modal-edit-user.php', {
  //     id: userId,
  //     name: user?.full_name,
  //     email: user?.email,
  //     role: 'Therapist'
  //   });
  // }

  // function viewUser(userId) {
  //   showGlobalModal('../../modal/admin_modal-view-user.php', {
  //     user_id: userId,
  //     role: 'User'
  //   });
  // }

  // function viewAdmin(userId) {
  //   showGlobalModal('../../modal/admin_modal-view-user.php', {
  //     user_id: userId,
  //     role: 'Admin'
  //   });
  // }

  // function viewTherapist(userId) {
  //   showGlobalModal('../../modal/admin_modal-view-user.php', {
  //     user_id: userId,
  //     role: 'Therapist'
  //   });
  // }

  // function manageTherapistSchedule(userId) {
  //   showGlobalModal('../../modal/admin_modal-therapist-schedule.php', {
  //     user_id: userId
  //   });
  // }

  // function manageTherapistServices(userId) {
  //   showGlobalModal('../../modal/admin_modal-therapist-services.php', {
  //     user_id: userId
  //   });
  // }

  // function deleteUser(userId, role) {
  //   Swal.fire({
  //     title: `Delete ${role}?`,
  //     text: 'This action cannot be undone.',
  //     icon: 'warning',
  //     showCancelButton: true,
  //     confirmButtonColor: '#dc3545',
  //     confirmButtonText: 'Yes, Delete'
  //   }).then((result) => {
  //     if (result.isConfirmed) {
  //       // Use appropriate controller based on role
  //       const isTherapist = role.toLowerCase() === 'therapist';
  //       const controllerUrl = isTherapist ? '../../controller/therapist_contr.php' : '../../controller/user_contr.php';
  //       const action = isTherapist ? 'delete_therapist' : 'delete_user';
  //       const userIdField = isTherapist ? 'therapist_id' : 'user_id';

  //       $.ajax({
  //         url: controllerUrl,
  //         type: 'POST',
  //         dataType: 'json',
  //         data: {
  //           action: action,
  //           [userIdField]: userId
  //         },
  //         success: function(response) {
  //           if (response.status === 'success') {
  //             Swal.fire('Deleted!', `${role} has been deleted.`, 'success');
  //             refreshActiveTab();
  //           } else {
  //             Swal.fire('Error', response.message || 'Failed to delete user', 'error');
  //           }
  //         },
  //         error: function() {
  //           Swal.fire('Error', 'Failed to delete user', 'error');
  //         }
  //       });
  //     }
  //   });
  // }

  // function refreshActiveTab() {
  //   const activeTab = $('#userTabs .nav-link.active').attr('id');

  //   // Clear the appropriate user array and reload all data
  //   switch (activeTab) {
  //     case 'users-tab':
  //       regularUsers = [];
  //       break;
  //     case 'admin-tab':
  //       adminUsers = [];
  //       break;
  //     case 'therapist-tab':
  //       therapistUsers = [];
  //       break;
  //   }

  //   loadAllUsers();
  // }

  // // Global callback for modal success
  // window.userManagementSuccess = function() {
  //   refreshActiveTab();
  // };
</script>

<style>

</style>