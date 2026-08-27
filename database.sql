

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `nomor_id` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kontak` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `admin` (`id`, `nomor_id`, `password`, `nama`, `kontak`, `email`) VALUES
(1, 'kelompokganteng', 'kg2026', 'Super Admin', '081234567890', 'admin@kelompokganteng.com'),
(2, 'admin', 'admin123', 'Admin Biasa', '089876543210', 'admin@sekolah.com');


CREATE TABLE `inventory` (
  `id_barang` int(11) NOT NULL,
  `serial_number` varchar(50) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `jenis_barang` varchar(50) NOT NULL,
  `kuantitas_stok` int(11) NOT NULL,
  `harga` int(11) NOT NULL,
  `id_gudang` int(11) DEFAULT NULL,
  `id_vendor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id_barang`, `serial_number`, `nama_barang`, `jenis_barang`, `kuantitas_stok`, `harga`, `id_gudang`, `id_vendor`) VALUES
(1, 'SN-2026-001', 'Laptop Asus ROG', 'Elektronik', 15, 15000000, 1, 1),
(2, 'SN-2026-002', 'Printer Epson L3210', 'Hardware', 0, 2500000, 2, 2);


CREATE TABLE `storage_unit` (
  `id_gudang` int(11) NOT NULL,
  `nama_gudang` varchar(100) NOT NULL,
  `lokasi` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `storage_unit`
--

INSERT INTO `storage_unit` (`id_gudang`, `nama_gudang`, `lokasi`) VALUES
(1, 'Gudang Utama', 'Surabaya Pusat'),
(2, 'Gudang Cabang', 'Sidoarjo Barat'),
(3, 'Gudang garam sidoarjo', 'jln sidoarjo gg 13');

-- --------------------------------------------------------

--
-- Table structure for table `vendor`
--

CREATE TABLE `vendor` (
  `id_vendor` int(11) NOT NULL,
  `nama_vendor` varchar(100) NOT NULL,
  `kontak` varchar(20) NOT NULL,
  `nama_barang_supply` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor`
--

INSERT INTO `vendor` (`id_vendor`, `nama_vendor`, `kontak`, `nama_barang_supply`) VALUES
(1, 'PT Asus Indonesia', '021-999888', 'Laptop & PC'),
(2, 'Epson Master', '021-777666', 'Printer & Tinta'),
(3, 'PT gudang garam', '08980513003', 'Tembakau russia');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id_barang`),
  ADD KEY `id_gudang` (`id_gudang`),
  ADD KEY `id_vendor` (`id_vendor`);

--
-- Indexes for table `storage_unit`
--
ALTER TABLE `storage_unit`
  ADD PRIMARY KEY (`id_gudang`);

--
-- Indexes for table `vendor`
--
ALTER TABLE `vendor`
  ADD PRIMARY KEY (`id_vendor`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `storage_unit`
--
ALTER TABLE `storage_unit`
  MODIFY `id_gudang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vendor`
--
ALTER TABLE `vendor`
  MODIFY `id_vendor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`id_gudang`) REFERENCES `storage_unit` (`id_gudang`),
  ADD CONSTRAINT `inventory_ibfk_2` FOREIGN KEY (`id_vendor`) REFERENCES `vendor` (`id_vendor`);
COMMIT;
