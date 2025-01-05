<?php
$url = require_once 'config/config.php';
include_once 'includes/header.php';
require_once 'config/Database.php';
require_once 'models/Banner.php';
require_once 'models/Destination.php';

function getBanners()
{
    $database = new Database();
    $db = $database->getConnection();
    $banner = new Banner($db);
    return $banner->readAll(); // Assuming you have a readAll method in your Banner model
}

$banners = getBanners();


function getFeaturedDestinations()
{
    $database = new Database();
    $db = $database->getConnection();
    $destination = new Destination($db); // Assuming you have a Destination model
    return $destination->readFeatured(); // Assuming you have a readFeatured method in your Destination model
}

$featuredDestinations = getFeaturedDestinations();


function getImageUrl($imagePath, $baseUrl)
{
    return $baseUrl . $imagePath; // Concatenate the base URL with the image path
}
?>

<main>
    <!-- Hero Section -->
    <section class="hero-section">
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php if (!empty($banners)): ?>
                    <?php foreach ($banners as $index => $banner): ?>
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <img src="<?php echo htmlspecialchars(getImageUrl($banner['image_url'], $url)); ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($banner['title']); ?>">
                            <div class="carousel-caption d-none d-md-block">
                                <h5><?php echo htmlspecialchars($banner['title']); ?></h5>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="carousel-item active">
                        <img src="path/to/default/image.jpg" class="d-block w-100" alt="Default Banner">
                        <div class="carousel-caption d-none d-md-block">
                            <h5>No Banners Available</h5>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Search Input -->
            <div class="input-search">
                <input type="text" id="searchInput" class="form-control" placeholder="Search destinations...">
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>

    <!-- Featured Destinations -->
    <section class="featured-destinations py-3 py-md-5 pb-md-3">
    <div class="container">
        <h2 class="text-center mb-4">Featured Destinations</h2>
        <div class="row">
            <?php if (!empty($featuredDestinations)): ?>
                <?php foreach ($featuredDestinations as $destination): ?>
                    <div class="col-12 col-md-3 mb-4 destination-item" data-category="<?php echo htmlspecialchars($destination['category']); ?>"> <!-- Use Bootstrap grid system -->
                        <div class="card"> <!-- Card component with specified width -->
                            <img src="<?php echo htmlspecialchars(getImageUrl($destination['image_url'], $url)); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($destination['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($destination['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($destination['short_description']); ?></p>
                                <a href="destination.php?id=<?php echo $destination['id']; ?>" class="btn btn-primary">Learn More</a> <!-- Button for action -->
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-center">No featured destinations available.</p>
                </div>
            <?php endif; ?>
        </div>
        <div id="noResultsMessage" class="text-center" style="display: none;">
            <p>No destinations found for your search.</p>
        </div>
    </div>
</section>


    <!-- Map Section -->
    <section class="map-section">
        <div class="container">
            <h2 class="text-center mb-4">Explore Porto</h2>
            <div id="map" style="height: 500px;"></div>
        </div>
    </section>

</main>

<?php include_once 'includes/footer.php'; ?>

</script>