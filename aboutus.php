<?php
session_start();
require_once __DIR__ . '/UrlHelper.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viva Porto - About Us</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo UrlHelper::getUrl('assets/css/style.css'); ?>">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top custom-navbar" style="background-color: #2C3E50;">
        <div class="container">
            <a class="navbar-brand" href="<?php echo UrlHelper::getUrl(); ?>">Viva Porto</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo UrlHelper::isCurrentUrl('') ? 'active' : ''; ?>" 
                           href="<?php echo UrlHelper::getUrl(); ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo UrlHelper::isCurrentUrl('gallery') ? 'active' : ''; ?>" 
                           href="<?php echo UrlHelper::getUrl('gallery'); ?>">Gallery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo UrlHelper::isCurrentUrl('map') ? 'active' : ''; ?>" 
                           href="<?php echo UrlHelper::getUrl('map'); ?>">Map</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo UrlHelper::isCurrentUrl('contact') ? 'active' : ''; ?>" 
                           href="<?php echo UrlHelper::getUrl('contact'); ?>">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo UrlHelper::isCurrentUrl('about') ? 'active' : ''; ?>" 
                           href="<?php echo UrlHelper::getUrl('about'); ?>">About Us</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="row">
            <div class="col-md-12">
                <h1>About Us</h1>
                <p class="lead">Welcome to Viva Porto - Discover the beauty and culture of Porto!</p>
                <p>Viva Porto is dedicated to showcasing the rich history, vibrant culture, and stunning landscapes of Porto, Portugal. Our mission is to help you explore and experience the best that Porto has to offer.</p>

                <h3>Our Mission</h3>
                <p>Our mission is to create memorable experiences for visitors by highlighting the unique charm of Porto. From historical landmarks to hidden gems, we aim to make your journey unforgettable.</p>

                <h3>Our Team</h3>
                <div class="row text-center">
                    <div class="col-md-3">
                        <img src="https://via.placeholder.com/100" class="rounded-circle mb-2" alt="Team Member 1">
                        <p>John Doe - Founder</p>
                    </div>
                    <div class="col-md-3">
                        <img src="https://via.placeholder.com/100" class="rounded-circle mb-2" alt="Team Member 2">
                        <p>Jane Smith - Marketing Head</p>
                    </div>
                    <div class="col-md-3">
                        <img src="https://via.placeholder.com/100" class="rounded-circle mb-2" alt="Team Member 3">
                        <p>Mark Johnson - Tour Guide</p>
                    </div>
                    <div class="col-md-3">
                        <img src="https://via.placeholder.com/100" class="rounded-circle mb-2" alt="Team Member 4">
                        <p>Emily Davis - Customer Support</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
