<div class="modal-header">
    <h5 class="modal-title">User Details</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body" id="userDetailsContent">
    <div class="text-center py-4">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="mt-2">Loading user details...</div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary" onclick="editCurrentUser()">Edit User</button>
</div>

<script>

function loadUserDetails(userId) {
   
}

function displayUserDetails(user) {
    
}

function editCurrentUser() {
    $('#globalModal').modal('hide');
    
    // Open appropriate edit modal based on role
    if (currentUserRole === 'Therapist') {
        showGlobalModal('../modal/admin_modal-edit-therapist.php', {
            user_id: currentUserId,
            action: 'edit'
        });
    } else {
        showGlobalModal('../modal/admin_modal-edit-user.php', {
            id: currentUserId,
            action: 'edit'
        });
    }
}
</script>

<style>
.modal-body .row > div {
    margin-bottom: 1rem;
}

.form-label {
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.fw-semibold {
    font-weight: 600;
}
</style>