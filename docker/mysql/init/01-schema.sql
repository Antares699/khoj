-- Khoj Database Schema
-- Auto-imported by Docker on first run

DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS businesses;
DROP TABLE IF EXISTS business_owners;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE business_owners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    job_title VARCHAR(100),
    phone VARCHAR(20),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE businesses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT NOT NULL,
    osm_id BIGINT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    category VARCHAR(50) NOT NULL,
    description TEXT,
    location VARCHAR(100),
    lat DECIMAL(10,8) DEFAULT NULL,
    lon DECIMAL(11,8) DEFAULT NULL,
    phone VARCHAR(20),
    website VARCHAR(255),
    attributes JSON,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES business_owners(id) ON DELETE CASCADE,
    UNIQUE KEY unique_osm_id (osm_id)
);

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    business_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
);

CREATE INDEX idx_category ON businesses(category);
CREATE INDEX idx_location ON businesses(location);
