<div class="modal-header">
  <h5 class="modal-title"><i class="fas fa-spa me-2"></i>Promote Customers to Therapist</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
  <div class="alert alert-info mb-3">
    <small>
      <i class="fas fa-info-circle me-1"></i>
      Select one or more <strong>customers</strong> (regular users) from the list below to promote them to Therapist role. 
      Only users with Customer role are shown here.
    </small>
  </div>

  <!-- Search Box -->
  <div class="mb-3">
    <input type="text" class="form-control" id="searchUsersTherapist" placeholder="Search by name or email...">
  </div>

  <!-- Select All Checkbox -->
  <div class="mb-3">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" id="selectAllUsersTherapist">
      <label class="form-check-label fw-semibold" for="selectAllUsersTherapist">
        Select All
      </label>
    </div>
  </div>

  <!-- Loading Indicator -->
  <div id="loadingUsersTherapist" class="text-center py-4">
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <p class="mt-2 text-muted">Loading customers...</p>
  </div>

  <!-- Users List -->
  <div id="usersListTherapist" style="display: none; max-height: 400px; overflow-y: auto;">
    <!-- Users will be loaded here dynamically -->
  </div>

  <!-- No Users Message -->
  <div id="noUsersMessageTherapist" style="display: none;" class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>No customer accounts found. All users are already Admins or Therapists.
  </div>

  <!-- Selected Count -->
  <div class="mt-3 text-end">
    <span class="badge bg-secondary" id="selectedCountTherapist">0 selected</span>
  </div>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
  <button type="button" id="promoteTherapistBtn" class="btn btn-success" onclick="promoteToTherapist()" disabled>
    <i class="fas fa-spa me-1"></i>Promote to Therapist
  </button>
</div>

<script>
let allUsersTherapist = [];

// Load users when modal opens
$(document).ready(function() {
  loadCustomerUsersForTherapist();
});

function loadCustomerUsersForTherapist() {
  $.ajax({
    url: '../../controller/user_contr.php',
    type: 'POST',
    dataType: 'json',
    data: {
      action: 'fetch_manage_users',
      role: 'User'
    },
    success: function(response) {
      $('#loadingUsersTherapist').hide();
      
      if (response.data && response.data.length > 0) {
        allUsersTherapist = response.data;
        displayUsersTherapist(allUsersTherapist);
        $('#usersListTherapist').show();
      } else {
        $('#noUsersMessageTherapist').show();
      }
    },
    error: function() {
      $('#loadingUsersTherapist').hide();
      $('#noUsersMessageTherapist').html('<i class="fas fa-exclamation-triangle me-2"></i>Failed to load customers.').show();
    }
  });
}

function displayUsersTherapist(users) {
  let html = '<div class="list-group">';
  
  users.forEach(function(user) {
    const avatar = user.profile_picture || '../../vendor/images/default_profile.png';
    const email = user.email || 'No email';
    const contact = user.contact_number || 'N/A';
    const joinDate = new Date(user.created_at).toLocaleDateString();
    
    html += `
      <label class="list-group-item list-group-item-action d-flex align-items-center user-item-therapist" style="cursor: pointer;">
        <input class="form-check-input me-3 user-checkbox-therapist" type="checkbox" value="${user.user_id}" data-name="${user.full_name}">
        <img src="${avatar}" class="rounded-circle me-3" width="50" height="50" style="object-fit: cover;">
        <div class="flex-grow-1">
          <h6 class="mb-1">${user.full_name}</h6>
          <small class="text-muted">
            <i class="fas fa-envelope me-1"></i>${email}
            <span class="mx-2">|</span>
            <i class="fas fa-phone me-1"></i>${contact}
            <span class="mx-2">|</span>
            <i class="fas fa-calendar me-1"></i>Joined: ${joinDate}
          </small>
        </div>
      </label>
    `;
  });
  
  html += '</div>';
  $('#usersListTherapist').html(html);
  
  // Update count when checkboxes change
  $('.user-checkbox-therapist').on('change', updateSelectedCountTherapist);
}

function updateSelectedCountTherapist() {
  const selectedCount = $('.user-checkbox-therapist:checked').length;
  $('#selectedCountTherapist').text(selectedCount + ' selected');
  $('#promoteTherapistBtn').prop('disabled', selectedCount === 0);
}

// Select All functionality
$('#selectAllUsersTherapist').on('change', function() {
  const isChecked = $(this).is(':checked');
  $('.user-checkbox-therapist:visible').prop('checked', isChecked);
  updateSelectedCountTherapist();
});

// Search functionality
$('#searchUsersTherapist').on('keyup', function() {
  const searchTerm = $(this).val().toLowerCase();
  
  if (searchTerm === '') {
    displayUsersTherapist(allUsersTherapist);
  } else {
    const filteredUsers = allUsersTherapist.filter(function(user) {
      return user.full_name.toLowerCase().includes(searchTerm) || 
             (user.email && user.email.toLowerCase().includes(searchTerm));
    });
    displayUsersTherapist(filteredUsers);
  }
  
  // Reset select all checkbox
  $('#selectAllUsersTherapist').prop('checked', false);
  updateSelectedCountTherapist();
});

function promoteToTherapist() {
  const selectedUsers = [];
  $('.user-checkbox-therapist:checked').each(function() {
    selectedUsers.push({
      id: $(this).val(),
      name: $(this).data('name')
    });
  });
  
  if (selectedUsers.length === 0) {
    Swal.fire('Warning', 'Please select at least one user to promote.', 'warning');
    return;
  }
  
  const userNames = selectedUsers.map(u => u.name).join(', ');
  const confirmMessage = selectedUsers.length === 1 
    ? `Are you sure you want to promote "${userNames}" to Therapist?`
    : `Are you sure you want to promote ${selectedUsers.length} users to Therapist?`;
  
  Swal.fire({
    title: 'Confirm Promotion',
    html: confirmMessage,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#198754',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Yes, promote them!',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      promoteUsersToTherapistRole(selectedUsers);
    }
  });
}

function promoteUsersToTherapistRole(users) {
  const btn = $('#promoteTherapistBtn');
  btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Processing...');
  
  let completed = 0;
  let successful = 0;
  let failed = 0;
  const total = users.length;
  
  // Process each user
  users.forEach(function(user) {
    $.ajax({
      url: '../../controller/user_contr.php',
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'update_role',
        id: user.id,
        role: 'Therapist'
      },
      success: function(response) {
        completed++;
        if (response && response.status === 'success') {
          successful++;
        } else {
          failed++;
        }
        checkCompletion();
      },
      error: function() {
        completed++;
        failed++;
        checkCompletion();
      }
    });
  });
  
  function checkCompletion() {
    if (completed === total) {
      btn.prop('disabled', false).html('<i class="fas fa-spa me-1"></i>Promote to Therapist');
      
      let message = '';
      if (successful === total) {
        message = successful === 1 
          ? '1 user has been successfully promoted to Therapist!' 
          : `${successful} users have been successfully promoted to Therapist!`;
        
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: message,
          timer: 2000,
          showConfirmButton: false
        }).then(() => {
          $('#globalModal').modal('hide');
          // Refresh the user tables
          if (typeof loadManageUser === 'function') {
            loadManageUser('User', 'userTable');
            loadManageUser('Therapist', 'therapistTable');
          }
          if (typeof loadCounts === 'function') {
            loadCounts();
          }
        });
      } else if (failed === total) {
        Swal.fire({
          icon: 'error',
          title: 'Failed',
          text: 'Failed to promote users. Please try again.'
        });
      } else {
        message = `${successful} user(s) promoted successfully, ${failed} failed.`;
        Swal.fire({
          icon: 'warning',
          title: 'Partial Success',
          text: message
        }).then(() => {
          $('#globalModal').modal('hide');
          // Refresh the user tables
          if (typeof loadManageUser === 'function') {
            loadManageUser('User', 'userTable');
            loadManageUser('Therapist', 'therapistTable');
          }
          if (typeof loadCounts === 'function') {
            loadCounts();
          }
        });
      }
    }
  }
}
</script>

<style>
.user-item-therapist {
  transition: background-color 0.2s;
}

.user-item-therapist:hover {
  background-color: #f8f9fa;
}

.user-checkbox-therapist {
  cursor: pointer;
}

.list-group-item {
  border-left: 3px solid transparent;
}

.list-group-item:has(.user-checkbox-therapist:checked) {
  border-left-color: #198754;
  background-color: #f0f9f4;
}
</style>