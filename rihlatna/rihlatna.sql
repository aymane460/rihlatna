-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 23, 2025 at 10:05 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rihlatna`
--

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `reservation_id` int(11) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `birthday` date NOT NULL,
  `cin` varchar(20) NOT NULL,
  `city` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `first_experience` tinyint(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `reservation_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`reservation_id`, `gender`, `first_name`, `last_name`, `birthday`, `cin`, `city`, `phone`, `email`, `first_experience`, `user_id`, `trip_id`, `status`, `reservation_date`) VALUES
(1, 'male', 'aymane', 'elafia', '2025-06-21', 'k45325', 'tanger', '212769865432', 'aymaneelafia.solicode@gmail.com', 1, 7, 22, 'confirmed', '2025-06-21 18:29:50'),
(2, 'male', 'aymane', 'el', '2025-06-16', 'AB945325', 'tanger', '0676432190', 'aymaneelafia.solicode@gmail.com', 1, 7, 10, 'pending', '2025-06-22 15:51:27');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `rating_value` int(11) NOT NULL,
  `comment` text NOT NULL,
  `review_date` date DEFAULT curdate(),
  `user_id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `rating_value`, `comment`, `review_date`, `user_id`, `trip_id`) VALUES
(5, 5, 'hhhhhhhhhhhhhhhhhhhhhhhh', '2025-06-22', 7, 22),
(6, 4, 'jjjjjjjjjjjjjjjjjjjjjjjjjjjjjj', '2025-06-22', 20, 22),
(7, 3, 'good', '2025-06-22', 20, 9),
(8, 4, 'good', '2025-06-22', 7, 10);

-- --------------------------------------------------------

--
-- Table structure for table `trips`
--

CREATE TABLE `trips` (
  `trip_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `hiking_level` enum('beginner','advanced') NOT NULL,
  `location` varchar(250) NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `trip_category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trips`
--

INSERT INTO `trips` (`trip_id`, `title`, `price`, `start_date`, `end_date`, `hiking_level`, `location`, `image_url`, `trip_category_id`) VALUES
(9, 'Essaouira Seaside Escape', 1500.00, '2025-06-29', '2025-07-01', 'beginner', 'Essaouira', '/trips/68543d7cf27c3.jpeg', 1),
(10, 'Agadir Beach Adventure', 1800.00, '2025-06-30', '2025-07-02', 'beginner', 'Agadir', '/trips/685443c770072.jpeg', 1),
(11, 'Al Hoceima Coastal Chill', 1300.00, '2025-07-04', '2025-07-06', 'beginner', 'Al Hoceima', '/trips/685445e031a97.jpeg', 1),
(19, 'Ouzoud Waterfalls Nature Break', 1400.00, '2025-06-29', '2025-07-01', 'beginner', 'Ouzoud Waterfalls', '/trips/6855575b03e5f.jpeg', 3),
(20, 'Paradise Valley Eco Retreat', 1350.00, '2025-06-30', '2025-07-02', 'beginner', 'Paradise Valley', '/trips/685559cfbf0e7.jpeg', 3),
(21, 'Dakhla Lagoon Adventure', 2300.00, '2025-07-04', '2025-07-06', 'beginner', 'Dakhla Lagoon', '/trips/68555c1db9eaa.jpeg', 3),
(22, 'Toubkal Trekking Challenge (Imlil)', 1600.00, '2025-06-29', '2025-07-01', 'advanced', 'Imlil & Toubkal National Park', '/trips/68555f590e667.jpeg', 2),
(23, 'Chefchaouen Blue Mountain Getaway', 1100.00, '2025-06-30', '2025-07-02', 'advanced', 'Chefchaouen (Rif Mountains)', '/trips/6855616d5b10b.jpeg', 2),
(24, 'Imilchil Highlands Discovery', 1550.00, '2025-07-04', '2025-07-06', 'advanced', 'Imilchil (High Atlas)', '/trips/6855639998b3e.jpeg', 2);

-- --------------------------------------------------------

--
-- Table structure for table `trips_categories`
--

CREATE TABLE `trips_categories` (
  `trip_category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trips_categories`
--

INSERT INTO `trips_categories` (`trip_category_id`, `name`) VALUES
(1, 'beach'),
(2, 'mountain'),
(3, 'nature');

-- --------------------------------------------------------

--
-- Table structure for table `trip_activities`
--

CREATE TABLE `trip_activities` (
  `activity_id` int(11) NOT NULL,
  `day_id` int(11) NOT NULL,
  `activity_order` int(11) NOT NULL,
  `activity_content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_activities`
--

INSERT INTO `trip_activities` (`activity_id`, `day_id`, `activity_order`, `activity_content`) VALUES
(9, 7, 1, '07:00 – Departure from Tangier'),
(10, 7, 2, '12:00 – Arrival in Essaouira & hotel check-in'),
(11, 7, 3, '13:30 – Lunch at a seaside restaurant'),
(12, 7, 4, '15:00 – Guided Medina tour'),
(13, 7, 5, '17:30 – Walk on the beach & sunset'),
(14, 7, 6, '20:00 – Dinner & free time'),
(15, 8, 1, '09:00 – Breakfast'),
(16, 8, 2, '10:00 – Camel ride on the beach'),
(17, 8, 3, '12:00 – Visit to the port & fish market'),
(18, 8, 4, '14:00 – Lunch'),
(19, 8, 5, '16:00 – Visit local art galleries'),
(20, 8, 6, '19:00 – Dinner with Gnawa music show'),
(21, 9, 1, '09:00 – Breakfast'),
(22, 9, 2, '10:00 – Free time for shopping'),
(23, 9, 3, '12:00 – Check-out'),
(24, 9, 4, '13:00 – Return to Tangier'),
(25, 9, 5, '20:00 – Arrival in Tangier'),
(26, 10, 1, '06:30 – Departure from Tangier'),
(27, 10, 2, '13:00 – Arrival & hotel check-in'),
(28, 10, 3, '14:00 – Lunch'),
(29, 10, 4, '16:00 – Beach walk & marina visit'),
(30, 10, 5, '18:30 – Sunset viewpoint (Agadir Oufella)'),
(31, 10, 6, '20:00 – Dinner'),
(32, 11, 1, '08:00 – Breakfast'),
(33, 11, 2, '09:00 – Quad biking along the beach'),
(34, 11, 3, '11:30 – Souk El Had market tour'),
(35, 11, 4, '13:00 – Lunch'),
(36, 11, 5, '15:00 – Cable car ride'),
(37, 11, 6, '18:00 – Free beach time'),
(38, 11, 7, '20:00 – Seafood dinner'),
(39, 12, 1, '08:30 – Breakfast'),
(40, 12, 2, '10:00 – Check-out & visit to CrocoPark'),
(41, 12, 3, '12:30 – Departure'),
(42, 12, 4, '20:00 – Arrival in Tangier'),
(43, 13, 1, '08:00 – Departure from Tangier'),
(44, 13, 2, '13:00 – Arrival & check-in'),
(45, 13, 3, '14:00 – Lunch with sea view'),
(46, 13, 4, '16:00 – Sfiha beach relaxation'),
(47, 13, 5, '18:30 – Sunset viewpoint (Quemado Beach)'),
(48, 13, 6, '20:00 – Dinner'),
(49, 14, 1, '09:00 – Breakfast'),
(50, 14, 2, '10:00 – Boat trip along the coast'),
(51, 14, 3, '13:00 – Picnic lunch on the beach'),
(52, 14, 4, '15:00 – Snorkeling/free time'),
(53, 14, 5, '18:00 – Cultural walk in the Medina'),
(54, 14, 6, '20:00 – Dinner & local music night'),
(55, 15, 1, '08:30 – Breakfast'),
(56, 15, 2, '10:00 – Check-out'),
(57, 15, 3, '11:00 – Visit to National Park'),
(58, 15, 4, '13:00 – Return to Tangier'),
(59, 15, 5, '18:00 – Arrival in Tangier'),
(81, 25, 1, '07:00 – Departure from Tangier'),
(82, 25, 2, '13:00 – Lunch stop'),
(83, 25, 3, '16:00 – Arrival at Ouzoud & hotel check-in'),
(84, 25, 4, '17:00 – Short hike to the falls'),
(85, 25, 5, '19:30 – Dinner'),
(86, 26, 1, '08:00 – Breakfast'),
(87, 26, 2, '09:00 – Guided trek around the waterfalls'),
(88, 26, 3, '12:00 – Picnic near the base'),
(89, 26, 4, '15:00 – Monkey observation & river swim'),
(90, 26, 5, '18:00 – Sunset from viewpoint'),
(91, 26, 6, '20:00 – Campfire dinner'),
(92, 27, 1, '08:30 – Breakfast'),
(93, 27, 2, '10:00 – Local Berber village visit'),
(94, 27, 3, '12:00 – Departure'),
(95, 27, 4, '19:00 – Return to Tangier'),
(96, 28, 1, '07:00 – Departure from Tangier'),
(97, 28, 2, '14:00 – Arrival & lodge check-in'),
(98, 28, 3, '15:00 – Explore oasis trail'),
(99, 28, 4, '18:00 – Sunset yoga'),
(100, 28, 5, '20:00 – Organic dinner'),
(101, 29, 1, '08:00 – Breakfast'),
(102, 29, 2, '09:00 – Hike to natural pools'),
(103, 29, 3, '11:00 – Swim & relaxation'),
(104, 29, 4, '13:00 – Lunch by the waterfall'),
(105, 29, 5, 'Rock climbing intro (optional)'),
(106, 29, 6, '18:00 – Free time'),
(107, 29, 7, '20:00 – Berber-style dinner'),
(108, 30, 1, '09:00 – Breakfast'),
(109, 30, 2, '10:00 – Meditation walk'),
(110, 30, 3, '12:00 – Departure'),
(111, 30, 4, '20:00 – Return to Tangier'),
(112, 31, 1, '06:00 – Flight from Tangier to Dakhla'),
(113, 31, 2, '10:00 – Arrival & transfer to eco-lodge'),
(114, 31, 3, '12:00 – Welcome lunch'),
(115, 31, 4, '14:00 – Kayaking in the lagoon'),
(116, 31, 5, '17:00 – Sunset walk on the beach'),
(117, 31, 6, '19:30 – Dinner'),
(118, 32, 1, '08:00 – Breakfast'),
(119, 32, 2, '09:00 – Kite surfing (lesson or free)'),
(120, 32, 3, '12:30 – Lunch'),
(121, 32, 4, '14:00 – 4x4 tour to White Dune'),
(122, 32, 5, '18:00 – Spa and hammam'),
(123, 32, 6, '20:00 – Seafood dinner'),
(124, 33, 1, '08:30 – Breakfast'),
(125, 33, 2, '10:00 – Check-out'),
(126, 33, 3, '12:00 – Flight back to Tangier'),
(127, 33, 4, '16:00 – Arrival in Tangier'),
(128, 34, 1, '06:30 – Departure from Tangier'),
(129, 34, 2, '14:00 – Arrival in Imlil & guesthouse check-in'),
(130, 34, 3, '15:30 – Short acclimatization hike'),
(131, 34, 4, '19:00 – Dinner & briefing'),
(132, 35, 1, '07:00 – Breakfast'),
(133, 35, 2, '08:00 – Full-day trek to Toubkal base camp'),
(134, 35, 3, '13:00 – Picnic on the trail'),
(135, 35, 4, '16:00 – Return to Imlil'),
(136, 35, 5, '20:00 – Dinner with local music'),
(137, 36, 1, '08:00 – Breakfast'),
(138, 36, 2, '09:00 – Visit to Berber market'),
(139, 36, 3, '12:00 – Return to Tangier'),
(140, 36, 4, '20:00 – Arrival'),
(141, 37, 1, '08:00 – Departure from Tangier'),
(142, 37, 2, '11:00 – Arrival & check-in'),
(143, 37, 3, '12:30 – Lunch in a traditional restaurant'),
(144, 37, 4, '14:00 – Guided Medina tour'),
(145, 37, 5, '17:00 – Ras El Maa spring visit'),
(146, 37, 6, '20:00 – Dinner'),
(147, 38, 1, '08:30 – Breakfast'),
(148, 38, 2, '10:00 – Hike to Spanish Mosque viewpoint'),
(149, 38, 3, '13:00 – Picnic lunch'),
(150, 38, 4, '15:00 – Free time for photography'),
(151, 38, 5, '18:00 – Relax in a local café'),
(152, 38, 6, '20:00 – Dinner'),
(153, 39, 1, '09:00 – Breakfast'),
(154, 39, 2, '10:00 – Local market visit'),
(155, 39, 3, '12:00 – Return to Tangier'),
(156, 39, 4, '15:00 – Arrival'),
(157, 40, 1, '07:00 – Departure from Tangier'),
(158, 40, 2, '14:00 – Arrival in Imilchil & hotel check-in'),
(159, 40, 3, '15:30 – Visit Isli and Tislit Lakes'),
(160, 40, 4, '19:30 – Dinner'),
(161, 41, 1, '08:00 – Breakfast'),
(162, 41, 2, '09:00 – Village cultural tour'),
(163, 41, 3, '11:00 – Traditional weaving workshop'),
(164, 41, 4, '13:00 – Lunch with local family'),
(165, 41, 5, '15:00 – Mountain trail walk'),
(166, 41, 6, '18:00 – Folklore evening'),
(167, 41, 7, '20:00 – Dinner'),
(168, 42, 1, '09:00 – Breakfast'),
(169, 42, 2, '10:30 – Departure'),
(170, 42, 3, '18:00 – Return to Tangier');

-- --------------------------------------------------------

--
-- Table structure for table `trip_days`
--

CREATE TABLE `trip_days` (
  `day_id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `day_number` int(11) NOT NULL,
  `day_title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_days`
--

INSERT INTO `trip_days` (`day_id`, `trip_id`, `day_number`, `day_title`) VALUES
(7, 9, 1, 'Sunday: Arrival and Coastal Exploration in Essaouira'),
(8, 9, 2, 'Monday: Cultural Immersion and Coastal Experiences in Essaouira'),
(9, 9, 3, 'Tuesday: Farewell to Essaouira & Journey Back to Tangier'),
(10, 10, 1, 'Monday: Arrival in Agadir & Sunset at the Oufella'),
(11, 10, 2, 'Tuesday: Adventure & Culture in Agadir'),
(12, 10, 3, 'Wednesday: Farewell Agadir & CrocoPark Visit'),
(13, 11, 1, 'Friday: Arrival in Al Hoceima & Coastal Serenity'),
(14, 11, 2, 'Saturday: Coastal Adventures & Cultural Evenings in Al Hoceima'),
(15, 11, 3, 'Sunday: Nature Farewell & Return to Tangier'),
(25, 19, 1, 'friday: Journey to Ouzoud & Sunset Waterfall Stroll'),
(26, 19, 2, 'Saturday: Adventure & Nature at Ouzoud Falls'),
(27, 19, 3, 'Sunday: Farewell through Berber Heritage'),
(28, 20, 1, 'Monday: Arrival & Oasis Discovery Day'),
(29, 20, 2, 'Tuesday: Adventure in Nature: Hike, Swim & Climb'),
(30, 20, 3, 'Wednesday: Farewell in Serenity: Morning Meditation & Return'),
(31, 21, 1, 'Friday: Arrival in Dakhla: Lagoon Adventures & Sunset Walk'),
(32, 21, 2, 'Saturday: Thrill & Tranquility: Kitesurfing, Dunes & Hammam'),
(33, 21, 3, 'Sunday: Departure Day: Return to Tangier'),
(34, 22, 1, 'friday: Arrival in Imlil: Acclimatization & Briefing'),
(35, 22, 2, 'Saturday: Toubkal Trekking Day: Trail, Picnic & Music'),
(36, 22, 3, 'Sunday: Berber Market Visit & Return to Tangier'),
(37, 23, 1, 'sunday: Arrival in Chefchaouen: Medina Tour & Ras El Maa'),
(38, 23, 2, 'monday: Views & Vibes: Hike to Spanish Mosque & Relaxation'),
(39, 23, 3, 'Tuesday: Market Visit & Return to Tangier'),
(40, 24, 1, 'Friday: Arrival in Imilchil: Lakes Visit & Relaxation'),
(41, 24, 2, 'Saturday: Village Life: Culture, Craft & Traditions'),
(42, 24, 3, 'Sunday: Departure from Imilchil & Return to Tangier');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `role` enum('customer','supervisor','admin') NOT NULL DEFAULT 'customer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `password`, `phone`, `role`) VALUES
(7, 'aymane', 'elafia', 'aymane@gmail.com', '$2y$10$JosL.II6QnH2fQLOC/GgM.wZPaT7xaWErpcUabVjKhISuNTVoWwTW', '112', 'customer'),
(15, 'aymane', 'elafia', 'ayma@gmail.com', '$2y$10$rWwHLETX3tsc.AXGrZmvjeV9bwJfjFVsThLcxwYmtKX7zyHDRKx3m', '128', 'admin'),
(16, 'aymane', 'elafia', 'aym@gmail.com', '$2y$10$XVvuargZwn1t87WQ7ie/POccx4sTKUokIw8mbiY2e/VnuJqWHvkMi', '654', 'supervisor'),
(18, 'aymane', 'elafia', 'a@gmail.com', '$2y$10$rI5asCvb48AXmMM2oTpSDuV4xQ2IwkFxJwMkuhZ6qoghu9Yhu65/C', '54321', 'admin'),
(20, 'aymane', 'elafia', 'ayman@gmail.com', '$2y$10$XEVyuidjoAf5nNtPxBK3Ku5.brHJPzv44wipdWqYriT.kOETLPTjC', '99', 'customer');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`),
  ADD UNIQUE KEY `cin` (`cin`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD KEY `trip_id` (`trip_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `trip_id` (`trip_id`);

--
-- Indexes for table `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`trip_id`),
  ADD KEY `trip_category_id` (`trip_category_id`);

--
-- Indexes for table `trips_categories`
--
ALTER TABLE `trips_categories`
  ADD PRIMARY KEY (`trip_category_id`);

--
-- Indexes for table `trip_activities`
--
ALTER TABLE `trip_activities`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `day_id` (`day_id`);

--
-- Indexes for table `trip_days`
--
ALTER TABLE `trip_days`
  ADD PRIMARY KEY (`day_id`),
  ADD KEY `trip_id` (`trip_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
  MODIFY `trip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `trips_categories`
--
ALTER TABLE `trips_categories`
  MODIFY `trip_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `trip_activities`
--
ALTER TABLE `trip_activities`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=171;

--
-- AUTO_INCREMENT for table `trip_days`
--
ALTER TABLE `trip_days`
  MODIFY `day_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`trip_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`trip_id`) ON DELETE CASCADE;

--
-- Constraints for table `trips`
--
ALTER TABLE `trips`
  ADD CONSTRAINT `trips_ibfk_1` FOREIGN KEY (`trip_category_id`) REFERENCES `trips_categories` (`trip_category_id`);

--
-- Constraints for table `trip_activities`
--
ALTER TABLE `trip_activities`
  ADD CONSTRAINT `trip_activities_ibfk_1` FOREIGN KEY (`day_id`) REFERENCES `trip_days` (`day_id`) ON DELETE CASCADE;

--
-- Constraints for table `trip_days`
--
ALTER TABLE `trip_days`
  ADD CONSTRAINT `trip_days_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`trip_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
