// Update featured count on page load
function updateFeaturedCount() {
    const featured = Array.from(document.querySelectorAll('.form-check-input:checked')).length;
    document.getElementById('featuredCount').textContent = featured;
    document.getElementById('featuredCountCard').textContent = featured;
}

// Function to refresh data
function refreshData() {
    location.reload();
}

// Function to refresh quick actions
function refreshQuickActions() {
    // Implement refresh logic here
    loadMessages();
}

// Helper function to escape HTML
function escapeHtml(unsafe) {
    if (typeof unsafe !== 'string') {
        return '';
    }
    return unsafe
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// Load messages
function loadMessages() {
    const messagesList = document.getElementById('messagesList');

    fetch('../api/get_messages.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '';
                data.messages.forEach(message => {
                    html += `
                        <div class="card mb-3 ${message.is_read ? '' : 'border-primary'}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="card-subtitle text-muted">${message.name}</h6>
                                    <small class="text-muted">${message.created_at}</small>
                                </div>
                                <p class="card-text">${message.message}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="mailto:${message.email}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-reply"></i> Reply
                                    </a>
                                    ${!message.is_read ? `
                                        <button class="btn btn-sm btn-light" onclick="markAsRead(${message.id})">
                                            Mark as Read
                                        </button>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                });
                messagesList.innerHTML = html || '<p class="text-center text-muted my-5">No messages found</p>';
            } else {
                messagesList.innerHTML = '<div class="alert alert-danger">Error loading messages</div>';
            }
        })
        .catch(error => {
            messagesList.innerHTML = '<div class="alert alert-danger">Error loading messages</div>';
            console.error('Error:', error);
        });
}

// Edit destination
function editDestination(id) {
    // Update modal title
    document.getElementById('destinationModalTitle').textContent = 'Edit Destination';

    fetch(`../api/get_destination.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                showAlert(data.error, 'danger');
                return;
            }

            // Update form fields
            document.getElementById('destinationId').value = data.id;
            document.getElementById('name').value = data.name;
            document.getElementById('category').value = data.category;
            document.getElementById('shortDescription').value = data.shortDescription;
            document.getElementById('description').value = data.description;
            document.getElementById('currentImageUrl').value = data.imageUrl;
            document.getElementById('latitude').value = data.latitude;
            document.getElementById('longitude').value = data.longitude;
            document.getElementById('isFeatured').checked = data.is_featured;

            // Show/hide image controls
            const hasImageControls = document.getElementById('hasImageControls');
            const uploadControls = document.getElementById('uploadControls');

            if (data.imageUrl) {
                hasImageControls.classList.remove('d-none');
                uploadControls.classList.add('d-none');
            } else {
                hasImageControls.classList.add('d-none');
                uploadControls.classList.remove('d-none');
            }

            const modal = new bootstrap.Modal(document.getElementById('addDestinationModal'));
            modal.show();
        })
        .catch(error => {
            showAlert('Error loading destination details', 'danger');
            console.error('Error:', error);
        });
}

// Delete destination image
function deleteDestinationImage() {
    const imageUrl = document.getElementById('currentImageUrl').value;
    if (!imageUrl) return;

    if (confirm('Are you sure you want to delete this image?')) {
        const destinationId = document.getElementById('destinationId').value;

        fetch('../api/delete_image.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: destinationId
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('hasImageControls').classList.add('d-none');
                    document.getElementById('uploadControls').classList.remove('d-none');
                    document.getElementById('currentImageUrl').value = '';
                    showAlert('Image deleted successfully', 'success');
                } else {
                    showAlert(data.error || 'Error deleting image', 'danger');
                }
            })
            .catch(error => {
                showAlert('Error deleting image', 'danger');
                console.error('Error:', error);
            });
    }
}

// Save destination
function saveDestination() {
    const fileInput = document.getElementById('currentImageUrl');
    const file = fileInput.value;

    // Check if an image is selected
    if (!file) {
        showAlert('Please select an image file to upload.', 'danger', 'destination');
        return; // Exit the function if no file is selected
    }

    const data = {
        id: document.getElementById('destinationId').value,
        name: document.getElementById('name').value,
        category: document.getElementById('category').value,
        shortDescription: document.getElementById('shortDescription').value,
        description: document.getElementById('description').value,
        imageUrl: document.getElementById('currentImageUrl').value,
        latitude: document.getElementById('latitude').value,
        longitude: document.getElementById('longitude').value,
        is_featured: document.getElementById('isFeatured').checked
    };

    // Validate required fields
    const requiredFields = ['name', 'category', 'shortDescription', 'description', 'latitude', 'longitude'];
    for (const field of requiredFields) {
        if (!data[field]) {
            showAlert(`${field.replace(/([A-Z])/g, ' $1').toLowerCase()} is required`, 'danger', 'destination');
            return;
        }
    }

    // Validate image for new destinations
    if (!data.id && !data.imageUrl) {
        showAlert('Please upload an image for the destination', 'danger', 'destination');
        return;
    }

    fetch('../api/save_destination.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showAlert(result.message, 'success', 'destination');
                $('#addDestinationModal').modal('hide');
                reloadDestinationsTable();
                updateFeaturedCount();
            } else {
                showAlert(result.message, 'danger', 'destination');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Failed to save destination', 'danger', 'destination');
        });
}

// Reload destinations table
function reloadDestinationsTable() {
    fetch('../api/get_destinations.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tbody = document.querySelector('#destinations table tbody');
                let html = '';

                data.destinations.forEach(item => {
                    html += `
                        <tr>
                             <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h6 class="mb-0">${escapeHtml(item.id)}</h6>
                                    </div>
                                </div>
                            </td>
                            <td>
                                ${item.image_url ? `
                                    <img src="${item.image_url}" 
                                         alt="${escapeHtml(item.name)}"
                                         class="rounded" 
                                         style="width: 48px; height: 48px; object-fit: cover;">
                                ` : `
                                    <div class="rounded bg-light d-flex align-items-center justify-content-center" 
                                         style="width: 48px; height: 48px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                `}
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h6 class="mb-0">${escapeHtml(item.name)}</h6>
                                        <small class="text-muted">${escapeHtml(item.short_description)}</small>
                                    </div>
                                </div>
                            </td>
                            <td>${escapeHtml(item.category)}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" 
                                           ${item.is_featured ? 'checked' : ''} 
                                           onchange="updateFeatured(${item.id}, this.checked)">
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="editDestination(${item.id})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="deleteDestination(${item.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                tbody.innerHTML = html;
                updateFeaturedCount();
            } else {
                showAlert('Error loading destinations', 'danger', 'destination');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading destinations', 'danger', 'destination');
        });
}



// Reset form when modal is closed
document.getElementById('addDestinationModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('destinationModalTitle').textContent = 'Add New Destination';
    document.getElementById('destinationId').value = '';
    document.getElementById('currentImageUrl').value = '';
    document.getElementById('destinationForm').reset();
    document.getElementById('hasImageControls').classList.add('d-none');
    document.getElementById('uploadControls').classList.remove('d-none');
    document.getElementById('uploadProgress').classList.add('d-none');
    document.getElementById('uploadProgress').querySelector('.progress-bar').style.width = '0%';
});

// Delete destination
function deleteDestination(id) {
    if (!confirm('Are you sure you want to delete this destination? This will also delete any associated images.')) {
        return;
    }

    fetch('../api/delete_destination.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: id })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Destination deleted successfully', 'success', 'destination');
                reloadDestinationsTable();
            } else {
                throw new Error(data.error || 'Failed to delete destination');
            }
        })
        .catch(error => {
            showAlert(error.message || 'Error deleting destination', 'danger', 'destination');
            console.error('Error:', error);
        });
}

// Update featured status
function updateFeatured(id, featured) {
    fetch('../api/update_featured.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id: id,
            featured: featured
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateFeaturedCount();
            } else {
                alert('Error updating featured status');
                // Revert checkbox
                event.target.checked = !featured;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating featured status');
            // Revert checkbox
            event.target.checked = !featured;
        });
}

// Update featured count
function updateFeaturedCount() {
    fetch('../api/get_featured_count.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const featuredCount = document.getElementById('featuredCount');
                const featuredCountCard =document.getElementById('featuredCountCard');
                if (featuredCount) {
                    featuredCount.textContent = data.count;
                }
                if (featuredCountCard) {
                    featuredCountCard.textContent = data.count;
                }
            }
        })
        .catch(error => console.error('Error updating featured count:', error));
}

// Mark message as read
function markAsRead(id, button) {
    fetch('../api/mark_message_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadMessages(); // Reload messages if successful
            updateUnreadMessagesCount(); // Update the unread messages count
        } else {
            alert('Error marking message as read: ' + data.message); // Show specific error message
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error marking message as read');
    });
}

function updateUnreadMessagesCount() {
    fetch('../api/get_unread_messages_count.php')
        .then(response => response.json())
        .then(data => {
            const unreadMessagesBadge = document.querySelector('#unreadMessagesCount');
            const unreadMessagesTitle = document.querySelector('#unreadMessagesCountTitle'); // Select the <h2> element

            if (unreadMessagesBadge) { // Ensure the badge element exists
                if (data.count > 0) {
                    unreadMessagesBadge.textContent = data.count; // Update the badge with the new count
                    unreadMessagesBadge.style.display = 'inline'; // Show the badge
                    unreadMessagesTitle.textContent = data.count; // Update the <h2> with the new count
                } else {
                    unreadMessagesBadge.style.display = 'none'; // Hide the badge if no unread messages
                    unreadMessagesTitle.textContent = '0'; // Update the <h2> to show zero
                }
            } else {
                console.error('Element with ID "unreadMessagesCount" not found.');
            }
        })
        .catch(error => {
            console.error('Error fetching unread messages count:', error);
        });
}

// Upload image
function uploadImage(file) {
    const formData = new FormData();
    formData.append('image', file);

    // Show progress bar
    const progressBar = document.getElementById('uploadProgress');
    const progressBarInner = progressBar.querySelector('.progress-bar');
    progressBar.classList.remove('d-none');
    progressBarInner.style.width = '0%';

    fetch('../api/upload_image.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('currentImageUrl').value = data.imageUrl;
                document.getElementById('hasImageControls').classList.remove('d-none');
                document.getElementById('uploadControls').classList.add('d-none');
                showAlert('Image uploaded successfully', 'success', 'destination');
                progressBarInner.style.width = '100%';

                // Hide progress bar after a short delay
                setTimeout(() => {
                    progressBar.classList.add('d-none');
                    progressBarInner.style.width = '0%';
                }, 1000);
            } else {
                throw new Error(data.error || 'Failed to upload image');
            }
        })
        .catch(error => {
            progressBar.classList.add('d-none');
            showAlert(error.message || 'Error uploading image', 'danger', 'destination');
            console.error('Error:', error);
        });
}

// Handle file upload button click
document.getElementById('uploadButton').addEventListener('click', function () {
    const fileInput = document.getElementById('imageUpload');
    const file = fileInput.files[0];

    if (!file) {
        showAlert('Please select a file first', 'warning', 'destination');
        return;
    }

    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        showAlert('Please select a valid image file (JPG, PNG, or WEBP)', 'warning', 'destination');
        return;
    }

    // Validate file size (5MB max)
    const maxSize = 5 * 1024 * 1024; // 5MB in bytes
    if (file.size > maxSize) {
        showAlert('File size must be less than 5MB', 'warning', 'destination');
        return;
    }

    uploadImage(file);
});


// Reset form when banner modal is closed
document.getElementById('addBannerModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('bannerModalTitle').textContent = 'Add New Banner';
    document.getElementById('bannerId').value = '';
    document.getElementById('bannerTitle').value = '';
    document.getElementById('currentBannerImageUrl').value = '';
    document.getElementById('bannerForm').reset(); // Assuming you have a form with this ID
    document.getElementById('hasBannerImageControls').classList.add('d-none');
    document.getElementById('uploadBannerControls').classList.remove('d-none');
    document.getElementById('uploadBannerProgress').classList.add('d-none');
    document.getElementById('uploadBannerProgress').querySelector('.progress-bar').style.width = '0%';
});
// Save banner function
function saveBanner() {
    const fileInput = document.getElementById('bannerImageUpload');
    const file = fileInput.value;

    // Check if an image is selected
    if (!file) {
        showAlert('Please select an image file to upload.', 'danger', 'banner');
        return; // Exit the function if no file is selected
    }
    


    const data = {
        id: document.getElementById('bannerId').value,
        title: document.getElementById('bannerTitle').value,
        imageUrl: document.getElementById('currentBannerImageUrl').value
    };

    // Validate required fields
    const requiredFields = ['title', 'imageUrl'];
    for (const field of requiredFields) {
        if (!data[field]) {
            showAlert(`${field.replace(/([A-Z])/g, ' $1').toLowerCase()} is required`, 'danger', 'banner');
            return;
        }
    }

    // Validate image for new banners
    if (!data.id && !data.imageUrl) {
        showAlert('Please upload an image for the banner', 'danger', 'banner');
        return;
    }

    fetch('../api/save_banner.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showAlert(result.message, 'success', 'banner');
                // Optionally reload banners or update UI
                loadBannersTable();
                $('#addBannerModal').modal('hide'); // Hide the modal
            } else {
                showAlert(result.message, 'danger', 'banner');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Failed to save banner', 'danger', 'banner');
        });
}


// Load banners function
function loadBannersTable() {
    fetch('../api/get_banners.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const bannersContainer = document.querySelector('#banners tbody');
                bannersContainer.innerHTML = ''; // Clear existing banners
                data.banners.forEach(banner => {
                    const bannerRow = document.createElement('tr');
                    bannerRow.innerHTML = `
                        <td>
                            <h6 class="mb-0">${banner.id}</h6>
                        </td>
                        <td>
                            ${banner.image_url ? `<img src="${escapeHtml(banner.image_url)}" alt="${escapeHtml(banner.title)}" class="rounded" style="width: 48px; height: 48px; object-fit: cover;">` : `<div class="rounded bg-light d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;"><i class="fas fa-image text-muted"></i></div>`}
                        </td>
                        <td>
                            <h6 class="mb-0">${escapeHtml(banner.title)}</h6>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="editBanner(${banner.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-danger" onclick="deleteBanner(${banner.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    `;
                    bannersContainer.appendChild(bannerRow);
                });
            } else {
                console.error('Error loading banners:', data.message);
            }
        })
        .catch(error => console.error('Error:', error));
}

// Adjusted uploadBanner function to improve file upload handling
function uploadBanner(file) {
    const formData = new FormData();
    formData.append('image', file);

    // Show progress bar
    const progressBar = document.getElementById('uploadBannerProgress');
    const progressBarInner = progressBar.querySelector('.progress-bar');
    progressBar.classList.remove('d-none');
    progressBarInner.style.width = '0%';

    fetch('../api/upload_banner.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('currentBannerImageUrl').value = data.imageUrl;
                document.getElementById('hasBannerImageControls').classList.remove('d-none');
                document.getElementById('uploadBannerControls').classList.add('d-none');
                showAlert('Banner uploaded successfully', 'success', 'banner');
                progressBarInner.style.width = '100%';

                // Hide progress bar after a short delay
                setTimeout(() => {
                    progressBar.classList.add('d-none');
                    progressBarInner.style.width = '0%';
                }, 1000);
            } else {
                throw new Error(data.message || 'Failed to upload banner');
            }
        })
        .catch(error => {
            progressBar.classList.add('d-none');
            showAlert(error.message || 'Error uploading banner', 'danger', 'banner');
            console.error('Error:', error);
        });
}

// Adjusted event listener for uploadBannerButton
document.getElementById('uploadBannerButton').addEventListener('click', function () {
    const fileInput = document.getElementById('bannerImageUpload');
    const file = fileInput.files[0];

    if (!file) {
        showAlert('Please select a file first', 'warning', 'banner');
        return;
    }

    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        showAlert('Please select a valid image file (JPG, PNG, or WEBP)', 'warning', 'banner');
        return;
    }

    // Validate file size (5MB max)
    const maxSize = 5 * 1024 * 1024; // 5MB in bytes
    if (file.size > maxSize) {
        showAlert('File size must be less than 5MB', 'warning', 'banner');
        return;
    }

    uploadBanner(file);
});


function editBanner(id) {
    fetch(`../api/get_banner.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Populate modal fields
                document.getElementById('bannerId').value = data.banner.id;
                document.getElementById('bannerTitle').value = data.banner.title;
                document.getElementById('currentBannerImageUrl').value = data.banner.image_url;


                const hasImageControls = document.getElementById('hasBannerImageControls');
                const uploadControls = document.getElementById('uploadBannerControls');

                if (data.banner.image_url) {
                    hasImageControls.classList.remove('d-none');
                    uploadControls.classList.add('d-none');
                } else {
                    hasImageControls.classList.add('d-none');
                    uploadControls.classList.remove('d-none');
                }

                // Show the modal
                $('#addBannerModal').modal('show');
            } else {
                showAlert(data.message, 'danger', 'banner');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading banner details', 'danger', 'banner');
        });
}


// Delete banner
function deleteBanner(id) {
    if (!confirm('Are you sure you want to delete this banner? This will also delete any associated images.')) {
        return;
    }

    fetch('../api/delete_banner.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: id })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Banner deleted successfully', 'success', 'banner');
                loadBannersTable(); // Reload the banners table
            } else {
                throw new Error(data.error || 'Failed to delete banner');
            }
        })
        .catch(error => {
            showAlert(error.message || 'Error deleting banner', 'danger', 'banner');
            console.error('Error:', error);
        });
}


// Delete banner image
function deleteBannerImage() {
    const imageUrl = document.getElementById('currentBannerImageUrl').value;
    if (!imageUrl) return;

    if (confirm('Are you sure you want to delete this image?')) {
        const bannerId = document.getElementById('bannerId').value;

        fetch('../api/delete_banner_image.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: bannerId
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('hasBannerImageControls').classList.add('d-none');
                    document.getElementById('uploadBannerControls').classList.remove('d-none');
                    document.getElementById('currentBannerImageUrl').value = '';
                    showAlert('Image deleted successfully', 'success', 'banner');
                } else {
                    showAlert(data.error || 'Error deleting image', 'danger', 'banner');
                }
            })
            .catch(error => {
                showAlert('Error deleting image', 'danger', 'banner');
                console.error('Error:', error);
            });
    }
}

// Show alert function
function showAlert(message, type = 'success', selector) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    const container = document.querySelector('.modal-body-'+selector);
    container.insertBefore(alertDiv, container.firstChild);

    // Auto dismiss after 5 seconds
    setTimeout(() => {
        alertDiv.classList.remove('show');
        setTimeout(() => alertDiv.remove(), 150);
    }, 5000);
}



// View analytics
function viewAnalytics() {
    // Scroll to the analytics tab
    const analyticsTab = document.getElementById('analytics');
    analyticsTab.scrollIntoView({ behavior: 'smooth' });

    // Set the analytics tab as active
    const analyticsTabLink = document.querySelector('a[href="#analytics"]');
    if (analyticsTabLink) {
        analyticsTabLink.click();
    }

    // Fetch visitor data
    fetch('../api/get_visitors.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tbody = document.getElementById('visitorTableBody');
                tbody.innerHTML = '';
                data.visitors.forEach(visitor => {
                    const row = `<tr><td>${visitor.id}</td><td>${new Date(visitor.visit_time).toLocaleString()}</td></tr>`;
                    tbody.innerHTML += row;
                });
            } else {
                console.error('Error fetching visitors:', data.error);
            }
        })
        .catch(error => console.error('Error:', error));
}

// Initialize
document.addEventListener('DOMContentLoaded', function () {
    updateFeaturedCount();
    // Load messages when messages tab is shown
    document.querySelector('a[href="#messages"]').addEventListener('shown.bs.tab', loadMessages);
    // Load visitor data on dashboard load

    fetch('../api/get_visitors.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tbody = document.getElementById('visitorTableBody');
                tbody.innerHTML = '';
                data.visitors.forEach(visitor => {
                    const row = `<tr><td>${visitor.id}</td><td>${new Date(visitor.visit_time).toLocaleString()}</td></tr>`;
                    tbody.innerHTML += row;
                });
            } else {
                console.error('Error fetching visitors:', data.error);
            }
        })
        .catch(error => console.error('Error:', error));
});
