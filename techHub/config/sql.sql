- Table structure for table `brands`
CREATE TABLE `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table `brands`
INSERT INTO `brands` (`id`, `name`) VALUES
(1, 'Apple'),
(2, 'Samsung'),
(3, 'Sony');

-- Table structure for table `categories`
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table `categories`
INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Electronics'),
(2, 'Computers'),
(3, 'Smartphones');

-- Table structure for table `products`
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `image_url` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `brand_id` (`brand_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table `products`
INSERT INTO `products` (`id`, `name`, `description`, `price`, `original_price`, `image_url`, `category_id`, `brand_id`) VALUES
(1, 'iPhone 14 Pro', 'The latest iPhone with A16 Bionic chip.', '999.00', '1099.00', 'https://placehold.co/600x400/d3c4b3/ffffff?text=iPhone+14', 3, 1),
(2, 'Galaxy S23 Ultra', 'The ultimate Samsung phone with an amazing camera.', '1199.00', NULL, 'https://placehold.co/600x400/a89078/ffffff?text=Galaxy+S23', 3, 2),
(3, 'MacBook Air M2', 'A powerful and portable laptop from Apple.', '1299.00', '1399.00', 'https://placehold.co/600x400/c3b8ad/ffffff?text=MacBook+Air', 2, 1),
(4, 'WH-1000XM5 Headphones', 'Industry-leading noise canceling headphones.', '399.00', '449.00', 'https://placehold.co/600x400/50463f/ffffff?text=Sony+XM5', 1, 3);

