CREATE DATABASE tes_spp;
USE tes_spp;

CREATE TABLE user (
    id INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'siswa') NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uni_username (username)
);

CREATE TABLE siswa (
    id INT NOT NULL AUTO_INCREMENT,
    nisn VARCHAR(12) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    tanggal_lahir DATE NOT NULL,
    tempat_lahir VARCHAR(50) NOT NULL,
    kelas VARCHAR(20) NOT NULL,
    semester ENUM('ganjil', 'genap') NOT NULL,
    nominal_spp DECIMAL(10, 2) NOT NULL,
    status_pembayaran ENUM('lunas', 'belum_lunas') NOT NULL,
    created_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

CREATE TABLE pembayaraan (
    id INT NOT NULL AUTO_INCREMENT,
    siswa_id INT NOT NULL,
    tanggal_bayar DATE NOT NULL,
    jumlah_bayar DECIMAL(15, 2) NOT NULL,
    keterangan VARCHAR(255) NULL,
    PRIMARY KEY (id)
);

ALTER TABLE pembayaraan
ADD CONSTRAINT fk_pembayaraan_ke_siswa
FOREIGN KEY (siswa_id) REFERENCES siswa(id)
ON DELETE RESTRICT ON UPDATE CASCADE;

INSERT INTO user (username, password, role) VALUES
('admin', SHA2('admin123', 256), 'admin'),
('siswa1', SHA2('siswa123', 256), 'siswa');

select * from user;

INSERT INTO siswa 
(nisn, nama, tanggal_lahir, tempat_lahir, kelas, semester, nominal_spp, status_pembayaran)
VALUES 
('1109', 'Siswa Satu', '2008-05-15', 'Jakarta', 'X IPA 1', 'ganjil', 250000, 'lunas');

INSERT INTO user (username, password, role)
VALUES ('1109', SHA2('siswa123', 256), 'siswa');