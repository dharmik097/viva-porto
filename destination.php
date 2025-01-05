<?php
session_start();
require_once 'config/Database.php';
require_once 'models/Destination.php';
include_once 'includes/header.php';

// Get destination ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    UrlHelper::redirect('404');
}

// Get destination details
$database = new Database();
$db = $database->getConnection();

$destination = new Destination($db);
$details = $destination->read($id);

if (!$details) {
    UrlHelper::redirect('404');
}
?>

<main class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo UrlHelper::getUrl(); ?>">Home</a></li>
            <li class="breadcrumb-item">
                <a href="<?php echo UrlHelper::getCategoryUrl($details['category']); ?>">
                    <?php echo ucfirst($details['category']); ?>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($details['name']); ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <img src="<?php echo UrlHelper::getUrl($details['image_url']); ?>" 
                 class="img-fluid rounded mb-4" 
                 alt="<?php echo htmlspecialchars($details['name']); ?>">
            
            <h1><?php echo htmlspecialchars($details['name']); ?></h1>
            <p class="lead"><?php echo htmlspecialchars($details['short_description']); ?></p>
            
            <div class="mt-4">
                <?php echo nl2br(htmlspecialchars($details['description'])); ?>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Location</h5>
                    <div id="destinationMap" style="height: 300px;"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Share</h5>
                    <div class="d-flex gap-2">
                        <a href="https://facebook.com/sharer/sharer.php?u=<?php echo urlencode(UrlHelper::getCurrentUrl()); ?>" 
                           class="btn btn-primary" target="_blank">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(UrlHelper::getCurrentUrl()); ?>&text=<?php echo urlencode($details['name']); ?>" 
                           class="btn btn-info text-white" target="_blank">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://pinterest.com/pin/create/button/?url=<?php echo urlencode(UrlHelper::getCurrentUrl()); ?>&media=<?php echo urlencode(UrlHelper::getUrl($details['image_url'])); ?>&description=<?php echo urlencode($details['short_description']); ?>" 
                           class="btn btn-danger" target="_blank">
                            <i class="fab fa-pinterest-p"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map
    const map = L.map('destinationMap').setView([
        <?php echo $details['latitude']; ?>, 
        <?php echo $details['longitude']; ?>
    ], 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Add marker
    L.marker([<?php echo $details['latitude']; ?>, <?php echo $details['longitude']; ?>])
        .addTo(map)
        .bindPopup('<?php echo htmlspecialchars($details['name']); ?>');
});
</script>

<?php include_once 'includes/footer.php'; ?>
