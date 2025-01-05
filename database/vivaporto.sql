-- Create database if not exists
CREATE DATABASE IF NOT EXISTS vivaporto;
USE vivaporto;

-- Destinations table
CREATE TABLE IF NOT EXISTS destinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    short_description VARCHAR(255),
    category VARCHAR(50),
    image_url VARCHAR(255),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    is_featured BOOLEAN DEFAULT FALSE,
    is_highlighted BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Users table for admin access
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Contact messages table
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Visitors table to track site visits
CREATE TABLE IF NOT EXISTS visitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Banners table for carousel management
CREATE TABLE IF NOT EXISTS banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_url VARCHAR(255) NOT NULL,
    title VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
-- Password hash generated with PASSWORD_DEFAULT algorithm
INSERT INTO users (username, password, email, is_admin) VALUES
('admin', '$2y$10$XVHTsfUc5jU2dJfpH0MlU.CxAfDsxT3Bp9jugYgbTdIvkJiSSG.HG', 'admin@vivaporto.com', 1);

-- Insert sample destinations
INSERT INTO destinations (name, description, short_description, category, image_url, latitude, longitude, is_featured, is_highlighted) VALUES
('Livraria Lello', 
 'Uma das livrarias mais bonitas do mundo, famosa por sua arquitetura neogótica e escadaria impressionante.',
 'Arquitetura incrível e história rica.',
 'Attractions',
 '/uploads/destinations/placeholder-image.webp',
 41.146945, -8.614994,
 TRUE, TRUE),

('Ponte Dom Luís I', 
 'Ícone arquitetônico que conecta Porto a Vila Nova de Gaia, oferecendo vistas espetaculares do rio Douro.',
 'Ponte famosa no Porto.',
 'Restaurants',
 '/uploads/destinations/placeholder-image.webp',
 41.140238, -8.611104,
 TRUE, FALSE),

('Ribeira', 
 'Área histórica junto ao rio Douro com ruas estreitas, casas coloridas e atmosfera vibrante.',
 'Local histórico no Porto.',
 'Hotels',
 '/uploads/destinations/placeholder-image.webp',
 41.140366, -8.611040,
 TRUE, TRUE),

('Torre dos Clérigos', 
 'Torre barroca que oferece vistas panorâmicas da cidade após subir 225 degraus.',
 'Vista panorâmica espetacular.',
 'Museums',
 '/uploads/destinations/placeholder-image.webp',
 41.145824, -8.614607,
 TRUE, FALSE),

('Casa da Música', 
 'Sala de concertos contemporânea reconhecida por sua arquitetura única e programação diversificada.',
 'Cultura e música no Porto.',
 'Parks',
 '/uploads/destinations/placeholder-image.webp',
 41.157883, -8.629115,
 FALSE, TRUE),

('Palácio da Bolsa', 
 'Edifício histórico com interiores luxuosos, incluindo o famoso Salão Árabe.',
 'Patrimônio cultural.',
 'Shopping',
 '/uploads/destinations/placeholder-image.webp',
 41.141156, -8.615318,
 TRUE, FALSE),

('Estação de São Bento', 
 'Estação de trem famosa por seus painéis de azulejos que retratam a história de Portugal.',
 'Azulejos e história.',
 'Nightlife',
 '/uploads/destinations/placeholder-image.webp',
 41.145273, -8.610993,
 FALSE, TRUE),

('Jardins do Palácio de Cristal', 
 'Jardins românticos com vistas deslumbrantes do Douro e esculturas impressionantes.',
 'Natureza no Porto.',
 'Nightlife',
 '/uploads/destinations/placeholder-image.webp',
 41.148812, -8.623392,
 TRUE, FALSE),

('Sé do Porto', 
 'Catedral histórica com vista para a cidade e rica em arquitetura e história.',
 'Catedral e história.',
 'Religious',
 '/uploads/destinations/placeholder-image.webp',
 41.143267, -8.611102,
 FALSE, TRUE),

('Mosteiro da Serra do Pilar', 
 'Mosteiro com vista impressionante do Porto e do rio Douro.',
 'Vistas deslumbrantes.',
 'Religious',
 '/uploads/destinations/placeholder-image.webp',
 41.133919, -8.609814,
 TRUE, FALSE);

-- Optionally, insert an initial record for testing
INSERT INTO visitors (visit_time) VALUES (CURRENT_TIMESTAMP);


INSERT INTO banners (image_url, title) VALUES
('/uploads/destinations/placeholder-image.webp', 'Welcome to Porto'),
('/uploads/destinations/placeholder-image.webp', 'Discover the Douro Valley'),
('/uploads/destinations/placeholder-image.webp', 'Explore Porto\'s Historic Center'),
('/uploads/destinations/placeholder-image.webp', 'Experience Porto\'s Nightlife'),
('/uploads/destinations/placeholder-image.webp', 'Visit the Iconic Ribeira District');


INSERT INTO messages (name, email, subject, message, is_read) VALUES
('John Doe', 'john.doe@example.com', 'Inquiry about Porto', 'I would like to know more about the attractions in Porto.', FALSE),
('Jane Smith', 'jane.smith@example.com', 'Booking Request', 'Can I book a tour for next week?', FALSE),
('Carlos Silva', 'carlos.silva@example.com', 'Feedback', 'Loved my visit! The food was amazing.', TRUE),
('Anna Johnson', 'anna.johnson@example.com', 'Question about transportation', 'What is the best way to get around Porto?', FALSE),
('Michael Brown', 'michael.brown@example.com', 'Lost Item', 'I lost my wallet at the hotel, can you help?', FALSE),
('Emily Davis', 'emily.davis@example.com', 'Event Inquiry', 'Are there any events happening this weekend?', FALSE),
('David Wilson', 'david.wilson@example.com', 'Restaurant Recommendation', 'Could you recommend a good restaurant in Porto?', FALSE),
('Sarah Taylor', 'sarah.taylor@example.com', 'Cultural Activities', 'What cultural activities do you recommend in Porto?', TRUE),
('James Anderson', 'james.anderson@example.com', 'Travel Tips', 'Any tips for traveling in Porto?', FALSE),
('Laura Martinez', 'laura.martinez@example.com', 'General Inquiry', 'I have some general questions about my upcoming trip.', FALSE);