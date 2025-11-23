-- Table des utilisateurs génériques
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL
);

-- Table des clients (Customer)
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des propriétaires (Owner)
CREATE TABLE owners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des parkings (Parking)
CREATE TABLE parkings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location VARCHAR(255) NOT NULL,
    capacity INT NOT NULL,
    owner_id INT NOT NULL,
    FOREIGN KEY (owner_id) REFERENCES owners(id) ON DELETE CASCADE
);

-- Table des réservations (Reservation)
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    day_of_week VARCHAR(20) NOT NULL,
    start_hour VARCHAR(10) NOT NULL,
    end_hour VARCHAR(10) NOT NULL,
    customer_id INT NOT NULL,
    parking_id INT NOT NULL,
    rate_id INT NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (parking_id) REFERENCES parkings(id) ON DELETE CASCADE,
    FOREIGN KEY (rate_id) REFERENCES rates(id) ON DELETE SET NULL
);

-- Table des abonnements (Subscription)
CREATE TABLE subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    rate FLOAT NOT NULL,
    weekly_slots TEXT,
    customer_id INT NOT NULL,
    parking_id INT NOT NULL,
    rate_id INT NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (parking_id) REFERENCES parkings(id) ON DELETE CASCADE,
    FOREIGN KEY (rate_id) REFERENCES rates(id) ON DELETE SET NULL
);

-- Table des stationnements (Stationing)
CREATE TABLE stationings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    status ENUM('available', 'unavailable') NOT NULL,
    customer_id INT NOT NULL,
    parking_id INT NOT NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (parking_id) REFERENCES parkings(id) ON DELETE CASCADE
);

-- Table des tarifs (Rate)
CREATE TABLE rates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM(
        'hourly',
        'daily',
        'weekly_subscription',
        'monthly_subscription',
        'yearly_subscription'
    ) NOT NULL,
    calculation_rule VARCHAR(255) NOT NULL,
    price FLOAT NOT NULL,
    hourly_discount FLOAT NULL,
    duration VARCHAR(50) NULL
);