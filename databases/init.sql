CREATE DATABASE silost_db;
USE silost_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('user', 'admin') DEFAULT 'user'
);

CREATE TABLE barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100),
    deskripsi TEXT,
    lokasi VARCHAR(255),
    tanggal_hilang DATE,
    foto VARCHAR(255),
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE chat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pengirim_id INT,
    penerima_id INT,
    pesan TEXT,
    tanggal_kirim TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pengirim_id) REFERENCES users(id),
    FOREIGN KEY (penerima_id) REFERENCES users(id)
);
