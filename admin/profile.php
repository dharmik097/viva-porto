<?php
session_start();

// Include Database and User classes
require_once '../config/Database.php';
require_once '../models/User.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit();
}

// Include admin header
include_once 'includes/header.php';

// Get total visitors
$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$totalVisitors = $user->getTotalVisitors();
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Profile</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Profile</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Profile Content -->
<div class="row">
    <!-- Profile Information -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Profile Information</h5>
            </div>
            <div class="card-body">
                <form id="profileForm">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newPassword" placeholder="Enter new password">
                        <small class="text-muted">Leave blank if you don't want to change the password</small>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm new password">
                    </div>
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="currentPassword" placeholder="Enter current password to save changes" required>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="updateProfile()">
                        Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Account Information -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Account Information</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-circle fa-2x"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mb-1"><?php echo htmlspecialchars($_SESSION['username']); ?></h5>
                        <p class="text-muted mb-0">Administrator</p>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Account Status</label>
                    <p class="mb-0"><span class="badge bg-success">Active</span></p>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Role</label>
                    <p class="mb-0">Administrator</p>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Total Visitors</label>
                    <p class="mb-0"><?php echo $totalVisitors; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateProfile() {
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const currentPassword = document.getElementById('currentPassword').value;

    // Validate current password
    if (!currentPassword) {
        showAlert('Current password is required', 'danger');
        return;
    }

    // Validate new password match if provided
    if (newPassword || confirmPassword) {
        if (newPassword !== confirmPassword) {
            showAlert('New passwords do not match', 'danger');
            return;
        }
        if (newPassword.length < 6) {
            showAlert('New password must be at least 6 characters long', 'danger');
            return;
        }
    }

    // Send update request
    fetch('../api/update_profile.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            new_password: newPassword,
            current_password: currentPassword
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Profile updated successfully', 'success');
            // Clear password fields
            document.getElementById('newPassword').value = '';
            document.getElementById('confirmPassword').value = '';
            document.getElementById('currentPassword').value = '';
        } else {
            throw new Error(data.error || 'Failed to update profile');
        }
    })
    .catch(error => {
        showAlert(error.message || 'Error updating profile', 'danger');
        console.error('Error:', error);
    });
}

// Show alert function (if not already defined in your header)
function showAlert(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Find the form and insert the alert before it
    const form = document.getElementById('profileForm');
    form.parentNode.insertBefore(alertDiv, form);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>

<?php include_once 'includes/footer.php'; ?>
