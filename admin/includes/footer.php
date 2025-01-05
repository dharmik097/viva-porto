    </div><!-- End Main Content Container -->
    </main>
    <footer class="admin-footer">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span>&copy; <?php echo date('Y'); ?> Porto Tourism. All rights reserved.</span>
                </div>
                <div>
                    <a href="../" target="_blank" class="me-3">View Website</a>
                    <a href="https://www.visitporto.travel" target="_blank">Official Porto Tourism</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      var script = document.createElement('script');
      script.src = '../assets/js/dashboard/dashboard.js?v=' + new Date().getTime();
      document.head.appendChild(script);
    </script>
    
    <!-- Admin Scripts -->
    <script>
        // Highlight current page in navigation
        document.addEventListener('DOMContentLoaded', function() {
            const currentPage = window.location.pathname.split('/').pop();
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPage) {
                    link.classList.add('active');
                }
            });
        });

        // Logout function
        function logout() {
            fetch('../api/admin_logout.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'login.php';
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>
