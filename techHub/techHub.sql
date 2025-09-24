-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 23, 2025 at 03:08 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `phone_laptop_sale`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'MYA', 'admin@gmail.com', '$2y$10$VmvdYhgvT/xD7wdrdtA3k.In5NG1Ya9PqXR0ZUmn2n7xT/EUbWORG', 'admin', '2025-09-19 09:06:52');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`) VALUES
(1, 'Apple'),
(2, 'Samsung'),
(3, 'Sony'),
(4, 'Dell'),
(5, 'ASUS');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `product_id`, `session_id`, `user_id`, `quantity`, `price`, `added_on`) VALUES
(17, 5, 'lrbj1dcqn7hgc2gg5t2rc3g40d', NULL, 1, 2500000.00, '2025-09-20 15:26:30'),
(22, 11, NULL, 2, 1, 239000.00, '2025-09-21 16:07:22');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Electronics'),
(2, 'Gaming Computers'),
(3, 'Smartphones'),
(4, 'Gaming Laptops'),
(5, 'Laptop'),
(6, 'Phone'),
(7, 'Accessories'),
(8, 'Tablet');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `name`, `email`, `subject`, `message`, `submitted_at`) VALUES
(1, 'Nali', 'nili@gmail.com', 'Laptop', 'ADA DFDS ASDA SDFSDG', '2025-09-21 15:15:38');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `shipping_address` text NOT NULL,
  `order_status` varchar(50) NOT NULL DEFAULT 'Pending',
  `payment_status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `shipping_address`, `order_status`, `payment_status`, `created_at`) VALUES
(1, 1, 1299.00, 'Yangon', 'Delivered', 'Paid', '2025-09-19 03:16:36'),
(2, 1, 1299.00, '456 A/B Corner of Dhamazzedi Road and, Link Ln, Yangon 11201, Myanmar (Burma)', 'Pending', 'pending', '2025-09-19 08:19:23'),
(3, 1, 1199.00, '456 A/B Corner of Dhamazzedi Road and, Link Ln, Yangon 11201, Myanmar (Burma)', 'Pending', 'pending', '2025-09-19 08:20:15'),
(4, 1, 1299.00, '456 A/B Corner of Dhamazzedi Road and, Link Ln, Yangon 11201, Myanmar (Burma)', 'Pending', 'pending', '2025-09-19 08:22:22'),
(5, 1, 399.00, 'ဓမ္မစေတီလမ်း နှင့် လင့်လမ်း ထောင့် ၊ ဗဟန်းမြို့နယ်', 'Pending', 'pending', '2025-09-19 08:28:40'),
(6, 1, 2500000.00, 'YAngon', 'Pending', 'pending', '2025-09-19 08:54:32'),
(7, 2, 399.00, 'YAngon,Insein', 'Pending', 'pending', '2025-09-20 15:37:50'),
(8, 2, 1398.00, 'xvxv', 'Pending', 'pending', '2025-09-21 14:42:52');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 3, 1, 1299.00),
(2, 2, 3, 1, 1299.00),
(3, 3, 2, 1, 1199.00),
(4, 4, 3, 1, 1299.00),
(5, 5, 4, 1, 399.00),
(6, 6, 5, 1, 2500000.00),
(7, 7, 4, 1, 399.00),
(8, 8, 1, 1, 999.00),
(9, 8, 4, 1, 399.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `stock_qty` int(11) NOT NULL DEFAULT 0,
  `image_url` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `original_price`, `stock_qty`, `image_url`, `category_id`, `brand_id`, `created_at`) VALUES
(1, 'iPhone 14 Pro', 'The latest iPhone with A16 Bionic chip.', 999.00, 1099.00, 20, 'https://static.wikia.nocookie.net/ipod/images/b/b2/IPhone_14_Pro_Max_colors_2022-09.png/revision/latest?cb=20250803205319', 3, 1, '2025-09-18 08:12:14'),
(2, 'Galaxy S23 Ultra', 'The ultimate Samsung phone with an amazing camera.', 1199.00, NULL, 8, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSWYV04T_eIKr-j-RL9lBo5mpJhd7TDlTaPJA&s', 3, 2, '2025-09-18 08:12:14'),
(3, 'MacBook Air M2', 'A powerful and portable laptop from Apple.', 1299.00, 1399.00, 9, 'https://root-nation.com/wp-content/uploads/2023/08/6efd6ab0-e5d1-11ec-be6c-b2fe04160b9a.cf_.jpg', 2, 1, '2025-09-18 08:12:14'),
(4, 'WH-1000XM5 Headphones', 'Industry-leading noise canceling headphones.', 399.00, 449.00, 7, 'https://www.adorama.com/images/Large/SOWH1XM5BAK.JPG', 1, 3, '2025-09-18 08:12:14'),
(5, 'Apple MacBook Air', 'RAM	\r\n16 GB\r\n\r\nresolution	\r\n2560×1600\r\n\r\nOperating system	\r\nMacOS Sonoma\r\n\r\nDisplay size	\r\n13.3\"\r\n\r\nbrand	\r\nApple\r\n\r\nprocessor	\r\nApple M1\r\n\r\nCondition	\r\nRefurbished\r\n\r\nHard drive capacity	\r\n250 GB', 2500000.00, 2300000.00, 4, 'https://pclaptop.de/wp-content/smush-webp/2025/01/Vorne-21.png.webp', 2, 1, '2025-09-18 15:37:30'),
(6, 'ASUS Vivobook', 'Key Features\r\nIntel Core 5 120U (0.9GHz) Processor\r\n16GB DDR4-3200 RAM\r\nIntel Graphics Integrated Graphics\r\n1TB PCIe Gen4 SSD\r\n15.6\" Full HD IPS Display\r\n1x1 Wireless LAN WiFi 6 (802.11ax), Bluetooth 5.2\r\n3.75 lbs. (1.70 kg)\r\nWindows 11 Home', 5290000.00, 5090000.00, 10, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSwsQ9Q2UIkR6yRsXDNICr1AeePFKmBwU-0vw&s', 2, 5, '2025-09-21 15:33:16'),
(7, 'Samsung - Galaxy S25 Ultra 256GB', 'Screen Size\r\n6.9 inches\r\nFront-Facing Camera\r\n12 megapixels\r\nRear-Facing Camera\r\n200 megapixels\r\nTelephoto Camera 1\r\n10 megapixels\r\nUltrawide Camera\r\n50 megapixels', 1299000.00, 1099000.00, 20, 'https://productimages.microcenter.com/689396_789099_01_front_zoom.jpg', 3, 2, '2025-09-21 15:38:25'),
(8, 'Apple - 13-inch iPad Pro M4', 'Shop now\r\nShop now\r\nQuick Look\r\n11”\"inches\"\r\nUltra Retina XDR display*Refer to legal disclaimers\r\nProMotion technology\r\nP3 wide color\r\nTrue Tone\r\nAntireflective coating\r\nNano-texture display glass option on 1TB and 2TB models*', 1299000.00, 1099000.00, 0, 'https://mdriveasia.com/cdn/shop/files/ROSA_iPad_Pro_13_M4_WiFi_Space_Black_PDP_Image_Position_1b_1024x1024.jpg?v=1719290462', 8, 1, '2025-09-21 15:44:23'),
(9, 'HP - Smart Tank 5101 Wireless AI-Enabled AiO Supertank Inkjet Printer', 'Key Features\r\nMulti-Function, Print, Scan, Copy\r\n1200 x 1200 Print, 1200 x 1200 Scan, 600 x 600 Copy Resolution\r\n12 ppm Black & White, 5 ppm Color Print Speed\r\nWireless, Mobile, Ethernet, USB Connections', 2500000.00, 2000000.00, 6, 'https://i.ebayimg.com/images/g/p~AAAOSw7~5oKl51/s-l500.jpg', 7, 4, '2025-09-21 15:50:44'),
(10, 'Apple AirPods 4 True Wireless Bluetooth Earbuds - White', 'Key Features\r\nIn Ear Earbuds\r\nBluetooth Wireless\r\nWater Resistant, IP54\r\nWhite', 129000.00, 109000.00, 20, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR0TUWbHGEHNLQqL-s73-jTMxrq4DwDX43liQ&s', 7, 1, '2025-09-21 15:52:23'),
(11, 'AOC CU34G4 34\" 2K WQHD (3440 x 1440) 180Hz Curved Screen UltraWide Gaming Monitor', 'Key Features\r\n180 Hz Refresh Rate, 0.5ms Response Time, VA Panel\r\nAMD FreeSync Premium\r\nHDMI 2.0; DisplayPort 1.4\r\n100 x 100 mm (3.9 x 3.9 in) VESA Mount\r\nHeight; Tilt; Swivel', 239000.00, 209000.00, 30, 'https://mmd-aoc2.oss-cn-hongkong.aliyuncs.com/Products/Monitors/G%20Line/G4/CU34G4/CU34G4_F.png', 7, 5, '2025-09-21 15:54:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `otp_hash` varchar(255) DEFAULT NULL,
  `otp_expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `otp_hash`, `otp_expires_at`, `created_at`) VALUES
(1, 'Mike Pirath Nitipaisankul', 'mike@gmail.com', '$2y$10$pJ6WzDRTzAN5.eJle40lH.ml3mk9mGfx6hdBuNDBP35icZ6ZfG8HS', NULL, NULL, '2025-09-19 03:02:10'),
(2, 'Mike Pirath', 'Pirath@gmail.com', '$2y$10$RZcf6zem0fKrEDd94/9Xde1ILUR3LsFUPNS5gykq.N6EREo22MxUq', NULL, NULL, '2025-09-20 15:33:06'),
(3, 'User 9', 'user9@gmail.com', '$2y$10$pJvIpQLaY0aYsU5L7WueL.ZjZcKmol7FxiQjQZ1rRN3MuDj2a.LHm', NULL, NULL, '2025-09-20 15:33:49'),
(4, 'User 9', 'user92@gmail.com', '$2y$10$zkoECcHmJ0nqGKnEWoFqn.OIKHiCp/UuvT.hic2PYCpvYIi4.pb.a', NULL, NULL, '2025-09-20 15:34:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `brand_id` (`brand_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
