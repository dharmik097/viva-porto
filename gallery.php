<?php
session_start();
$url = require_once 'config/config.php';
include_once 'includes/header.php';
require_once 'config/Database.php';
require_once 'models/Destination.php';

$database = new Database();
$db = $database->getConnection();
$destination = new Destination($db);
$destinations = $destination->readAll();


function getImageUrl($imagePath, $baseUrl)
{
    return $baseUrl . $imagePath; // Concatenate the base URL with the image path
}
?>

<main class="container py-5">
    <h1 class="text-center mb-4">Discover Porto's Treasures</h1>

    <!-- Search and Filter Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <input  type="text" id="searchInput" class="form-control" placeholder="Search destinations..." onkeyup="searchDestinations()">
        </div>
        <div class="col-md-6">
            <div class="btn-group w-100" role="group">
                <button type="button" class="btn btn-outline-primary" onclick="filterDestinations('all')">All</button>
                <button type="button" class="btn btn-outline-primary" onclick="filterDestinations('Attractions')">Attractions</button>
                <button type="button" class="btn btn-outline-primary" onclick="filterDestinations('Hotels')">Hotels</button>
                <button type="button" class="btn btn-outline-primary" onclick="filterDestinations('Restaurants')">Restaurants</button>
            </div>
        </div>
    </div>

    <!-- Gallery Grid -->
    <div class="row" id="galleryGrid">
        <?php foreach($destinations as $item): ?>
        <div class="col-12 col-md-3 mb-4 destination-item" data-category="<?php echo htmlspecialchars($item['category']); ?>">
            <div class="card h-100">
                <img src="<?php echo htmlspecialchars(getImageUrl($item['image_url'], $url)); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['name']); ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($item['short_description']); ?></p>
                    <a href="destination.php?id=<?php echo $item['id']; ?>" class="btn btn-primary">Learn More</a> 
                </div>
            </div>
        </div>

        <!-- Modal for each destination -->
        <div class="modal fade" id="modal<?php echo $item['id']; ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" class="img-fluid mb-3" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <p><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<?php include_once 'includes/footer.php'; ?>
