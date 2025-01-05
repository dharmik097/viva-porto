// Initialize map if element exists
if (document.getElementById('map')) {
    var map = L.map('map').setView([41.14961, -8.61099], 13); // Default view (Porto, Portugal)

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Custom marker icons
    const standardIcon = L.icon({
        iconUrl: 'assets/images/markers/highlighted.png',
        iconSize: [36, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34]
    });

    const highlightedIcon = L.icon({
        iconUrl: 'assets/images/markers/highlighted.png',
        iconSize: [36, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34]
    });

    let markers = []; // Array to hold markers

    // Function to add markers
    function addMarkers(locations) {
        locations.forEach(location => {
            if (location.latitude && location.longitude) { // Ensure latitude and longitude are defined
                const marker = L.marker([location.latitude, location.longitude], {
                    icon: location.highlighted ? highlightedIcon : standardIcon,
                    category: location.category // Set the category property
                }).addTo(map);

                marker.bindPopup(`
                    <h5>${location.name}</h5>
                    <p>${location.description}</p>
                    <a href="destination.php?id=${location.id}" class="btn btn-sm btn-primary">Learn More</a>
                `);

                markers.push(marker); // Store the marker in the array
            } else {
                console.error('Invalid destination data:', location); // Log invalid data
            }
        });
    }

    // Load markers from API
    fetch('api/get_locations.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(locations => {
            addMarkers(locations); // Call the function to add markers
        })
        .catch(error => {
            console.error('Error fetching locations:', error);
        });

    // Function to filter markers by category
    function filterMarkersByCategory() {
        const selectedCategories = Array.from(document.querySelectorAll('.form-check-input:checked')).map(input => input.value);

        markers.forEach(marker => {
            const markerCategory = marker.options.category; // Access the category property
            if (selectedCategories.length === 0 || selectedCategories.includes(markerCategory)) {
                marker.addTo(map); // Show marker if it matches selected categories
            } else {
                map.removeLayer(marker); // Hide marker if it doesn't match
            }
        });
    }

    // Add event listeners to checkboxes
    document.querySelectorAll('.form-check-input').forEach(input => {
        input.addEventListener('change', filterMarkersByCategory);
    });


    // Check if the search input element exists
    const searchInput = document.getElementById('locationSearch');
    if (searchInput) {
        // Add event listener for input changes
        searchInput.addEventListener('input', function () {
            const searchValue = this.value.toLowerCase(); // Get the search input
            markers.forEach(marker => {
                const markerName = marker.getPopup().getContent().split('<h5>')[1].split('</h5>')[0].toLowerCase(); // Extract the name from the popup content
                if (markerName.includes(searchValue)) {
                    marker.addTo(map); // Show marker if it matches
                } else {
                    map.removeLayer(marker); // Hide marker if it doesn't match
                }
            });
        });
    }
}

// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});

// Gallery filter functionality
const filterDestinations = (category) => {
    const items = document.querySelectorAll('.destination-item');
    items.forEach(item => {
        if (category === 'all' || item.dataset.category === category) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}


// Search functionality
const searchDestinations = () => {
    const searchInput = document.getElementById('searchInput');
    const filter = searchInput.value.toLowerCase();
    const items = document.querySelectorAll('.destination-item');

    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        if (text.includes(filter)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}


function searchDestinationsHome() {
    const searchValue = document.getElementById('searchInput').value.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, ""); // Normalize and remove accents
    const destinationItems = document.querySelectorAll('.featured-destinations .destination-item'); // Select all destination cards
    let hasResults = false; // Flag to check if there are any results

    destinationItems.forEach(item => {
        const destinationName = item.querySelector('.card-title').textContent.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, ""); // Normalize and remove accents
        if (destinationName.includes(searchValue)) {
            item.style.display = 'block'; // Show item if it matches
            hasResults = true; // Set flag to true if there's a match
        } else {
            item.style.display = 'none'; // Hide item if it doesn't match
        }
    });

    // Show or hide the no results message
    const noResultsMessage = document.getElementById('noResultsMessage');
    if (hasResults) {
        noResultsMessage.style.display = 'none'; // Hide the message if there are results
    } else {
        noResultsMessage.style.display = 'block'; // Show the message if no results
    }
}

// Add event listener for the search input
document.getElementById('searchInput').addEventListener('keypress', function(event) {
    if (event.key === 'Enter') { // Check if the pressed key is Enter
        searchDestinationsHome(); // Call the search function
        scrollToFeaturedDestinations(); // Scroll to the featured destinations
    }
});

function scrollToFeaturedDestinations() {
    const featuredSection = document.querySelector('.featured-destinations');
    if (featuredSection) {
        featuredSection.scrollIntoView({ behavior: 'smooth' }); // Smooth scroll to the featured section
    }
}