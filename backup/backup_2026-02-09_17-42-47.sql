-- Backup Database ecommerce_labib
-- Generated: 2026-02-09 17:42:47 WIB

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `petugas`;
CREATE TABLE `petugas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `jabatan` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `petugas` (`id`,`nama`,`email`,`jabatan`) VALUES ('1','Indah','indah12@gmail.com','Staff');
INSERT INTO `petugas` (`id`,`nama`,`email`,`jabatan`) VALUES ('2','Labib','labib@gmail.com','Digital Marketing Specialist');
INSERT INTO `petugas` (`id`,`nama`,`email`,`jabatan`) VALUES ('3','Andi','andi87@gmail.com','E-Commerce Manager');


DROP TABLE IF EXISTS `produk`;
CREATE TABLE `produk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `harga` int(11) NOT NULL,
  `stok` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `produk` (`id`,`nama`,`harga`,`stok`,`created_at`) VALUES ('1','Lenovo ThinkPad A475','3200000','19','2026-02-08 14:54:57');
INSERT INTO `produk` (`id`,`nama`,`harga`,`stok`,`created_at`) VALUES ('2','Earbuds Bose Ultra ComfortQuiet','4900000','27','2026-02-08 14:55:25');
INSERT INTO `produk` (`id`,`nama`,`harga`,`stok`,`created_at`) VALUES ('3','Camera Canon G7X','5199000','22','2026-02-08 17:11:24');
INSERT INTO `produk` (`id`,`nama`,`harga`,`stok`,`created_at`) VALUES ('4','OPPO A6 Pro 5G','3900000','31','2026-02-09 17:20:30');


DROP TABLE IF EXISTS `transaksi`;
CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_transaksi` varchar(30) NOT NULL,
  `nama_pembeli` varchar(100) NOT NULL,
  `total` int(11) NOT NULL,
  `status` enum('pending','diproses','selesai','batal') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`,`nama`,`email`,`password`,`role`) VALUES ('1','Samsul','samsul@gmail.com','','user');
INSERT INTO `users` (`id`,`nama`,`email`,`password`,`role`) VALUES ('8','Fandi','fandi23@gmail.com','','user');
INSERT INTO `users` (`id`,`nama`,`email`,`password`,`role`) VALUES ('9','Labib','admin@gmail.com','12345','admin');


SET FOREIGN_KEY_CHECKS=1;
