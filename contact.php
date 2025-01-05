<?php
session_start();
include_once 'includes/header.php';
?>

<main class="container py-5">
    <div class="row">
        <div class="col-md-6">
            <h1>Contact Us</h1>
            <p class="lead">Have questions about Porto? We're here to help!</p>
            
            <form id="contactForm" action="api/submit_contact.php" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                
                <div class="mb-3">
                    <label for="subject" class="form-label">Subject</label>
                    <input type="text" class="form-control" id="subject" name="subject" required>
                </div>
                
                <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Visit Our Office</h5>
                    <p class="card-text">
                        <i class="fas fa-map-marker-alt"></i> Rua das Flores, Porto, Portugal<br>
                        <i class="fas fa-phone"></i> +351 123 456 789<br>
                        <i class="fas fa-envelope"></i> info@vivaporto.com
                    </p>
                    
                    <h5 class="mt-4">Office Hours</h5>
                    <p class="card-text">
                        Monday - Friday: 9:00 AM - 6:00 PM<br>
                        Saturday: 10:00 AM - 2:00 PM<br>
                        Sunday: Closed
                    </p>
                </div>
            </div>
            
            <!-- Mini Map -->
            <div id="contactMap" style="height: 300px; margin-top: 20px;"></div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize contact page map
    const map = L.map('contactMap').setView([41.1579, -8.6291], 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Add marker for office location
    L.marker([41.1579, -8.6291])
        .addTo(map)
        .bindPopup('Viva Porto Office')
        .openPopup();
});

// Form submission handling
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('api/submit_contact.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Thank you for your message! We will get back to you soon.');
            this.reset();
        } else {
            alert('There was an error sending your message. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('There was an error sending your message. Please try again.');
    });
});
</script>

<?php include_once 'includes/footer.php'; ?>
