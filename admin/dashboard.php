<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit();
}

// Include admin header
include_once 'includes/header.php';

// Get necessary data
require_once '../config/Database.php';
require_once '../models/Destination.php';
require_once '../models/Message.php';
require_once '../models/User.php';
require_once '../models/Banner.php';

$database = new Database();
$db = $database->getConnection();

$destination = new Destination($db);
$message = new Message($db);
$user = new User($db);
$banner = new Banner($db);

$destinations = $destination->readAll();
$unreadMessages = $message->getUnreadCount();
$messages = $message->readAll();
$totalVisitors = $user->getTotalVisitors();
$banners = $banner->readAll();

// Fetch visitor data
$query = "SELECT * FROM visitors ORDER BY visit_time DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$visitors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to get full image URL
function getImageUrl($path) {
    if (empty($path)) return '';
    // Remove leading slash if present
    $path = ltrim($path, '/');
    return '../' . $path;
}

?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Dashboard</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">Overview</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDestinationModal" id="addDestinationBtn">
            <i class="fas fa-plus"></i> Add Destination
        </button>

        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addBannerModal" id="addBannerBtn">
            <i class="fas fa-plus"></i> Add Banner
        </button>

        <button type="button" class="btn btn-outline-secondary" onclick="refreshData()">
            <i class="fas fa-sync"></i> Refresh
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                        </div>
                    </div>
                    <div>
                        <h6 class="card-subtitle mb-1 text-muted">Total Destinations</h6>
                        <h2 class="card-title mb-0"><?php echo count($destinations); ?></h2>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="#destinations" class="btn btn-sm btn-light w-100" data-bs-toggle="tab">View All →</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-envelope fa-2x text-success"></i>
                        </div>
                    </div>
                    <div>
                        <h6 class="card-subtitle mb-1 text-muted">Unread Messages</h6>
                        <h2 class="card-title mb-0" id="unreadMessagesCountTitle"><?php echo $unreadMessages; ?></h2>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="#messages" class="btn btn-sm btn-light w-100" data-bs-toggle="tab">View Messages →</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-star fa-2x text-warning"></i>
                        </div>
                    </div>
                    <div>
                        <h6 class="card-subtitle mb-1 text-muted">Featured Places</h6>
                        <h2 class="card-title mb-0" id="featuredCount">0</h2>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="#destinations" class="btn btn-sm btn-light w-100" data-bs-toggle="tab">Manage Featured →</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-users fa-2x text-info"></i>
                        </div>
                    </div>
                    <div>
                        <h6 class="card-subtitle mb-1 text-muted">Total Visitors</h6>
                        <h2 class="card-title mb-0"><?php echo $totalVisitors; ?></h2>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-sm btn-light w-100" onclick="viewAnalytics()">View Analytics →</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions and Recent Activity -->
<div class="row g-4 mb-4">
    <!-- Quick Actions -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action" onclick="event.preventDefault(); document.getElementById('addDestinationBtn').click();">
                        <i class="fas fa-plus text-success me-2"></i>
                        Add New Destination
                    </a>

                    <a href="#" class="list-group-item list-group-item-action" onclick="event.preventDefault(); document.getElementById('addBannerBtn').click();">
                        <i class="fas fa-plus text-success me-2"></i>
                        Add New Banner
                    </a>

                    <a href="../" class="list-group-item list-group-item-action" target="_blank">
                        <i class="fas fa-external-link-alt text-info me-2"></i>
                        View Website
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Featured Destinations</h5>
            </div>
            <div class="card-body">
                <p class="mb-0">
                    <i class="fas fa-star text-warning me-2"></i>
                    Currently featuring <span id="featuredCountCard">0</span> destinations
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Content Tabs -->
<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="#destinations" data-bs-toggle="tab">
                    <i class="fas fa-map-marker-alt"></i> Destinations
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#messages" data-bs-toggle="tab" onclick="loadMessages()">
                    <i class="fas fa-envelope"></i> Messages
                    <?php if ($unreadMessages > 0): ?>
                        <span id="unreadMessagesCount" class="badge bg-danger ms-1"><?php echo $unreadMessages; ?></span>
                    <?php else: ?>
                        <span id="unreadMessagesCount" class="badge bg-danger ms-1" style="display: none;">0</span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#analytics" data-bs-toggle="tab">
                    <i class="fas fa-chart-bar"></i> Analytics
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#banners" data-bs-toggle="tab">
                    <i class="fas fa-image"></i> Banners
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <!-- Destinations Tab -->
            <div class="tab-pane fade show active" id="destinations">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                            <th class="text-center">ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Featured</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($destinations as $item): ?>
                            <tr>
                            <td class="text-center">
                        <h6 class="mb-0"><?php echo $item['id']; ?></h6>
                   </td>
                                <td>
                                    <?php if (!empty($item['image_url'])): ?>
                                        <img src="<?php echo htmlspecialchars(getImageUrl($item['image_url'])); ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                                             class="rounded" 
                                             style="width: 48px; height: 48px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="rounded bg-light d-flex align-items-center justify-content-center" 
                                             style="width: 48px; height: 48px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="ms-2">
                                            <h6 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                            <small class="text-muted"><?php echo htmlspecialchars($item['short_description']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($item['category']); ?></td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" 
                                               <?php echo $item['is_featured'] ? 'checked' : ''; ?>
                                               onchange="updateFeatured(<?php echo $item['id']; ?>, this.checked)">
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" onclick="editDestination(<?php echo $item['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="deleteDestination(<?php echo $item['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Messages Tab -->
            <div class="tab-pane fade" id="messages">
                <div id="messagesList">
                    <!-- Messages will be loaded dynamically -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Tab -->
            <div class="tab-pane fade" id="analytics">
                <h5 class="card-title mb-0">Visitor Analytics</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Visit Time</th>
                        </tr>
                    </thead>
                    <tbody id="visitorTableBody">
                        <?php foreach ($visitors as $visitor): ?>
                            <tr>
                                <td><?php echo $visitor['id']; ?></td>
                                <td><?php echo date('Y-m-d H:i:s', strtotime($visitor['visit_time'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Banners Tab -->
            <div class="tab-pane fade" id="banners">
                <h3>Banners</h3>
                <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-center">ID</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Actions</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($banners as $banner): ?>
                <tr>
                    <td class="text-center">
                        <h6 class="mb-0"><?php echo $banner['id']; ?></h6>
                   </td>
                    <td>
                        <?php if (!empty($banner['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars(getImageUrl($banner['image_url'])); ?>" 
                                 alt="<?php echo htmlspecialchars($banner['title']); ?>"
                                 class="rounded" 
                                 style="width: 48px; height: 48px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded bg-light d-flex align-items-center justify-content-center" 
                                 style="width: 48px; height: 48px;">
                                <i class="fas fa-image text-muted"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <h6 class="mb-0"><?php echo htmlspecialchars($banner['title']); ?></h6>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="editBanner(<?php echo $banner['id']; ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="deleteBanner(<?php echo $banner['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Destination Modal -->
<div class="modal fade" id="addDestinationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="destinationModalTitle">Add New Destination</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body  modal-body-destination">
                <form id="destinationForm">
                    <input type="hidden" id="destinationId">
                    <input type="hidden" id="currentImageUrl">
                    
                    <!-- Image Upload Section -->
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <div id="imageControls">
                            <div id="hasImageControls" class="d-none">
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteDestinationImage()">
                                    <i class="fas fa-trash"></i> Delete Current Image
                                </button>
                            </div>
                            <div id="uploadControls" class="mt-2">
                                <div class="input-group">
                                    <input type="file" class="form-control" id="imageUpload" accept="image/*">
                                    <button class="btn btn-outline-secondary" type="button" id="uploadButton">
                                        Upload
                                    </button>
                                </div>
                                <div id="uploadProgress" class="progress mt-2 d-none">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                         role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" required 
                                placeholder="e.g., Clérigos Tower">
                        </div>
                        <div class="col-md-6">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" required>
                                <option value="">Select Category</option>
                                <option value="Attractions">Attractions</option>
                                <option value="Restaurants">Restaurants</option>
                                <option value="Hotels">Hotels</option>
                                <option value="Museums">Museums</option>
                                <option value="Parks">Parks</option>
                                <option value="Shopping">Shopping</option>
                                <option value="Nightlife">Nightlife</option>
                                <option value="Religious">Religious</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="shortDescription" class="form-label">Short Description</label>
                            <input type="text" class="form-control" id="shortDescription" required 
                                placeholder="Brief description for cards and previews (max 150 characters)">
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Full Description</label>
                            <textarea class="form-control" id="description" rows="3" required 
                                placeholder="Detailed description of the destination, including historical information, opening hours, ticket prices, etc."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="latitude" class="form-label">Latitude</label>
                            <input type="number" class="form-control" id="latitude" step="any" required 
                                placeholder="e.g., 41.1457">
                        </div>
                        <div class="col-md-6">
                            <label for="longitude" class="form-label">Longitude</label>
                            <input type="number" class="form-control" id="longitude" step="any" required 
                                placeholder="e.g., -8.6149">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="isFeatured">
                                <label class="form-check-label" for="isFeatured">Feature this destination on the homepage</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveDestination()">Save Destination</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Banner Modal -->
<div class="modal fade" id="addBannerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bannerModalTitle">Add New Banner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-banner">
                
                <form id="bannerForm">
                    <input type="hidden" id="bannerId">
                    <input type="hidden" id="currentBannerImageUrl">
                    
                    <!-- Image Upload Section -->
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <div id="bannerImageControls">
                            <div id="hasBannerImageControls" class="d-none">
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteBannerImage()">
                                    <i class="fas fa-trash"></i> Delete Current Image
                                </button>
                            </div>
                            <div id="uploadBannerControls" class="mt-2">
                                <div class="input-group">
                                    <input type="file" class="form-control" id="bannerImageUpload" accept="image/*">
                                    <button class="btn btn-outline-secondary" type="button" id="uploadBannerButton">
                                        Upload
                                    </button>
                                </div>
                                <div id="uploadBannerProgress" class="progress mt-2 d-none">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" id="bannerUploadProgress"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Fields -->
                    <div class="mb-3">
                        <label for="bannerTitle" class="form-label">Banner Title</label>
                        <input type="text" class="form-control" id="bannerTitle" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveBanner()">Save Banner</button>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
