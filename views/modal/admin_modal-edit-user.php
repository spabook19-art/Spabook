<div class="modal-header">
  <h5 class="modal-title">Edit User Information</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
  <form id="editUserForm">
    <input type="hidden" name="user_id" id="userId">
    <div class="mb-3 text-center">
      <img id="profileView"
        src="../../vendor/images/default_profile.png"
        class="rounded-circle shadow-sm"
        width="120" height="120"
        alt="Profile Picture">
    </div>
    <!-- Personal Information -->
    <h6 class="fw-bold mb-3">Personal Information</h6>
    <div class="row g-3">
      <div class="col-md-6">
        <label for="fullName" class="form-label">Full Name</span></label>
        <input type="text" class="form-control" id="fullName" name="full_name" readonly>
      </div>
      <div class="col-md-6">
        <label for="dateOfBirth" class="form-label">Date of Birth</label>
        <input type="date" class="form-control" id="dateOfBirth" name="date_of_birth" readonly>
      </div>
      <div class="col-md-6">
        <label for="gender" class="form-label">Gender</label>
        <select class="form-select" id="gender" name="gender" disabled>
          <option value="" selected disabled>Choose...</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
          <option value="Other">Other</option>
        </select>
      </div>
      <div class="col-md-6">
        <label for="contactNumber" class="form-label">Contact Number</label>
        <input type="text" class="form-control" id="contactNumber" name="contact_number" placeholder="+63 9XXXXXXXXX" readonly>
      </div>
      <div class="col-12">
        <label for="address" class="form-label">Address</label>
        <textarea class="form-control" id="address" name="address" rows="2" placeholder="Enter full address" readonly></textarea>
      </div>
    </div>

    <hr class="my-4">

    <!-- Account Information -->
    <h6 class="fw-bold mb-3">Account Information</h6>
    <div class="row g-3">
      <div class="col-md-6">
        <label for="email" class="form-label">Email</span></label>
        <input type="email" class="form-control" id="email" name="email" readonly>
      </div>
      <div class="col-md-6">
        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
        <select class="form-select" id="role" name="role" required>
          <option value="User">User</option>
          <option value="Admin">Admin</option>
          <option value="Therapist">Therapist</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Email Verified</label>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" id="isEmailVerified" name="is_email_verified" disabled>
          <label class="form-check-label" for="isEmailVerified">Verified</label>
        </div>
      </div>
      <div class="col-md-6">
        <label class="form-label">Number Verified</label>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" id="isNumberVerified" name="is_number_verified" disabled>
          <label class="form-check-label" for="isNumberVerified">Verified</label>
        </div>
      </div>
      <div class="col-12">
        <label for="bio" class="form-label">Bio</label>
        <textarea class="form-control" id="bio" name="bio" rows="3" placeholder="Short description about the user..." readonly></textarea>
      </div>
    </div>

    <hr class="my-4">

    <!-- System Metadata (Read-only) -->
    <h6 class="fw-bold mb-3">System Metadata</h6>
    <div class="row g-3">
      <div class="col-md-6">
        <label for="createdAt" class="form-label">Created At</label>
        <input type="text" class="form-control" id="createdAt" name="created_at" readonly>
      </div>
      <div class="col-md-6">
        <label for="updatedAt" class="form-label">Updated At</label>
        <input type="text" class="form-control" id="updatedAt" name="updated_at" readonly>
      </div>
      <div class="col-md-6">
        <label class="form-label">Agreed to Terms</label>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" id="agreedToTerms" name="agreed_to_terms" checked disabled>
          <label class="form-check-label" for="agreedToTerms">Yes</label>
        </div>
      </div>
    </div>

    <div class="mt-4 text-end">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
  </form>
</div>