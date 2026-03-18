-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 23, 2024 at 05:38 AM
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
-- Database: `fyp`
--

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE `address` (
  `A_ID` int(11) NOT NULL,
  `Address_1` varchar(225) NOT NULL,
  `Address_2` varchar(225) NOT NULL,
  `Postcode` int(11) NOT NULL,
  `City` varchar(100) NOT NULL,
  `state_country` varchar(100) NOT NULL,
  `C_ID` int(11) NOT NULL,
  `Address_status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `address`
--

INSERT INTO `address` (`A_ID`, `Address_1`, `Address_2`, `Postcode`, `City`, `state_country`, `C_ID`, `Address_status`) VALUES
(47, '2547,jalan sri putri 8/4', 'taman putri kulai', 75450, 'Ayer Keroh', 'Melaka, Malaysia', 46, 1),
(48, '2547,jalan sri putri 8/4', 'undefined', 75450, 'Bukit Beruang', 'Melaka, Malaysia', 46, 1),
(51, '2547,jalan sri putri 8/4', 'undefined', 75450, 'Ayer Keroh', 'Melaka, Malaysia', 46, 1),
(54, '2547,jalan sri putri 8/4', 'undefined', 75450, 'Bukit Katil', 'Melaka, Malaysia', 46, 1),
(55, '2547,jalan sri putri 8/4', 'taman putri kulai', 75450, 'Ayer Keroh', 'Melaka, Malaysia', 50, 1),
(56, 'no.5,jalan s/p7,taman SRI panchor', 'dsf', 75450, 'Ayer Keroh', 'Melaka, Malaysia', 52, 1);

-- --------------------------------------------------------

--
-- Table structure for table `barista`
--

CREATE TABLE `barista` (
  `B_ID` int(11) NOT NULL,
  `B_Name` varchar(100) NOT NULL,
  `B_Description` text NOT NULL,
  `B_Photo` varchar(100) NOT NULL,
  `barista_status` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barista`
--

INSERT INTO `barista` (`B_ID`, `B_Name`, `B_Description`, `B_Photo`, `barista_status`) VALUES
(1, 'Fadilah', 'She is a barista good in americano,, and very friendly and strong girl', 'fadilah.png', 'Inactive'),
(2, 'jenny', 'Working here is honestly really fun to the point where I’m excited to go to work everyday! I have really learnt and grown a lot not only in my barista skills, but management knowledge as well. I can also say that I have made long lasting friendships here at ZUS. We learn a lot from each other', 'janny.jpg', 'Active'),
(4, 'Adam', 'All the laughter, side jokes, smiles on their faces mean a lot to me.☺️', 'Adam.webp', 'Active'),
(5, 'Eshley', '.team-desc p {\r\n  margin-top: -50px; /* Adjust margin as needed */\r\n  text-align: center; /* Center-align the text */\r\n}\r\n', 'Eshley.jpg', 'Inactive'),
(6, 'Amelia', 'dsfsdfsd', 'Amelia.jpg', 'Inactive'),
(7, 'siewwennnnn', 'dsvsdvsd', 'internet.png', 'Inactive'),
(8, 'jaminaaaaa', 'dsfsg', 'back.jpg', 'Inactive'),
(9, 'Frilah', 'goood', 'Ahmad.jpg', 'Active'),
(10, 'maria', 'i am a barista good in americano', 'Johan.jpg', 'Inactive'),
(11, 'Johan', 'Meet Johan, our dedicated barista whose passion for coffee shines through in every cup. With a knack for perfecting espresso shots and creating stunning latte art, Johan transforms coffee into a true culinary experience.', 'Johan.jpg', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `CT_ID` int(11) NOT NULL,
  `C_ID` int(11) NOT NULL,
  `C_Status` varchar(50) NOT NULL,
  `Promo_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`CT_ID`, `C_ID`, `C_Status`, `Promo_ID`) VALUES
(99, 46, 'Paid', NULL),
(100, 46, 'Paid', NULL),
(101, 50, 'Paid', NULL),
(102, 50, 'No-paid', NULL),
(103, 46, 'Paid', NULL),
(104, 46, 'Paid', 2),
(105, 46, 'Paid', NULL),
(106, 46, 'Paid', NULL),
(107, 46, 'Paid', NULL),
(108, 46, 'Paid', 3),
(109, 46, 'Paid', NULL),
(110, 46, 'Paid', NULL),
(111, 46, 'Paid', NULL),
(112, 46, 'Paid', 22),
(113, 52, 'Paid', 2),
(114, 52, 'No-paid', NULL),
(115, 46, 'Paid', NULL),
(116, 46, 'Paid', 23),
(117, 46, 'No-paid', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cart_item`
--

CREATE TABLE `cart_item` (
  `CI_ID` int(11) NOT NULL,
  `CT_ID` int(11) NOT NULL,
  `P_ID` int(11) NOT NULL,
  `Qty` int(11) NOT NULL,
  `sub_price` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_item`
--

INSERT INTO `cart_item` (`CI_ID`, `CT_ID`, `P_ID`, `Qty`, `sub_price`) VALUES
(10, 99, 49, 2, 21.8),
(11, 99, 54, 2, 28),
(12, 99, 50, 1, 6.9),
(13, 100, 51, 1, 7.9),
(14, 100, 56, 1, 25.9),
(15, 100, 60, 1, 14.9),
(16, 100, 49, 1, 10.9),
(17, 101, 57, 2, 7.8),
(18, 101, 53, 2, 27.8),
(19, 101, 50, 1, 6.9),
(20, 102, 49, 1, 10.9),
(22, 103, 49, 1, 10.9),
(23, 103, 53, 1, 13.9),
(24, 103, 61, 1, 7.9),
(25, 103, 60, 1, 14.9),
(26, 103, 56, 1, 25.9),
(27, 103, 58, 1, 7.9),
(28, 104, 50, 1, 6.9),
(30, 104, 62, 2, 20),
(31, 105, 56, 1, 25.9),
(32, 106, 57, 2, 7.8),
(33, 107, 57, 1, 3.9),
(34, 108, 50, 2, 13.8),
(35, 108, 60, 4, 59.6),
(36, 109, 50, 2, 13.8),
(37, 109, 55, 4, 111.6),
(38, 109, 53, 2, 27.8),
(39, 110, 59, 2, 29.8),
(40, 111, 60, 2, 29.8),
(41, 113, 90, 4, 40),
(42, 113, 51, 6, 47.4),
(43, 113, 51, 1, 7.9),
(44, 112, 81, 1, 12),
(45, 112, 90, 3, 30),
(46, 115, 89, 2, 14),
(60, 116, 50, 2, 19.8);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `CA_ID` int(9) NOT NULL,
  `CA_Name` varchar(225) NOT NULL,
  `CA_Desc` text NOT NULL,
  `CA_SSID` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`CA_ID`, `CA_Name`, `CA_Desc`, `CA_SSID`) VALUES
(9, 'Coffee', 'Explore a range of rich coffee blends, from smooth roasts to intense espresso varieties. Elevate your coffee experience today!', '0'),
(10, 'Milk Tea', 'Indulge in the creamy goodness of our signature milk teas, crafted with premium tea leaves and a choice of creamy or fruity flavors.', '0'),
(11, 'Cocoa', 'Savor the rich and comforting taste of hot cocoa, made with premium cocoa powder and served with a dollop of whipped cream for an indulgent treat.', '0'),
(12, 'Smoothies', 'Refresh yourself with our selection of vibrant and nutrient-packed smoothies, blending together fresh fruits, yogurt, and natural sweeteners for a wholesome drink.', '0'),
(13, 'Fruit Juice', 'Quench your thirst with our refreshing fruit juices, made from a variety of seasonal fruits to provide a burst of vitamins and natural flavors.', '0'),
(14, 'Cakes', 'Treat yourself to our decadent cakes, baked fresh daily with the finest ingredients and available in a variety of flavors, from rich chocolate to tangy lemon.', '0'),
(15, 'Pastries', 'Indulge in our selection of freshly baked pastries, including flaky croissants, buttery danishes, and sweet cinnamon rolls, perfect for pairing with your favorite coffee or tea.', '0'),
(16, 'Pizza', 'Delight in our delicious pizzas, topped with a variety of savory ingredients and baked to perfection in a wood-fired oven, offering a crispy crust and mouthwatering flavors.', '0'),
(18, 'Hot Meals', 'Enjoy a hearty and satisfying meal with our selection of hot dishes, ranging from comforting soups and sandwiches to flavorful pasta and rice bowls, prepared fresh to order.', '0'),
(28, 'Burger', 'Very delicious', ''),
(29, 'abc', 'shdifjkasffsfsasf', ''),
(30, 'aaaaa', 'sdfdsgrehtrh', ''),
(31, 'qqqqqqqqqqqq', 'ddgddfh', ''),
(32, 'rrrr', 'fvcxvxcv', ''),
(33, 'ribena', 'dsgsdgds', ''),
(34, 'hhaaaa', 'sasdas', ''),
(35, 'asdasd', 'fasfas', ''),
(36, 'wd', 'ds', ''),
(37, 'Juice', 'good', ''),
(38, 'abcabc', 'ewfwefwefwe', ''),
(39, 'zebraaaaa', 'efwefewfwef', '');

-- --------------------------------------------------------

--
-- Table structure for table `contact_us`
--

CREATE TABLE `contact_us` (
  `Co_ID` int(11) NOT NULL,
  `Firstname` varchar(50) NOT NULL,
  `Lastname` varchar(50) NOT NULL,
  `Email` varchar(225) NOT NULL,
  `Phone` varchar(50) NOT NULL,
  `Subject` varchar(225) NOT NULL,
  `Message` text NOT NULL,
  `Contact_Status` varchar(50) NOT NULL,
  `Add_By` varchar(50) DEFAULT NULL,
  `Reply_Message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_us`
--

INSERT INTO `contact_us` (`Co_ID`, `Firstname`, `Lastname`, `Email`, `Phone`, `Subject`, `Message`, `Contact_Status`, `Add_By`, `Reply_Message`) VALUES
(3, 'Tan', 'Ying Xing', 'yingxing11@gmmail.com', '0198876543', 'Opening Hour', 'Hello there. I would like to ask about the opening hour of the cafe. And is it open during the Mother\'s Day?\r\n', 'Replied', 'CJS9587', 'yes we do open on that day'),
(4, 'Jonathan', 'Daniel', 'jonathan778@gmail.com', '0186654323', 'Make Reservation', 'Hello, I\'d like to make a reservation for dinner tonight for two people at 7:00 PM. Is there availability, and do you have any special dishes or promotions tonight?', 'Replied', 'CJS9587', 'fvd'),
(5, 'chin', 'yuxuan', 'yuxuan9008@gmail.com', '0183259008', 'haloooooo', 'dsvsdvdsv', 'Replied', 'CJS9587', 'waaaaaa'),
(6, 'chin', 'yuxuan', 'yuxuan9008@gmail.com', '0183259008', 'aaaaaa', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'Replied', 'CJS8284', 'bbbbbbbbbb'),
(7, 'chin', 'yuxuan', 'yuxuan9008@gmail.com', '0183259008', 'aaaaa', 'aaasdadasdasdasd', 'Replied', 'CJS9587', 'grerrggr'),
(8, 'Jane', 'Tan', 'janeadj@gmail.com', '605452435465', 'Closing Hour', 'Hello,\nI\'m interested in learning more about your latest product offerings. Could you please provide me with additional details?\nThank you.', 'Replied', 'CJS9587', 'Hi John,\r\nThank you for your inquiry. \r\nOur latest product line includes XYZ product, which offers advanced features and benefits. \r\nPlease let us know if you\'d like more detailed information or would like to schedule a demo.'),
(9, 'Goh', 'Siew Wen', 'gohsiewwen2004@gmail.com', '600187734782', 'Inquiry about Menu', 'Hello,\nI am interested in your menu offerings, particularly your coffee selection. Could you please provide more details about the types of coffee you serve and their prices? Also, do you have any special offers or promotions currently running?\nThank you.', 'Replied', 'CJS9587', 'Hello,\r\n\r\nThank you for reaching out to us with your inquiry. We offer a variety of coffee options including espresso, latte, cappuccino, and more. Our prices range from $2.50 to $4.50 depending on the size and type of coffee.\r\n\r\nCurrently, we have a promotion where you can enjoy a free pastry with any purchase of a large coffee. Please visit our website for more details on our menu and promotions.\r\n\r\nWe hope this reply addresses your question. Please feel free to visit our website for more information.');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `C_ID` int(9) NOT NULL,
  `C_Firstname` varchar(50) NOT NULL,
  `C_Lastname` varchar(50) NOT NULL,
  `C_Email` varchar(100) NOT NULL,
  `C_ContactNumber` varchar(12) NOT NULL,
  `C_PW` varchar(225) NOT NULL,
  `C_Status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`C_ID`, `C_Firstname`, `C_Lastname`, `C_Email`, `C_ContactNumber`, `C_PW`, `C_Status`) VALUES
(46, 'Chin', 'Yuxuan', 'yuxuan9008@gmail.com', '0183259008', '$2y$10$qpgDk99/aj7R5DBcGCWxseXGkLrM/oz5KYYL7HT6y.RG8k1YzItCe', 1),
(47, 'Tan', 'Chia Xing', 'ciaxing123@gmail.com', '0123068288', '$2y$10$CxOFAvXaXlMW/TK0/oJHR.OLXngCEsjAXh29puWbyBAjtLtIXoC4O', 1),
(48, 'Jenski', 'Pang', 'jenski889@gmail.com', '0123068288', '$2y$10$/AFGScoKpWsZOmmU/mqPiOBHm64MY0DENvgJa/lYAdjq3C7UBGT3u', 1),
(49, 'Ang', 'Yu Xun', 'yuxun@gmail.com', '0183259008', '$2y$10$WTYvrdDUqg6XTDmOpZoyG.FCZ2nNKEBGJ2tL3XP63hrM6MEwySuoS', 1),
(50, 'Goh', 'Siew Wen', 'gohsiewen2004@gmail.com', '0187734782', '$2y$10$0tS5qmxEMUki.Sw9FFn3y.BUNQDMWLpshN5xjzj6gygM3XUkWs9Zq', 0),
(52, 'Jamina', 'Tan', 'jamina5677@gmail.com', '605452435465', '$2y$10$c8O.la25B1Xbj2EHGr8k7OfYhhnkujis22w8HDFHCWxC.NW6toSFy', 1);

-- --------------------------------------------------------

--
-- Table structure for table `customization`
--

CREATE TABLE `customization` (
  `Custom_ID` int(11) NOT NULL,
  `Custom_Name` varchar(100) NOT NULL,
  `CC_ID` int(11) NOT NULL,
  `Custom_Price` float NOT NULL,
  `available_status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customization`
--

INSERT INTO `customization` (`Custom_ID`, `Custom_Name`, `CC_ID`, `Custom_Price`, `available_status`) VALUES
(4, 'Dairy Milk', 13, 1.4, 'Unavailable'),
(5, 'Soy Milk', 13, 0, 'Unavailable'),
(6, 'Almond Milk', 13, 3, 'Unavailable'),
(7, 'Oat Milk', 13, 6, 'Unavailable'),
(9, 'OATLY Milk', 13, 3.9, 'Unavailable'),
(10, 'Salted Caramel', 15, 1.9, 'Available'),
(11, 'French Vanilla', 15, 1.9, 'Available'),
(12, 'Roasted Hazelnut', 15, 1.9, 'Available'),
(13, 'With Whipped Cream', 17, 2, 'Available'),
(14, 'No Whipped Cream', 17, 0, 'Available'),
(18, 'Normal Sugar', 14, 0, 'Available'),
(19, 'Half Sugar', 14, 0, 'Available'),
(20, 'Slight Sugar', 14, 0, 'Available'),
(21, 'Non Sugar', 14, 0, 'Available'),
(23, 'Aloe Vera', 16, 1.79, 'Available'),
(24, 'Chia Seed', 16, 1.32, 'Available'),
(25, 'Coconut Jelly', 16, 3, 'Available'),
(26, 'Cream Cloud', 16, 1.79, 'Available'),
(31, 'Regular', 22, 0, 'Available'),
(32, 'Large', 22, 2, 'Available'),
(33, 'Lydia', 23, 0, 'Available'),
(34, 'Boss', 23, 0, 'Available'),
(35, 'Normal Ice', 24, 0, 'Available'),
(36, 'Less Ice', 24, 0, 'Available'),
(41, 'Medium', 22, 1.8, 'Available'),
(42, 'bbbbbbbb', 29, 1.5, 'Available'),
(43, 'ccccccc', 29, 2, 'Available'),
(44, 'aplleeeeeeee', 30, 2, 'Available'),
(45, 'orangeeee', 30, 3, 'Available'),
(46, 'Boba', 16, 1.9, 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `customize_category`
--

CREATE TABLE `customize_category` (
  `CC_ID` int(11) NOT NULL,
  `CC_Group` varchar(100) NOT NULL,
  `compulsory_status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customize_category`
--

INSERT INTO `customize_category` (`CC_ID`, `CC_Group`, `compulsory_status`) VALUES
(13, 'Milk Type', 'yes'),
(14, 'Sugar Level', 'yes'),
(15, 'Syrup', 'yes'),
(16, 'Topping', 'no'),
(17, 'Whipped Cream', 'yes'),
(22, 'Size', 'yes'),
(23, 'Bean', 'yes'),
(24, 'Ice Level', 'yes'),
(29, 'Temperature', 'yes'),
(30, 'Sweet Level', 'yes'),
(32, 'Coffee', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `details`
--

CREATE TABLE `details` (
  `D_ID` int(11) NOT NULL,
  `customize_id` int(100) NOT NULL,
  `c_item_id` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `details`
--

INSERT INTO `details` (`D_ID`, `customize_id`, `c_item_id`) VALUES
(12, 7, 10),
(13, 18, 10),
(14, 32, 10),
(15, 33, 10),
(16, 35, 10),
(17, 10, 12),
(18, 14, 12),
(19, 32, 12),
(20, 33, 12),
(21, 35, 12),
(22, 18, 13),
(23, 25, 13),
(24, 26, 13),
(25, 13, 13),
(26, 35, 13),
(27, 5, 16),
(28, 19, 16),
(29, 32, 16),
(30, 34, 16),
(31, 35, 16),
(32, 11, 19),
(34, 32, 19),
(35, 33, 19),
(36, 35, 19),
(37, 14, 19),
(39, 21, 20),
(40, 32, 20),
(41, 34, 20),
(42, 35, 20),
(43, 6, 20),
(47, 5, 22),
(48, 21, 22),
(49, 31, 22),
(50, 33, 22),
(51, 35, 22),
(52, 4, 24),
(53, 19, 24),
(54, 14, 24),
(55, 32, 24),
(56, 34, 24),
(57, 36, 24),
(58, 11, 28),
(59, 13, 28),
(60, 31, 28),
(61, 33, 28),
(62, 35, 28),
(67, 20, 36),
(68, 32, 36),
(69, 33, 36),
(70, 36, 36),
(72, 13, 42),
(73, 35, 42),
(74, 23, 42),
(75, 21, 43),
(76, 13, 43),
(77, 35, 43),
(78, 23, 43),
(79, 20, 42),
(80, 18, 46),
(81, 32, 46),
(82, 36, 46),
(83, 43, 46),
(84, 25, 46),
(150, 18, 60),
(151, 32, 60),
(152, 34, 60),
(153, 36, 60);

-- --------------------------------------------------------

--
-- Table structure for table `opt`
--

CREATE TABLE `opt` (
  `Opt_ID` int(11) NOT NULL,
  `CC_ID` int(11) NOT NULL,
  `P_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `opt`
--

INSERT INTO `opt` (`Opt_ID`, `CC_ID`, `P_ID`) VALUES
(65, 13, 65),
(66, 14, 65),
(67, 13, 71),
(68, 14, 71),
(69, 13, 72),
(70, 14, 72),
(71, 23, 76),
(72, 24, 76),
(193, 13, 82),
(194, 14, 82),
(195, 13, 83),
(196, 14, 83),
(197, 15, 83),
(201, 22, 86),
(202, 23, 86),
(211, 14, 88),
(212, 16, 88),
(213, 22, 88),
(234, 14, 89),
(235, 16, 89),
(236, 22, 89),
(237, 24, 89),
(238, 29, 89),
(291, 22, 81),
(292, 23, 81),
(301, 14, 60),
(302, 15, 60),
(303, 16, 60),
(304, 22, 60),
(321, 14, 61),
(322, 17, 61),
(323, 22, 61),
(324, 23, 61),
(325, 24, 61),
(326, 14, 70),
(327, 16, 70),
(328, 24, 70),
(329, 14, 51),
(330, 16, 51),
(331, 17, 51),
(332, 24, 51),
(333, 13, 49),
(334, 14, 49),
(335, 16, 49),
(336, 22, 49),
(337, 23, 49),
(338, 24, 49),
(339, 13, 93),
(340, 15, 93),
(341, 17, 93),
(342, 22, 93),
(343, 23, 93),
(344, 24, 93),
(353, 14, 50),
(354, 22, 50),
(355, 23, 50),
(356, 24, 50);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `PM_ID` int(11) NOT NULL,
  `Date` date NOT NULL DEFAULT current_timestamp(),
  `Time` time NOT NULL DEFAULT current_timestamp(),
  `O_ID` int(11) NOT NULL,
  `PM_Status` varchar(50) NOT NULL,
  `PM_Method` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`PM_ID`, `Date`, `Time`, `O_ID`, `PM_Status`, `PM_Method`) VALUES
(1, '2024-04-22', '19:42:35', 9, 'paid', 'card'),
(2, '2024-04-22', '20:08:59', 10, 'paid', 'card'),
(3, '2024-04-22', '20:23:55', 11, 'paid', 'card'),
(4, '2024-04-24', '10:55:02', 12, 'paid', 'card'),
(5, '2024-05-28', '01:21:12', 14, 'paid', 'PayPal'),
(6, '2024-05-28', '01:27:48', 15, 'paid', 'card'),
(7, '2024-05-31', '03:21:25', 16, 'paid', 'card'),
(8, '2024-05-31', '03:27:40', 17, 'paid', 'card'),
(9, '2024-06-15', '01:43:02', 18, 'paid', 'card'),
(10, '2024-06-15', '01:46:22', 19, 'paid', 'card'),
(11, '2024-06-15', '01:47:37', 20, 'paid', 'card'),
(12, '2024-06-18', '07:23:15', 21, 'paid', 'PayPal'),
(13, '2024-06-19', '07:59:48', 23, 'paid', 'card'),
(14, '2024-06-19', '08:22:24', 24, 'paid', 'PayPal'),
(15, '2024-06-20', '17:29:00', 25, 'paid', 'card'),
(16, '2024-06-22', '15:04:57', 26, 'paid', 'card');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `P_ID` int(9) NOT NULL,
  `P_Name` varchar(100) NOT NULL,
  `P_Photo` varchar(225) DEFAULT NULL,
  `P_Price` decimal(10,2) NOT NULL,
  `P_Category` int(9) NOT NULL,
  `P_Desc` longtext NOT NULL,
  `Add_by` varchar(50) NOT NULL,
  `Customize_Status` varchar(50) NOT NULL,
  `P_Status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`P_ID`, `P_Name`, `P_Photo`, `P_Price`, `P_Category`, `P_Desc`, `Add_by`, `Customize_Status`, `P_Status`) VALUES
(49, 'Iced Latte', 'iced_latte.png', 10.90, 30, 'Enjoy our rich, creamy latte crafted with freshly brewed espresso and silky steamed milk. Perfect for any time of day, its smooth flavor and luxurious texture offer a delightful coffee experience.', 'CJS2611', 'yes', 'yes'),
(50, 'Americano', 'mocha.png', 10.90, 9, 'Indulge in the bold simplicity of our Americano, a classic espresso-based drink made by adding hot water to rich espresso. Savor its robust flavor and smooth finish, perfect for those who appreciate the pure essence of coffee.', 'CJS2611', 'yes', 'yes'),
(51, 'Pink Lemonade', 'pink_lemonade.png', 7.90, 13, 'Refresh your senses with our zesty Lemonade, a delightful blend of tangy lemon juice, water, and a hint of sweetness. Enjoy its crisp and invigorating taste, ideal for quenching your thirst on a sunny day.', 'CJS2611', 'yes', 'yes'),
(53, 'Moist Chocolate Cake', 'moistcake.png', 13.90, 14, 'Indulge in pure decadence with our Moist Chocolate Cake. Each bite is a heavenly experience, with its rich, velvety texture and intense chocolate flavor.', 'CJS2611', 'no', 'yes'),
(54, 'Cheese Cake', 'cheesecake.png', 15.90, 14, 'Treat yourself to a slice of heaven with our creamy and indulgent Cheesecake. Made with the finest ingredients and crafted to perfection, each bite is a symphony of smooth creaminess and delicate sweetness.', 'CJS2611', 'no', 'yes'),
(55, 'Classic Cheese Pizza', 'classic-cheese-pizza.jpg', 27.90, 16, 'Savor the timeless flavors of our Classic Cheese Pizza. Made with a perfect blend of gooey melted cheese and rich tomato sauce atop a crispy crust, every bite is a symphony of cheesy goodness. ', 'CJS2611', 'no', 'yes'),
(56, 'Hawaiian Pizza', 'hawaiian.jpg', 25.90, 16, '\r\nTransport yourself to the tropics with our Hawaiian Pizza, a delightful blend of sweet and savory flavors. Topped with juicy pineapple chunks, savory ham, and melted mozzarella cheese, each bite is a burst of sunshine. ', 'CJS2611', 'no', 'yes'),
(57, 'Curry Puff', 'curry-puff.png', 10.00, 15, 'Indulge in the savory goodness of our Curry Puff, a delightful pastry filled with a flavorful mixture of curried potatoes, onions, and spices, all enveloped in a flaky, golden crust. Each bite offers a satisfying blend of aromatic curry flavors, making it a perfect snack or appetizer for any occasion. ', 'CJS2611', 'no', 'yes'),
(58, 'Croissant French Toast', 'Croissant-French-Toast.jpg', 7.90, 15, 'Experience the perfect fusion of two beloved breakfast classics with our Croissant French Toast. ', 'CJS2611', 'no', 'yes'),
(59, 'Orange Smoothie', 'orange smoothies.jpg', 14.90, 12, 'Savor the refreshing taste of sunshine with our Orange Smoothies. Bursting with tangy citrus flavor and packed with vitamin C, these smoothies are a zesty delight for any time of day. Blended to creamy perfection, they offer a refreshing burst of energy and vitality. ', 'CJS2611', 'no', 'yes'),
(60, 'Strawberry Smoothie', 'strawberry smoothie.jpg', 14.90, 28, 'Indulge in the sweet, luscious taste of summer with our Strawberry Smoothie. Made from ripe, juicy strawberries blended to perfection, this smoothie is a burst of vibrant flavor in every sip. ', 'CJS2611', 'yes', 'yes'),
(61, 'Babycino', 'babycino.png', 7.90, 9, 'Indulge your little ones with our delightful babycino, a warm and frothy treat designed just for them. Made with care using the finest quality milk and topped with a light dusting of cocoa or cinnamon, our babycino is the perfect beverage to delight young taste buds. ', 'CJS2611', 'yes', 'yes'),
(62, 'strawberry cake', 'man.png', 10.00, 14, 'delicious cake', 'CJS4615', 'no', 'yes'),
(63, 'siewwen', 'Screenshot 2023-01-06 141105.png', 10.00, 18, 'siewwen', 'CJS9587', 'yes', 'yes'),
(64, 'siewwen', 'photo_2022-10-12_13-42-08.jpg', 10.00, 11, 'siewwen', 'CJS9587', 'yes', 'yes'),
(65, 'siewwen', 'photo_2022-10-12_13-42-08.jpg', 10.00, 13, 'sdcsadasd', 'CJS9587', 'yes', 'yes'),
(66, 'peiying', 'photo_2022-10-12_13-42-08.jpg', 10.00, 10, 'fdfffff', 'CJS9587', 'yes', 'yes'),
(67, 'product1', '../image/product/Screenshot 2023-01-19 191726.png', 10.00, 11, 'product 12345', 'Tok Pei Ying', 'unavailable', 'available'),
(69, 'abcde', '../image/product/Screenshot 2023-01-20 131614.png', 20.00, 10, 'abcdefgh', 'CJS9587', 'available', 'available'),
(70, 'Pink Lemonade', 'pink_lemonade.png', 10.00, 37, 'pink lemonnade good', 'CJS9587', 'yes', 'yes'),
(71, 'aaaaaaaaaaaaaaaaa', 'uploads/Screenshot 2023-01-06 140914.png', 10.00, 13, 'aaaaaaaaaaaaaaa', 'CJS9587', 'available', 'available'),
(72, 'qqqqqqqqqqqq', 'tut7 q4.png', 12.00, 10, 'qqqqqqqq', 'CJS9587', 'yes', 'yes'),
(76, 'thank you', 'Screenshot 2023-01-30 143900.png', 12.00, 11, 'qwert', 'CJS9587', 'yes', 'yes'),
(81, 'abc hongdoubing', 'Amelia.jpg', 15.00, 9, 'sdfsdvcsdc', 'CJS9587', 'yes', 'yes'),
(82, 'siewwen', '', 11.00, 10, 'dsvcsdvsdf', 'CJS9587', 'yes', 'yes'),
(83, 'zzz', '', 5.00, 9, 'sdfsfsdas', 'CJS9587', 'yes', 'yes'),
(84, 'zebra', '', 12.00, 30, 'wdwds', 'CJS9587', 'no', 'yes'),
(85, 'zebra2222', 'Ahmad.jpg', 12.00, 32, 'dadasaahahahaha', 'CJS9587', 'no', 'yes'),
(86, 'zebra3', 'instagram.png', 40.00, 9, 'sdfcsdcs', 'CJS9587', 'yes', 'yes'),
(87, 'avatar', 'age-limit.png', 12.00, 9, 'qqqqqqqqqqqqq', 'CJS9587', 'no', 'yes'),
(88, 'Peach Smoothies', 'delete.png', 13.00, 9, 'delicios', 'CJS9587', 'yes', 'yes'),
(89, 'Orange Juiceee', 'grabpay.png', 7.00, 9, 'orange juice is good', 'CJS8284', 'yes', 'yes'),
(90, 'ababa', 'venom3.png', 20.00, 30, 'thank dfhiufkenfe,\r\nsdsdfiks\r\nsdffosdojo', 'CJS9587', 'yes', 'yes'),
(91, 'zebraaaaaaaaaaa', 'back.jpg', 12.00, 39, 'AAAAAAAAAASssssssssss', 'CJS9587', 'no', 'no'),
(92, 'Shuan Q', 'lalaland.jpg', 10.00, 39, 'nduendewknfoiwelf', 'CJS9587', 'no', 'yes'),
(93, 'Mocha', 'mocha.png', 14.90, 9, 'Indulge in the rich harmony of espresso and chocolate with our exquisite Mocha. Crafted from premium espresso coffee and velvety chocolate syrup, this decadent drink offers a perfect balance of robust coffee flavor and smooth, creamy sweetness.', 'CJS9587', 'yes', 'yes'),
(94, 'pppppp', '4550857_email_gmail_mail_sending_yahoo_icon (1).png', 10.00, 35, 'dvsfvcws', 'CJS9587', 'no', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `promo`
--

CREATE TABLE `promo` (
  `Promo_ID` int(11) NOT NULL,
  `Promo_Name` varchar(50) NOT NULL,
  `Discount` float NOT NULL,
  `Start_From` date NOT NULL,
  `End_By` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promo`
--

INSERT INTO `promo` (`Promo_ID`, `Promo_Name`, `Discount`, `Start_From`, `End_By`) VALUES
(2, 'CJP0016', 25, '2024-06-14', '2024-06-22'),
(3, 'HCNY', 15, '2024-06-22', '2024-06-29'),
(9, 'CuppaJoyyy', 10, '2024-06-23', '2024-06-26'),
(21, 'Welcome!', 20, '2024-01-01', '2024-12-31'),
(22, 'HajiPromo', 10, '2024-06-15', '2024-06-21'),
(23, 'FATHER2024', 10, '2024-06-22', '2024-06-27'),
(24, 'VisitMelaka', 10, '2024-06-22', '2024-06-30'),
(25, 'MOTHERDAY', 10, '2024-06-22', '2024-06-29');

-- --------------------------------------------------------

--
-- Table structure for table `promo_history`
--

CREATE TABLE `promo_history` (
  `P_History_ID` int(11) NOT NULL,
  `Promo_ID` int(11) NOT NULL,
  `Cus_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promo_history`
--

INSERT INTO `promo_history` (`P_History_ID`, `Promo_ID`, `Cus_ID`) VALUES
(1, 2, 46),
(2, 3, 46),
(3, 2, 52),
(4, 22, 46),
(5, 23, 46);

-- --------------------------------------------------------

--
-- Table structure for table `rate_product`
--

CREATE TABLE `rate_product` (
  `RP_ID` int(11) NOT NULL,
  `P_ID` int(11) NOT NULL,
  `Ra_ID` int(11) NOT NULL,
  `Rating_Product` int(11) NOT NULL,
  `Comment_Product` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rate_product`
--

INSERT INTO `rate_product` (`RP_ID`, `P_ID`, `Ra_ID`, `Rating_Product`, `Comment_Product`) VALUES
(5, 49, 6, 3, 'too sweet!!'),
(6, 50, 6, 4, ''),
(7, 54, 6, 4, ''),
(20, 50, 9, 4, 'pahittttt'),
(21, 53, 9, 4, ''),
(22, 57, 9, 5, 'deliciousssss'),
(23, 90, 12, 1, 'rcvt r6tfgh t'),
(24, 51, 12, 4, 'dctfvg  tg rtfg'),
(25, 81, 13, 5, 'gsdsg'),
(26, 90, 13, 5, 'ereged'),
(27, 60, 15, 5, 'very delicious'),
(28, 49, 15, 4, 'too sweet, bettter choose less sugar'),
(29, 51, 15, 4, 'hahahaha'),
(30, 56, 15, 3, 'soso, suggest to request for more cheese'),
(31, 49, 17, 4, ''),
(32, 53, 17, 5, ''),
(33, 58, 17, 4, ''),
(34, 60, 17, 4, ''),
(35, 61, 17, 4, ''),
(36, 56, 17, 5, ''),
(37, 59, 18, 5, '');

-- --------------------------------------------------------

--
-- Table structure for table `rating`
--

CREATE TABLE `rating` (
  `Ra_ID` int(11) NOT NULL,
  `O_ID` int(11) NOT NULL,
  `R_ID` varchar(50) NOT NULL,
  `Rating_R` int(11) DEFAULT NULL,
  `Comment_R` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rating`
--

INSERT INTO `rating` (`Ra_ID`, `O_ID`, `R_ID`, `Rating_R`, `Comment_R`) VALUES
(6, 9, 'CJR2855', 5, ''),
(9, 11, 'CJR2855', 4, 'fast, received in good condition'),
(12, 23, 'CJR1002', 5, 'yt  gtyhj gyhuu'),
(13, 24, 'CJR1369', 5, 'sdgg'),
(15, 10, 'CJR2855', 5, 'gooof'),
(17, 12, 'CJR2855', 5, 'Godosjbfuewfsdc'),
(18, 20, 'CJR3204', 3, 'good');

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `O_ID` int(11) NOT NULL,
  `CT_ID` int(11) NOT NULL,
  `ReceiverName` varchar(100) NOT NULL,
  `ReceiverPhone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `R_ID` varchar(50) DEFAULT NULL,
  `A_ID` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Time` time NOT NULL,
  `Remark` text NOT NULL,
  `Total` float NOT NULL,
  `Delivery_Status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation`
--

INSERT INTO `reservation` (`O_ID`, `CT_ID`, `ReceiverName`, `ReceiverPhone`, `R_ID`, `A_ID`, `Date`, `Time`, `Remark`, `Total`, `Delivery_Status`) VALUES
(9, 99, 'chinyuxuan', '0183259008', 'CJR2855', 47, '2024-04-22', '19:39:59', 'pls put at the guard house', 79.836, 'completed'),
(10, 100, 'chinyuxuan', '0183259008', 'CJR2855', 47, '2024-04-22', '20:05:31', 'put at the guard house', 77.8326, 'completed'),
(11, 101, 'GohSiew Wen', '0187734782', 'CJR2855', 55, '2024-04-22', '20:22:54', 'call me when you arrrive', 54.184, 'completed'),
(12, 103, 'chinyuxuan', '0183259008', 'CJR2855', 48, '2024-04-24', '10:53:46', 'yyyuttiu rdrd', 95.524, 'completed'),
(14, 104, 'chinyuxuan', '0183259008', 'CJR2855', 48, '2024-05-28', '01:21:12', '', 29.64, 'pending'),
(15, 105, 'chinyuxuan', '0183259008', 'CJR4683', 47, '2024-05-28', '01:26:52', 'llllll', 30.9, 'pending'),
(16, 106, 'chinyuxuan', '0183259008', 'CJR2855', 47, '2024-05-31', '03:20:26', '', 11.63, 'pending'),
(17, 107, 'chinyuxuan', '0183259008', 'CJR4683', 47, '2024-05-31', '03:25:59', '', 8.315, 'pending'),
(18, 108, 'chinyuxuan', '0183259008', 'CJR1369', 47, '2024-06-15', '01:42:29', 'call me when rider arrive', 67.39, 'pending'),
(19, 109, 'chinyuxuan', '0183259008', 'CJR2855', 48, '2024-06-15', '01:45:55', '', 162.2, 'pending'),
(20, 110, 'chinyuxuan', '0183259008', 'CJR3204', 47, '2024-06-15', '01:47:09', '', 34.8, 'completed'),
(21, 111, 'chinyuxuan', '0183259008', 'CJR1002', 47, '2024-06-18', '07:23:15', '', 34.8, 'pending'),
(23, 113, 'a', '234', 'CJR1002', 56, '2024-06-19', '07:58:40', '', 96.3725, 'completed'),
(24, 112, 'chin yuxuan', '0183259008', 'CJR1369', 51, '2024-06-19', '08:22:24', '', 42.8, 'completed'),
(25, 115, 'chin yuxuan', '0183259008', 'CJR1369', 47, '2024-06-20', '17:28:29', 'asd', 29.64, 'pending'),
(26, 116, 'Chin Yuxuan', '0183259008', 'CJR1014', 51, '2024-06-22', '15:04:27', 'thnyydfsd', 22.82, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `rider`
--

CREATE TABLE `rider` (
  `R_ID` varchar(50) NOT NULL,
  `R_Name` varchar(100) NOT NULL,
  `R_Photo` varchar(100) NOT NULL,
  `R_Contact_Number` varchar(20) NOT NULL,
  `R_Email` varchar(80) NOT NULL,
  `R_License` varchar(225) NOT NULL,
  `L_Exp_Date` date DEFAULT NULL,
  `R_PlateNo` varchar(10) NOT NULL,
  `R_PW` varchar(225) NOT NULL,
  `Bank_Type` varchar(100) NOT NULL,
  `Bank_Number` bigint(20) NOT NULL,
  `Money_Earned` float NOT NULL,
  `Total_Claim` float NOT NULL,
  `R_Status` varchar(50) NOT NULL,
  `R_SAID` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rider`
--

INSERT INTO `rider` (`R_ID`, `R_Name`, `R_Photo`, `R_Contact_Number`, `R_Email`, `R_License`, `L_Exp_Date`, `R_PlateNo`, `R_PW`, `Bank_Type`, `Bank_Number`, `Money_Earned`, `Total_Claim`, `R_Status`, `R_SAID`) VALUES
('CJR1002', 'Joanna', 'Amelia.jpg', '0198876765', 'joanna123@gmail.com', 'boost.png', '2024-06-21', 'MKJ6789', '$2y$10$mb39RVdZbm4AgfDLOrKqxuNr8TOCg2lJsWA3XTJWAB4TdxN7Xj/4.', 'BigPay', 1923274929321, 4.81862, 15.4196, 'Inactive', 'CJS9587'),
('CJR1014', 'Afiqah', 'assets/image/man.png', '0187734782', 'afiqah333@gmail.com', 'assets/image/cinema-auditorium.png', '2024-07-24', 'MKJ6789', '$2y$10$2.hOg9lz9KOPtd2ZL59shetzINGcAZQSxjrt8eOmRyFGkYmqdVkuS', 'Bank Simpanna Nasional Berhad', 2132132122133123, 0, 0, 'Active', 'CJS9587'),
('CJR1369', 'Tom', 'Ahmad.jpg', '0187734782', 'tantom999@gmail.com', 'assets/image/abangadik.jpg', '2024-06-29', 'MKJ6789', '$2y$10$PB.gnluwUeFFE02YlBP.U.EpHj3JwszPldqJlhBGY0f3QfJ3tkDC.', 'Bank of America', 32423322324, 5.56, 0, 'Active', 'CJS9587'),
('CJR1389', 'Nicholas', 'assets/image/py.jpg', '0187734782', 'nicholaslee899@gmail.com', 'assets/image/add-video.png', '2024-06-28', 'MHQ1567', '$2y$10$wXF8PNMij4MIhokXaimLDO9Mhq7tGXqPvlFFZGvvh3mWQNvPkW20i', 'China Construction Bank (Malaysia) Berhad', 888888888888888888, 0, 0, 'Active', 'CJS9587'),
('CJR1750', 'joan', 'assets/image/Screenshot 2022-09-21 023202.png', '0187734782', 'joannnn@gmail.com', 'assets/image/Picture1.png', '2024-06-20', 'MLK1233', '$2y$10$HtqwXqeSbWxq8HMSrwDeIuiSb3LDmEPjLnh8eZL/rNw.xgbjkMYbC', '', 0, 0, 0, 'Inactive', 'CJS9587'),
('CJR2855', 'Aniq', 'Aniq.jpg', '0183259008', 'jessica1212@gmail.com', 'assets/image/back1.png', NULL, 'MFR5674', '$2y$10$hsMp3HDfxLNiXDeo/qFr8O2DNoQOLFOnL30oJQ4wHWmqBOCkp809i', '', 0, 8.66783, 0, 'Active', 'CJS2611'),
('CJR3204', 'Abdulah', 'Johan.jpg', '0187734782', 'abdulah7789@gmail.com', 'assets/image/cinema.jpg', '2024-06-27', 'MCH4567', '$2y$10$hHwGMmol5U4FfVKet7KluOWzHQlsNpTRRRbiqrUXr8tMKEVXeN4Hy', 'Bank of America', 12, 0, 0, 'Active', 'CJS9587'),
('CJR3647', 'Aniq', 'assets/image/Aniq.jpg', '0136676543', 'gohsiewwen2004@gmail.com', 'assets/image/aniq_license.png', '2024-05-14', 'MLK8756', '$2y$10$.nDsWi0NaZAe/.tnKMtwP.59rdadxSIoMIweWLa.9KmdzZwSCto7C', 'Boost Bank', 1262139213929121, 0, 0, 'Active', 'CJS9587'),
('CJR4568', 'Aqilah', 'assets/image/Ahmad.jpg', '0187734782', 'aqilah009@gmail.com', 'assets/image/back1.png', '2024-06-26', 'MKJ6789', '$2y$10$5YcF//QBnKn9RMSFJzh3tuBxrK1j5MGXwDIFYSKRJgG8xuxDg6SKq', 'Ambank', 109877656522, 0, 0, 'Active', 'CJS8284'),
('CJR4683', 'Johan', 'assets/image/Johan.jpg', '01110657110', 'johan123@gmail.com', 'assets/image/4550857_email_gmail_mail_sending_yahoo_icon (1).png', '2026-06-17', 'MLW5678', '$2y$10$bDWXaZRbCCJeE8OyKZPLcefU82U.XIotpAC0Yph/mlPlu12X46Oea', '', 0, 0, 0, 'Active', 'CJS4615'),
('CJR6961', 'Amelia', 'Amelia.jpg', '0198898765', 'teeamelia888@gmail.com', 'assets/image/abangadik.jpg', '2024-06-13', 'MLW5678', '$2y$10$N8PuhmjCnH.hCQ3Ttm/z5eZC4CCZU3NVPgFAl9p75ST8nNgGMPFMm', '', 0, 0, 0, 'Active', 'CJS4615'),
('CJR7191', 'Jamina', 'assets/image/Nurul Ilma.jpg', '0187734782', 'jamina5564@gmail.com', 'assets/image/admin.jpeg', '2024-09-18', 'MHQ1567', '$2y$10$r01xnUSSha8di5JObKVazuY.34qCpMvUhG2Zg8ns/K9gm3dsaSAGC', 'Bank of China (Malaysia) Berhad', 21, 0, 0, 'Active', 'CJS9587'),
('CJR7365', 'Siew Wen', 'assets/image/Aniq.jpg', '0199989876', 'siewwen2004@gmail', 'assets/image/cinema-2.jpg', '2024-06-27', 'MKJ6789', '$2y$10$Q7i1/FuSBswjZH1aInDJ4uN.38b..g1PsF4gGq60xYmqYNnXjdWXK', 'BigPay', 17382989876523, 0, 0, 'Inactive', 'CJS9587'),
('CJR7521', 'Arliah', 'assets/image/add-user.png', '0187734782', 'arliah@gmail.com', 'assets/image/back1.png', '2024-06-21', 'MHQ1567', '$2y$10$KLRL1Dpxi2NoeDEIouBDRecTGDbIC0E7J7xGOE9iNZZxbMsvr3JQ2', 'Boost Bank', 9223372036854775807, 0, 0, 'Active', 'CJS9587'),
('CJR7854', 'Jesus', 'assets/image/Screenshot 2023-05-21 064945.png', '0189909876', 'siewwen@gmail.com', 'assets/image/Screenshot 2023-06-03 184232.png', '2024-06-29', 'MKJ6789', '$2y$10$KVqQxtG.CyvZO9TXXfnkEeStPjjgTup71Vy5IXUlLoP7nUnFFWh/i', 'BNP Paribas Malaysia Berhad', 18391, 0, 0, 'Active', 'CJS9587'),
('CJR8060', 'abi', 'assets/image/admin.jpeg', '0187734782', 'go04@gmail.com', 'assets/image/Amelia.jpg', '2024-06-30', 'MHQ1567', '$2y$10$G/N4bJCnlAtMSN/hfsnq..N/KXq4kJHOA.xnR2YdFMM/ozuVlGsUC', 'Bank of China (Malaysia) Berhad', 3242344141, 0, 0, 'Inactive', 'CJS9587');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `S_ID` varchar(50) NOT NULL,
  `S_Name` varchar(50) NOT NULL,
  `S_Email` varchar(100) NOT NULL,
  `S_Photo` varchar(100) NOT NULL,
  `S_Password` varchar(225) NOT NULL,
  `S_Status` varchar(50) NOT NULL,
  `Super_Staff` varchar(100) NOT NULL,
  `S_ContactNum` varchar(12) NOT NULL,
  `Add_By` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`S_ID`, `S_Name`, `S_Email`, `S_Photo`, `S_Password`, `S_Status`, `Super_Staff`, `S_ContactNum`, `Add_By`) VALUES
('CJS2174', 'Jasmine', 'jasmine0976@gmail.com', 'assets/image/add-video.png', '$2y$10$h8e8cJvywaigk9HOlDLSfuTFZmplT6XfmTnYC4hqqBG.zMFe//Eda', 'Inactive', 'No', '0187734782', 'CJS9587'),
('CJS2427', 'Johan', 'johan1234@gmail.com', 'BB-tran.png', '$2y$10$C5HyHgXJ4gGHGQZ9c/l1lOidJIGZXUy8z2bNwArKNWkyYE/6COCxa', 'Active', 'No', '0198898765', 'CJS9587'),
('CJS2611', 'Benjamin', 'benjamin455@gmail.com', 'assets/image/sw.jpg', '$2y$10$jFh4kYbRwsQEUzBc3Ck3guaLL.FJ.HykmApw5WEr70oDdbVP53RJO', 'Active', 'Yes', '0187734782', ''),
('CJS3414', 'Aniq', 'aniqqqq123@gmail.com', 'assets/image/Aniq.jpg', '$2y$10$bKtDUEUVWQoYrYpr.jP1eOni6I.cNfczSmYFbZ9x0ijusKTAxie3S', 'Active', 'No', '0145567290', 'CJS9587'),
('CJS4611', 'Jackson', 'jacksonlim@gmail.com', 'assets/image/rider message.png', '$2y$10$p6ZEi0t4nHE45fmyz.KweuF3cbAIx0pHKPuZ7nD/HQ4C2CZhmzGQa', 'Active', 'No', '0187734782', 'CJS9587'),
('CJS4615', 'Fadilah', 'fadilahhhh@gmail.com', 'assets/image/Ahmad.jpg', '$2y$10$.xUwt64D6BjlyYHj9fp5w.bDECHb09AAmBrYMD3LW7yO.9jY034XG', 'Active', 'Yes', '0189909877', ''),
('CJS545', 'Farilah', 'mmfarilah45@gmail.com', 'assets/image/add-user.png', '$2y$10$v9sx1vLrBZLT0Ot2438GRO4NeIkiPTKjiq2svEFsUgyrYZ48.GtzO', 'Active', 'No', '0198898765', 'CJS9587'),
('CJS6557', 'Nurul Ilma', 'lionatok08@gmail.com', 'Nurul Ilma.jpg', '$2y$10$bDUjlpVY3nFpA1uNRlIUX.mrWwyqGKIwSsq7dQJiwsg/Bvp8MHWZO', 'Active', 'No', '0176765434', 'CJS9587'),
('CJS7441', 'Ahmad', 'ahmad@gmail.com', 'Ahmad.jpg', '$2y$10$B7MUvTDTjZjJB9q7TcoyyuF4epmjkFSm3yljiaY1ZLTBV9a.qNGB.', 'Active', 'No', '0187734782', 'CJS9587'),
('CJS7900', 'Alisa', 'tan00alisa@gmail.com', 'assets/image/hl.png', '$2y$10$KmbZ9jwpj0FuDRyRfuyFaOMV.LJZeTua/iTsdfNLfx17CvgL6g7Ra', 'Active', 'No', '0187734782', 'CJS9587'),
('CJS8284', 'Radilah', 'radilah190872@gmail.com', 'Aniq.jpg', '$2y$10$5GsNI.kAHwQDV96HEOW8v.FFqX7.iXzt.XzprSK/aH92ZS5hbq2y.', 'Active', 'No', '0187734782', 'CJS9587'),
('CJS8756', 'Eshley', 'eshley@gmail.com', 'assets/image/Eshley.jpg', '$2y$10$75UPRJ3S6rT74RIM7Sj44OQui4pfyx4eaW19iNZus.8/MdwJ9KdWm', 'Active', 'No', '0189909877', ''),
('CJS9111', 'Kalina', 'gohsien2004@gmail.com', 'assets/image/Eshley.jpg', '$2y$10$NGDbqWN1NvCqxxYcoVqrb.mu6DN7UWsJbqITF27knA3YfmFF0Eu0e', 'Inactive', 'No', '0187734782', 'CJS9587'),
('CJS9587', 'Sharonn', 'siewwen041008@gmail.com', 'sw.jpg', '$2y$10$nRssCU0xl8iQGPI1B4T9ieLwsL1mr76eJXWL.5jheMAvHp1ssbG1K', 'Active', 'Yes', '01988392784', ''),
('CJS9775', 'Amelia', 'ameliaa77@gmail.com', 'assets/image/Amelia.jpg', '$2y$10$m7YiorUJesIY1SyyrgSQkuNvQuTWTWcUnnpYxiNYiGO/V1Whgu19G', 'Active', 'No', '0167787654', 'CJS9587'),
('CJS9982', 'Maria', 'gohsiewwen2004@gmail.com', 'assets/image/maria.jpeg', '$2y$10$xwUXOXruGW0pwaxYjUVIZOScY6oLyfSR3BzKpgeSCKl2PCDCPAdc6', 'Active', 'No', '0187734782', 'CJS9587');

-- --------------------------------------------------------

--
-- Table structure for table `stripepay`
--

CREATE TABLE `stripepay` (
  `SP_ID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `card_num` bigint(20) NOT NULL,
  `card_cvc` int(5) NOT NULL,
  `card_exp_month` varchar(2) NOT NULL,
  `card_exp_year` varchar(5) NOT NULL,
  `paid_amount` varchar(10) NOT NULL,
  `paid_amount_currency` varchar(10) NOT NULL,
  `txn_id` varchar(100) NOT NULL,
  `payment_status` varchar(50) NOT NULL,
  `created` date NOT NULL DEFAULT current_timestamp(),
  `modified` time NOT NULL DEFAULT current_timestamp(),
  `PM_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `stripepay`
--

INSERT INTO `stripepay` (`SP_ID`, `name`, `email`, `card_num`, `card_cvc`, `card_exp_month`, `card_exp_year`, `paid_amount`, `paid_amount_currency`, `txn_id`, `payment_status`, `created`, `modified`, `PM_ID`) VALUES
(12, 'Goh Siew Wen', 'gohsiewwen2004@gmail.com', 5200828282828210, 254, '01', '27', '7983', 'myr', 'txn_3P8QswCwq26I0BhN15tva3Za', 'succeeded', '2024-04-22', '19:42:35', 1),
(13, 'Chin Yu Xuan', 'yuxuan888@gmail.com', 5200828282828210, 122, '04', '28', '7783', 'myr', 'txn_3P8RIVCwq26I0BhN1kJCTJct', 'succeeded', '2024-04-22', '20:08:59', 2),
(14, 'Goh Siew Wen', 'gohsiewwen2004@gmail.com', 5200828282828210, 234, '03', '27', '5418', 'myr', 'txn_3P8RWwCwq26I0BhN03TTE6L8', 'succeeded', '2024-04-22', '20:23:55', 3),
(15, 'uyty5e', '1221201625@student.mmu.edu.my', 5200828282828210, 345, '3', '26', '9552', 'myr', 'txn_3P91bWCwq26I0BhN05Tp7GpB', 'succeeded', '2024-04-24', '10:55:02', 4),
(16, 'Chin Yu Xuan', 'yuxuan9008@gmail.com', 5200828282828210, 123, '04', '28', '3090', 'myr', 'txn_3PLEpLCwq26I0BhN0r1ikT4A', 'succeeded', '2024-05-28', '01:27:48', 6),
(17, 'chin yuxuan', 'yuxuan9008@gmail.com', 5200828282828210, 123, '06', '27', '1163', 'myr', 'txn_3PMM1wCwq26I0BhN1Vfky8v1', 'succeeded', '2024-05-31', '03:21:25', 7),
(18, 'chin yuxuan', 'yuxuan9008@gmail.com', 5200828282828210, 123, '09', '25', '832', 'myr', 'txn_3PMM80Cwq26I0BhN0p0JnN5N', 'succeeded', '2024-05-31', '03:27:40', 8),
(19, 'chin yuxuan', 'yuxuan9008@gmail.com', 5200828282828210, 123, '06', '27', '6739', 'myr', 'txn_3PRldxCwq26I0BhN0Tq1NOgI', 'succeeded', '2024-06-15', '01:43:02', 9),
(20, 'chin yuxuan', 'yuxuan9008@gmail.com', 5200828282828210, 123, '09', '28', '16219', 'myr', 'txn_3PRlhBCwq26I0BhN0eKD1A9z', 'succeeded', '2024-06-15', '01:46:22', 10),
(21, 'chin yuxuan', 'yuxuan9008@gmail.com', 5200828282828210, 123, '07', '29', '3479', 'myr', 'txn_3PRliOCwq26I0BhN1Cvg2qbY', 'succeeded', '2024-06-15', '01:47:37', 11),
(22, '123456', '', 5200828282828210, 123, '09', '27', '9637', 'myr', 'txn_3PTJQlCwq26I0BhN1zgWUe0W', 'succeeded', '2024-06-19', '07:59:48', 13),
(23, 'ISew Wen', 'yuxuan9008@gmail.com', 5200828282828210, 123, '01', '26', '2964', 'myr', 'txn_3PTon9Cwq26I0BhN0gRaweli', 'succeeded', '2024-06-20', '17:29:00', 15),
(24, 'Chin', 'yuxuan9008@gmail.com', 5200828282828210, 123, '09', '27', '2282', 'myr', 'txn_3PUVUrCwq26I0BhN1IKzo45O', 'succeeded', '2024-06-22', '15:04:57', 16);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `W_ID` int(11) NOT NULL,
  `C_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`W_ID`, `C_ID`) VALUES
(1, 46),
(2, 50),
(3, 52);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist_item`
--

CREATE TABLE `wishlist_item` (
  `WI_ID` int(11) NOT NULL,
  `P_ID` int(11) NOT NULL,
  `W_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist_item`
--

INSERT INTO `wishlist_item` (`WI_ID`, `P_ID`, `W_ID`) VALUES
(6, 49, 1),
(7, 54, 1),
(10, 56, 1),
(11, 60, 1),
(12, 57, 2),
(13, 53, 2),
(14, 50, 2),
(15, 49, 2),
(16, 53, 1),
(17, 57, 1),
(20, 87, 1),
(21, 81, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`A_ID`),
  ADD KEY `C_ID` (`C_ID`);

--
-- Indexes for table `barista`
--
ALTER TABLE `barista`
  ADD PRIMARY KEY (`B_ID`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`CT_ID`),
  ADD KEY `C_ID` (`C_ID`),
  ADD KEY `Promo_ID` (`Promo_ID`);

--
-- Indexes for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD PRIMARY KEY (`CI_ID`),
  ADD KEY `CT_ID` (`CT_ID`),
  ADD KEY `P_ID` (`P_ID`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`CA_ID`);

--
-- Indexes for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD PRIMARY KEY (`Co_ID`),
  ADD KEY `Add_By` (`Add_By`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`C_ID`),
  ADD UNIQUE KEY `C_Email` (`C_Email`);

--
-- Indexes for table `customization`
--
ALTER TABLE `customization`
  ADD PRIMARY KEY (`Custom_ID`),
  ADD KEY `CC_ID` (`CC_ID`);

--
-- Indexes for table `customize_category`
--
ALTER TABLE `customize_category`
  ADD PRIMARY KEY (`CC_ID`);

--
-- Indexes for table `details`
--
ALTER TABLE `details`
  ADD PRIMARY KEY (`D_ID`),
  ADD KEY `c_item_id` (`c_item_id`),
  ADD KEY `customize_id` (`customize_id`);

--
-- Indexes for table `opt`
--
ALTER TABLE `opt`
  ADD PRIMARY KEY (`Opt_ID`),
  ADD KEY `CC_ID` (`CC_ID`),
  ADD KEY `P_ID` (`P_ID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`PM_ID`),
  ADD KEY `O_ID` (`O_ID`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`P_ID`),
  ADD KEY `P_Category` (`P_Category`);

--
-- Indexes for table `promo`
--
ALTER TABLE `promo`
  ADD PRIMARY KEY (`Promo_ID`);

--
-- Indexes for table `promo_history`
--
ALTER TABLE `promo_history`
  ADD PRIMARY KEY (`P_History_ID`),
  ADD KEY `Promo_ID` (`Promo_ID`),
  ADD KEY `Cus_ID` (`Cus_ID`);

--
-- Indexes for table `rate_product`
--
ALTER TABLE `rate_product`
  ADD PRIMARY KEY (`RP_ID`),
  ADD KEY `P_ID` (`P_ID`),
  ADD KEY `Ra_ID` (`Ra_ID`);

--
-- Indexes for table `rating`
--
ALTER TABLE `rating`
  ADD PRIMARY KEY (`Ra_ID`),
  ADD KEY `O_ID` (`O_ID`),
  ADD KEY `R_ID` (`R_ID`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`O_ID`),
  ADD UNIQUE KEY `CT_ID_2` (`CT_ID`),
  ADD KEY `R_ID` (`R_ID`),
  ADD KEY `A_ID` (`A_ID`);

--
-- Indexes for table `rider`
--
ALTER TABLE `rider`
  ADD PRIMARY KEY (`R_ID`),
  ADD UNIQUE KEY `R_Email` (`R_Email`),
  ADD KEY `R_SSID` (`R_SAID`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`S_ID`),
  ADD UNIQUE KEY `S_Email` (`S_Email`);

--
-- Indexes for table `stripepay`
--
ALTER TABLE `stripepay`
  ADD PRIMARY KEY (`SP_ID`),
  ADD KEY `P_ID` (`PM_ID`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`W_ID`),
  ADD KEY `C_ID` (`C_ID`);

--
-- Indexes for table `wishlist_item`
--
ALTER TABLE `wishlist_item`
  ADD PRIMARY KEY (`WI_ID`),
  ADD KEY `P_ID` (`P_ID`),
  ADD KEY `wishlist_item_ibfk_2` (`W_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `address`
--
ALTER TABLE `address`
  MODIFY `A_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `barista`
--
ALTER TABLE `barista`
  MODIFY `B_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `CT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `cart_item`
--
ALTER TABLE `cart_item`
  MODIFY `CI_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `CA_ID` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `contact_us`
--
ALTER TABLE `contact_us`
  MODIFY `Co_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `C_ID` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `customization`
--
ALTER TABLE `customization`
  MODIFY `Custom_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `customize_category`
--
ALTER TABLE `customize_category`
  MODIFY `CC_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `details`
--
ALTER TABLE `details`
  MODIFY `D_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT for table `opt`
--
ALTER TABLE `opt`
  MODIFY `Opt_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=357;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `PM_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `P_ID` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `promo`
--
ALTER TABLE `promo`
  MODIFY `Promo_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `promo_history`
--
ALTER TABLE `promo_history`
  MODIFY `P_History_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `rate_product`
--
ALTER TABLE `rate_product`
  MODIFY `RP_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `rating`
--
ALTER TABLE `rating`
  MODIFY `Ra_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `O_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `stripepay`
--
ALTER TABLE `stripepay`
  MODIFY `SP_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `W_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wishlist_item`
--
ALTER TABLE `wishlist_item`
  MODIFY `WI_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `address`
--
ALTER TABLE `address`
  ADD CONSTRAINT `address_ibfk_1` FOREIGN KEY (`C_ID`) REFERENCES `customer` (`C_ID`);

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`C_ID`) REFERENCES `customer` (`C_ID`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`Promo_ID`) REFERENCES `promo` (`Promo_ID`);

--
-- Constraints for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD CONSTRAINT `cart_item_ibfk_1` FOREIGN KEY (`CT_ID`) REFERENCES `cart` (`CT_ID`),
  ADD CONSTRAINT `cart_item_ibfk_2` FOREIGN KEY (`P_ID`) REFERENCES `product` (`P_ID`);

--
-- Constraints for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD CONSTRAINT `contact_us_ibfk_1` FOREIGN KEY (`Add_By`) REFERENCES `staff` (`S_ID`);

--
-- Constraints for table `customization`
--
ALTER TABLE `customization`
  ADD CONSTRAINT `customization_ibfk_1` FOREIGN KEY (`CC_ID`) REFERENCES `customize_category` (`CC_ID`);

--
-- Constraints for table `details`
--
ALTER TABLE `details`
  ADD CONSTRAINT `details_ibfk_1` FOREIGN KEY (`c_item_id`) REFERENCES `cart_item` (`CI_ID`),
  ADD CONSTRAINT `details_ibfk_2` FOREIGN KEY (`customize_id`) REFERENCES `customization` (`Custom_ID`);

--
-- Constraints for table `opt`
--
ALTER TABLE `opt`
  ADD CONSTRAINT `opt_ibfk_1` FOREIGN KEY (`CC_ID`) REFERENCES `customize_category` (`CC_ID`),
  ADD CONSTRAINT `opt_ibfk_2` FOREIGN KEY (`P_ID`) REFERENCES `product` (`P_ID`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`O_ID`) REFERENCES `reservation` (`O_ID`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`P_Category`) REFERENCES `category` (`CA_ID`);

--
-- Constraints for table `promo_history`
--
ALTER TABLE `promo_history`
  ADD CONSTRAINT `promo_history_ibfk_1` FOREIGN KEY (`Promo_ID`) REFERENCES `promo` (`Promo_ID`),
  ADD CONSTRAINT `promo_history_ibfk_2` FOREIGN KEY (`Cus_ID`) REFERENCES `customer` (`C_ID`);

--
-- Constraints for table `rate_product`
--
ALTER TABLE `rate_product`
  ADD CONSTRAINT `rate_product_ibfk_1` FOREIGN KEY (`P_ID`) REFERENCES `product` (`P_ID`),
  ADD CONSTRAINT `rate_product_ibfk_2` FOREIGN KEY (`Ra_ID`) REFERENCES `rating` (`Ra_ID`);

--
-- Constraints for table `rating`
--
ALTER TABLE `rating`
  ADD CONSTRAINT `rating_ibfk_1` FOREIGN KEY (`O_ID`) REFERENCES `reservation` (`O_ID`),
  ADD CONSTRAINT `rating_ibfk_2` FOREIGN KEY (`R_ID`) REFERENCES `rider` (`R_ID`);

--
-- Constraints for table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`CT_ID`) REFERENCES `cart` (`CT_ID`),
  ADD CONSTRAINT `reservation_ibfk_3` FOREIGN KEY (`R_ID`) REFERENCES `rider` (`R_ID`),
  ADD CONSTRAINT `reservation_ibfk_4` FOREIGN KEY (`A_ID`) REFERENCES `address` (`A_ID`);

--
-- Constraints for table `rider`
--
ALTER TABLE `rider`
  ADD CONSTRAINT `rider_ibfk_1` FOREIGN KEY (`R_SAID`) REFERENCES `staff` (`S_ID`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`C_ID`) REFERENCES `customer` (`C_ID`);

--
-- Constraints for table `wishlist_item`
--
ALTER TABLE `wishlist_item`
  ADD CONSTRAINT `wishlist_item_ibfk_1` FOREIGN KEY (`P_ID`) REFERENCES `product` (`P_ID`),
  ADD CONSTRAINT `wishlist_item_ibfk_2` FOREIGN KEY (`W_ID`) REFERENCES `wishlist` (`W_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
