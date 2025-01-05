# Viva Porto

**Viva Porto** is a web application designed to highlight the charm and culture of the beautiful city of Porto, Portugal. It serves as an interactive platform for users to explore attractions, cultural landmarks, and events. With a sleek design and dynamic features, the application provides a seamless experience for both tourists and locals.

## Table of Contents

- [Features](#features)
- [Technologies Used](#technologies-used)
- [Installation](#installation)
- [Database Structure](#database-structure)
- [API Endpoints](#api-endpoints)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)

## Features

- **Explore Attractions**:

  - Users can browse various attractions categorized by type, such as cultural, historical, natural, and religious.
  - Each attraction features detailed information, including descriptions, images, and locations.

- **Dynamic Messaging System**:

  - A contact form allows users to send inquiries or questions directly to the site administrators.
  - Messages are stored in the database and can be managed through an admin dashboard.

- **Interactive Map Integration**:

  - Using **Leaflet.js**, the site displays an interactive map highlighting tourist destinations.
  - Custom markers and pop-ups provide easy navigation and quick information.

- **Responsive Design**:

  - Built with **Bootstrap 5**, the application ensures an optimal experience across desktop, tablet, and mobile devices.

- **Admin Dashboard**:

  - Accessible by authenticated admin users.
  - Enables efficient management of:
    - Messages received from the contact form.
    - Tourist destinations, including adding, editing, and removing entries.
    - Website banners and promotional content.

- **Search and Filters**:
  - Advanced filtering options allow users to search for destinations by category, location, or keyword.

## Technologies Used

- **Frontend**:

  - HTML, CSS, JavaScript
  - **Bootstrap 5** for styling and responsiveness
  - **FontAwesome** or **Bootstrap Icons** for intuitive visuals

- **Backend**:

  - **PHP** (Object-Oriented Programming) for server-side functionality

- **Database**:

  - **MySQL/MariaDB** to store user inquiries, attractions, and administrative data

- **Mapping**:

  - **Leaflet.js** for dynamic and customizable maps

- **Server**:
  - **Apache** (XAMPP recommended for local development)

## Installation

1. **Clone the Repository**:
   Clone the project to your local machine using the following command:
   ```bash
   git clone https://github.com/adelino-masioli/viva-porto.git
   ```
2. **Set Up the Server Environment:**:

- Install XAMPP (or another LAMP/WAMP stack) to create a local server.
- Place the project files in the htdocs directory (or equivalent).

3. **Configure the Database:**:

- Import the provided SQL file (viva_porto.sql) into your MySQL server using phpMyAdmin or the MySQL CLI.
- Update the database connection details in the configuration file (config.php).

4. **Start the Server:**:

- Open the XAMPP Control Panel and start Apache and MySQL services.
- Access the project in your browser at http://localhost/viva-porto.

## Database Structure

The database is structured to handle dynamic content and user interactions efficiently:

### Tables:

1. **destinations**

   - `id`: INT, Primary Key
   - `name`: VARCHAR(100)
   - `description`: TEXT
   - `short_description`: TEXT
   - `category`: ENUM ('Cultural', 'Historical', 'Religious', etc.)
   - `image_url`: VARCHAR(255)
   - `latitude`: FLOAT
   - `longitude`: FLOAT
   - `is_featured`: BOOLEAN
   - `is_active`: BOOLEAN

2. **messages**

   - `id`: INT, Primary Key
   - `name`: VARCHAR(100)
   - `email`: VARCHAR(100)
   - `subject`: VARCHAR(255)
   - `message`: TEXT
   - `is_read`: BOOLEAN
   - `created_at`: TIMESTAMP

3. **banners**

   - `id`: INT, Primary Key
   - `image_url`: VARCHAR(255)
   - `title`: VARCHAR(100)
   - `created_at`: TIMESTAMP
   - `updated_at`: TIMESTAMP

4. **visitors**

   - `id`: INT, Primary Key
   - `visit_time`: TIMESTAMP

5. **users**
   - `id`: INT, Primary Key
   - `username`: VARCHAR(50) NOT NULL UNIQUE
   - `password`: VARCHAR(255) NOT NULL
   - `email`: VARCHAR(100) NOT NULL UNIQUE
   - `is_admin`: BOOLEAN DEFAULT FALSE
   - `created_at`: TIMESTAMP DEFAULT CURRENT_TIMESTAMP

## API Endpoints

The project includes internal PHP scripts for dynamic functionality:

Messages API:

POST /api/messages: Submits a new message to the database.
Attractions API:

GET /api/attractions: Retrieves a list of tourist attractions with optional filters (e.g., category, region).
POST /api/attractions: Adds a new attraction (admin only).
DELETE /api/attractions: Removes an attraction (admin only).

## Usage

As a Visitor:

Explore attractions by browsing the gallery or using the map for location-based navigation.
Use the contact form to send inquiries or suggestions to the website administrators.
As an Admin:

Log in to the admin dashboard to manage messages, attractions, and promotional banners.
Ensure the site stays updated with accurate and engaging content.

## Contributing

Contributions are welcome! To get started:

1. **Fork the repository.**
2. **Create a new branch:**

```bash
git checkout -b feature-name
```

3. **Commit your changes:**

```bash
git commit -m "Description of the feature"
```

4. **Push to the branch:**

```bash
git push origin feature-name
```

5. **Create a pull request.**

License
This project is licensed under the MIT License. Feel free to use, modify, and distribute as needed.
