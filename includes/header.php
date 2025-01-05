<?php
session_start();
require_once __DIR__ . '/UrlHelper.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viva Porto - Discover Porto's Beauty</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
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
                    <?php if(isset($_SESSION['is_admin'])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo strpos(UrlHelper::getCurrentUrl(), '/admin') !== false ? 'active' : ''; ?>" 
                               href="<?php echo UrlHelper::getAdminUrl(); ?>">Admin</a>
                        </li>
                    <?php else :  ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo strpos(UrlHelper::getCurrentUrl(), '/admin') !== false ? 'active' : ''; ?>" 
                               href="<?php echo UrlHelper::getAdminUrl(); ?>">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
