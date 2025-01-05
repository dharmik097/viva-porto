<?php
session_start();
include_once 'includes/header.php';
require_once 'config/Database.php';
require_once 'models/Destination.php';

$database = new Database();
$db = $database->getConnection();
$destination = new Destination($db);
$categories = $destination->getCategories(); // Fetch categories
?>

<main>
    <div class="container-fluid px-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-3 p-3 fit-on-page fit-on-page-categories" style="background-color: #ECF0F1; overflow-y: auto;">
                <h3>Explore Porto</h3>
                <div class="mb-3">
                    <input type="text" id="locationSearch" class="form-control" placeholder="Search locations...">
                </div>

                <div class="accordion" id="locationFilters">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#categoryFilter">
                                Categories
                            </button>
                        </h2>
                        <div id="categoryFilter" class="accordion-collapse collapse show">
                            <div class="accordion-body">
                                <?php foreach ($categories as $category): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="<?php echo htmlspecialchars($category); ?>" id="<?php echo htmlspecialchars($category); ?>Check">
                                        <label class="form-check-label" for="<?php echo htmlspecialchars($category); ?>Check">
                                            <?php echo htmlspecialchars(ucfirst($category)); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map -->
            <div class="col-md-9">
                <div id="map" class="fit-on-page" style="margin-top: 20px;"></div>
            </div>
        </div>
    </div>
</main>



<?php include_once 'includes/footer.php'; ?>