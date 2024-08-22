-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 22, 2024 at 10:52 AM
-- Server version: 8.0.31
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `labachine`
--

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
CREATE TABLE IF NOT EXISTS `config` (
  `configID` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `value` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  PRIMARY KEY (`configID`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `config`
--

INSERT INTO `config` (`configID`, `name`, `value`, `description`) VALUES
(1, 'Daily sms', '2023-12-13', 'Sending daily sms for ready orders'),
(2, 'Dsr Sms', '1', '0 if you want dsr sms decativate 1 means activate'),
(3, 'Primary No.', '09197901830', ''),
(4, 'Secondary No.', '09177776718', ''),
(5, 'Max Load', '7', ''),
(6, 'Min Load', '6', ''),
(7, 'Wash Points', '1', 'This is wash points'),
(8, 'Dry Points', '1', 'This is for dry points'),
(9, 'Fold Points', '5', 'this is for fold points'),
(10, 'Remarks', 'Do not spray, Separate White, Do not bleach', 'List of Remarks'),
(11, 'Sms Status', '0', ''),
(12, 'Student Button', '0', '1: Enabled 0: Disabled');

-- --------------------------------------------------------

--
-- Table structure for table `detailed_dsr`
--

DROP TABLE IF EXISTS `detailed_dsr`;
CREATE TABLE IF NOT EXISTS `detailed_dsr` (
  `dsrID` int NOT NULL AUTO_INCREMENT,
  `ds_cash` float NOT NULL,
  `ds_gcash` float NOT NULL,
  `ds_unpaid` float NOT NULL,
  `ds_total` float NOT NULL,
  `col_cash` float NOT NULL,
  `col_gcash` float NOT NULL,
  `col_total` float NOT NULL,
  `item_cash` float NOT NULL,
  `item_gcash` float NOT NULL,
  `item_total` float NOT NULL,
  `total_cash` float NOT NULL,
  `total_gcash` float NOT NULL,
  `total_expenses` float NOT NULL,
  `expected_cash` float NOT NULL,
  `remittance` float NOT NULL,
  `variance` float NOT NULL,
  `sales_date` datetime NOT NULL,
  `date_created` datetime NOT NULL,
  `shift` tinyint NOT NULL,
  `userID` int NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `dateSettled` datetime NOT NULL,
  `varSettledAmt` float NOT NULL,
  PRIMARY KEY (`dsrID`),
  KEY `userID` (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `dsr`
--

DROP TABLE IF EXISTS `dsr`;
CREATE TABLE IF NOT EXISTS `dsr` (
  `dsrID` int NOT NULL AUTO_INCREMENT,
  `salesDate` date NOT NULL,
  `totalCash` float NOT NULL,
  `totalGcash` float NOT NULL,
  `totalSales` float NOT NULL,
  `totalExpenses` float NOT NULL,
  `totalUnpaid` float NOT NULL,
  `totalCollection` float NOT NULL,
  `cash_collection` float NOT NULL,
  `gcash_collection` float NOT NULL,
  `inventorySales` float NOT NULL,
  `cash_inventorySales` float NOT NULL,
  `gcash_inventorySales` float NOT NULL,
  `actualCash` float NOT NULL,
  `variance` float NOT NULL,
  `userID` int NOT NULL,
  `checkBy` varchar(60) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`dsrID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
CREATE TABLE IF NOT EXISTS `expenses` (
  `expID` int NOT NULL AUTO_INCREMENT,
  `particular` varchar(100) NOT NULL,
  `amount` float NOT NULL,
  `dateCreated` datetime NOT NULL,
  `expDate` date NOT NULL,
  `createdBy` varchar(36) NOT NULL,
  `allowance` tinyint NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`expID`),
  KEY `createdBy` (`createdBy`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
CREATE TABLE IF NOT EXISTS `items` (
  `itemID` int NOT NULL AUTO_INCREMENT,
  `description` varchar(50) NOT NULL,
  `price` float NOT NULL,
  `cost` float NOT NULL,
  `qty` int NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`itemID`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`itemID`, `description`, `price`, `cost`, `qty`, `status`) VALUES
(4, 'KOPIKO LUCKY DAY', 30, 21.75, 0, 1),
(5, 'MINUTE MAID', 15, 10.1, 24, 1),
(6, 'CALI', 40, 0, 0, 1),
(7, 'SPRITE LEMON CAN', 40, 32.3, 0, 1),
(8, 'COKE LIGHT CAN', 40, 33.1, 0, 1),
(9, 'COKE ORIGINAL CAN', 40, 0, 4, 1),
(10, 'COKE ZERO CAN', 40, 33.1, 0, 1),
(11, 'SPRITE MISMO', 25, 0, 12, 1),
(12, 'COKE MISMO', 25, 0, 12, 1),
(13, 'SPRITE SAKTO', 15, 10, 24, 1),
(14, 'COKE SAKTO', 15, 10, 0, 1),
(15, 'ROYAL SAKTO', 15, 10, 0, 1),
(16, 'PINEAPPLE JUICE', 40, 0, 0, 1),
(17, 'SOJU GRAPEFRUIT', 100, 0, 0, 1),
(18, 'FUDGEE BAR', 10, 7.89, 20, 1),
(19, 'Magic Chips', 10, 7.35, 0, 1),
(20, 'YAKULT', 15, 10, 0, 1),
(21, 'OISHI', 10, 6.4, 0, 1),
(22, 'C2 SOLO', 20, 14, 44, 1),
(23, 'ROYAL CAN', 40, 33.1, 0, 1),
(24, 'SPRITE CAN', 40, 33.1, 0, 1),
(25, 'COKE VANILLA CAN', 40, 33.1, 0, 1),
(26, 'NOVA', 20, 16.35, 5, 1),
(27, 'PIATTOS', 20, 16.3, 0, 1),
(28, 'BREAD PAN', 10, 6.7, 0, 1),
(29, 'CORN BITS', 20, 14.05, 0, 1),
(30, 'NESCAFE ORIGINAL', 15, 0, 0, 1),
(31, 'KOPIKO BROWN COFFE', 15, 7.3, 40, 1),
(32, 'ROYAL MISMO', 25, 15.33, 0, 1),
(33, 'NATURE SPRING 350ML', 15, 7.8, 0, 1),
(34, 'GO FRESH WATER', 20, 0, 0, 1),
(35, 'Nescafe stick with cup', 12, 0, 0, 1),
(36, 'Refresh Water', 15, 15, 24, 1);

-- --------------------------------------------------------

--
-- Table structure for table `job_orders`
--

DROP TABLE IF EXISTS `job_orders`;
CREATE TABLE IF NOT EXISTS `job_orders` (
  `joID` int NOT NULL AUTO_INCREMENT,
  `qrCode` varchar(36) NOT NULL,
  `joNo` tinyint NOT NULL,
  `transID` int NOT NULL,
  `washerNo` tinyint NOT NULL,
  `washDate` datetime NOT NULL,
  `washBy` int NOT NULL,
  `dryerNo` tinyint NOT NULL,
  `dryDate` datetime NOT NULL,
  `dryBy` tinyint NOT NULL,
  `foldDate` datetime NOT NULL,
  `foldBy` int NOT NULL,
  `readyDate` datetime NOT NULL,
  `readyBy` int NOT NULL,
  `rackNo` tinyint NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`joID`),
  KEY `qrCode` (`qrCode`,`joNo`,`transID`,`washBy`,`dryBy`,`foldBy`,`readyBy`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `laundry_price`
--

DROP TABLE IF EXISTS `laundry_price`;
CREATE TABLE IF NOT EXISTS `laundry_price` (
  `priceID` int NOT NULL AUTO_INCREMENT,
  `laundry_order` tinyint NOT NULL,
  `category` enum('Regular','Student','DIY Regular','DIY Student','Express Regular','Express Student') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'Regular',
  `kilo` float NOT NULL,
  `comforter` float NOT NULL,
  `detergent` float NOT NULL,
  `bleach` float NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`priceID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `laundry_price`
--

INSERT INTO `laundry_price` (`priceID`, `laundry_order`, `category`, `kilo`, `comforter`, `detergent`, `bleach`, `status`) VALUES
(1, 1, 'Regular', 30, 180, 30, 10, 1),
(2, 4, 'DIY Regular', 25, 180, 30, 10, 1),
(3, 2, 'Student', 25, 170, 30, 10, 1),
(4, 5, 'DIY Student', 20, 170, 30, 10, 1),
(5, 3, 'Express Regular', 40, 200, 30, 10, 1),
(6, 6, 'Express Student', 35, 190, 30, 10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `loginlogs`
--

DROP TABLE IF EXISTS `loginlogs`;
CREATE TABLE IF NOT EXISTS `loginlogs` (
  `lid` int NOT NULL AUTO_INCREMENT,
  `host` varchar(20) NOT NULL,
  `hostname` varchar(30) NOT NULL,
  `userID` int NOT NULL,
  `date` datetime NOT NULL,
  `operation` varchar(15) NOT NULL,
  `logs` varchar(10) NOT NULL,
  PRIMARY KEY (`lid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
CREATE TABLE IF NOT EXISTS `sales` (
  `salesID` int NOT NULL AUTO_INCREMENT,
  `itemID` int NOT NULL,
  `description` varchar(60) NOT NULL,
  `qty` tinyint NOT NULL,
  `price` float NOT NULL,
  `itemCost` float NOT NULL,
  `amount` float NOT NULL,
  `valeBy` int DEFAULT NULL,
  `paymentMethod` enum('Cash','Gcash','None') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'Cash',
  `referenceNo` varchar(20) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `salesDate` datetime DEFAULT NULL,
  `userID` int NOT NULL,
  `cashier` int NOT NULL,
  PRIMARY KEY (`salesID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `sale_details`
--

DROP TABLE IF EXISTS `sale_details`;
CREATE TABLE IF NOT EXISTS `sale_details` (
  `detailID` int NOT NULL AUTO_INCREMENT,
  `headerID` int NOT NULL,
  `itemID` int NOT NULL,
  `description` varchar(60) NOT NULL,
  `price` float NOT NULL,
  `qty` tinyint NOT NULL,
  `itemAmount` float NOT NULL,
  `dateCreated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userID` int NOT NULL,
  PRIMARY KEY (`detailID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `sale_headers`
--

DROP TABLE IF EXISTS `sale_headers`;
CREATE TABLE IF NOT EXISTS `sale_headers` (
  `headerID` int NOT NULL AUTO_INCREMENT,
  `totalAmount` float NOT NULL,
  `amountPaid` float NOT NULL,
  `cash` float NOT NULL,
  `gCash` float NOT NULL,
  `cashChange` float NOT NULL,
  `balance` float NOT NULL,
  `dateCreated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userID` int NOT NULL,
  PRIMARY KEY (`headerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `sms`
--

DROP TABLE IF EXISTS `sms`;
CREATE TABLE IF NOT EXISTS `sms` (
  `smsID` int NOT NULL AUTO_INCREMENT,
  `transID` int NOT NULL,
  `customer` varchar(100) NOT NULL,
  `mobile` varchar(12) NOT NULL,
  `message` text NOT NULL,
  `status` varchar(10) NOT NULL,
  `jostatus` varchar(10) NOT NULL,
  `response` varchar(60) NOT NULL,
  `dateSent` datetime NOT NULL,
  PRIMARY KEY (`smsID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `stockcards`
--

DROP TABLE IF EXISTS `stockcards`;
CREATE TABLE IF NOT EXISTS `stockcards` (
  `stockID` int NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `itemID` int NOT NULL,
  `begBal` float NOT NULL,
  `debit` float NOT NULL,
  `credit` float NOT NULL,
  `endBal` float NOT NULL,
  `refNo` varchar(50) NOT NULL,
  `remarks` varchar(150) NOT NULL,
  `insertedBy` int NOT NULL,
  PRIMARY KEY (`stockID`),
  KEY `date` (`date`,`itemID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE IF NOT EXISTS `transactions` (
  `transID` int NOT NULL AUTO_INCREMENT,
  `qrCode` varchar(36) NOT NULL,
  `customer` varchar(50) NOT NULL,
  `mobile` varchar(11) NOT NULL,
  `tranType` enum('Regular','Student','DIY Regular','DIY Student','Express Regular','Express Student') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'Regular',
  `kiloQty` tinyint NOT NULL,
  `kiloPrice` float NOT NULL,
  `kiloAmount` float NOT NULL,
  `comforterLoad` tinyint NOT NULL,
  `comforterPrice` float NOT NULL,
  `comforterAmount` float NOT NULL,
  `detergentSet` tinyint NOT NULL,
  `detergentPrice` float NOT NULL,
  `detergentAmount` int NOT NULL,
  `bleachLoad` tinyint NOT NULL,
  `bleachPrice` float NOT NULL,
  `bleachAmount` float NOT NULL,
  `totalAmount` float NOT NULL,
  `amountPaid` float NOT NULL,
  `paymentMethod` enum('Cash','GCash','Cash/GCash') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'Cash',
  `referenceNo` varchar(13) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `balance` float NOT NULL,
  `cash` float NOT NULL,
  `gCash` float NOT NULL,
  `cashChange` float NOT NULL,
  `loads` tinyint NOT NULL,
  `washerNo` tinyint NOT NULL,
  `dryerNo` tinyint NOT NULL,
  `dateCreated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `shirts` tinyint NOT NULL,
  `pants` tinyint NOT NULL,
  `undies` tinyint NOT NULL,
  `socks` tinyint NOT NULL,
  `mixedItems` tinyint NOT NULL,
  `dateSorted` datetime NOT NULL,
  `sortedBy` int NOT NULL,
  `dateWashed` datetime NOT NULL,
  `washedBy` int NOT NULL,
  `dateDried` datetime NOT NULL,
  `driedBy` int NOT NULL,
  `dateFolded` datetime NOT NULL,
  `foldedBy` int NOT NULL,
  `dateReady` datetime NOT NULL,
  `readyBy` int NOT NULL,
  `dateReleased` datetime NOT NULL,
  `releasedBy` int NOT NULL,
  `canceledBy` int NOT NULL,
  `dateCanceled` datetime NOT NULL,
  `canceledRemarks` text NOT NULL,
  `payment1Cash` float NOT NULL,
  `payment1GCash` float NOT NULL,
  `payment1Cashier` int NOT NULL,
  `payment1Date` datetime NOT NULL,
  `payment1Method` enum('Cash','GCash','Cash/GCash') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'Cash',
  `payment1ReferenceNo` varchar(13) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `payment2Cash` float NOT NULL,
  `payment2GCash` float NOT NULL,
  `payment2Cashier` int NOT NULL,
  `payment2Date` datetime NOT NULL,
  `payment2Method` enum('Cash','GCash','Cash/GCash') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'Cash',
  `payment2ReferenceNo` varchar(13) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `totalLoads` tinyint NOT NULL,
  `isSms` tinyint NOT NULL DEFAULT '0' COMMENT '1 Means true',
  `userID` int NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `remarks` varchar(100) DEFAULT NULL,
  `orNo` float NOT NULL,
  PRIMARY KEY (`transID`),
  KEY `qrCode` (`qrCode`),
  KEY `releasedBy` (`releasedBy`,`canceledBy`,`payment1Cashier`,`payment2Cashier`,`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `userID` int NOT NULL AUTO_INCREMENT,
  `firstName` varchar(60) NOT NULL,
  `lastName` varchar(60) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(36) NOT NULL,
  `userType` enum('Cashier','Admin','Staff') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'Cashier',
  `lastLogin` datetime NOT NULL,
  `lastLogout` datetime NOT NULL,
  `isDsr` tinyint NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `empID` int NOT NULL,
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `firstName`, `lastName`, `username`, `password`, `userType`, `lastLogin`, `lastLogout`, `isDsr`, `status`, `empID`) VALUES
(1, 'Jefferson', 'Pimentel', 'jeff', 'e10adc3949ba59abbe56e057f20f883e', 'Admin', '2024-08-19 14:14:55', '2024-08-19 14:03:19', 0, 1, 0),
(4, 'Nemrose', 'Toca', 'rose', 'e10adc3949ba59abbe56e057f20f883e', 'Staff', '2024-08-19 10:00:28', '2024-08-19 11:06:51', 1, 1, 0),
(6, 'Marfe Jane', 'Cabug-os', 'jane', 'e10adc3949ba59abbe56e057f20f883e', 'Cashier', '2024-08-19 09:32:56', '2024-08-19 09:53:09', 1, 1, 2),
(7, 'Lecil', 'Bamoya', 'lecil', '7854cdd6a417484ba983016101882c92', 'Cashier', '2024-08-08 07:10:47', '2024-08-08 15:41:07', 1, 1, 4),
(8, 'Resyl', 'Dacua', 'resyl', 'e10adc3949ba59abbe56e057f20f883e', 'Admin', '2024-08-02 08:23:09', '2024-08-02 08:27:31', 0, 1, 0),
(9, 'Rodgin', 'Misterio', 'rodgin', '919d756163ba1b7da96c1feeca6f4983', 'Staff', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 1, 0),
(10, 'CJ', 'Ferwelo', 'cj', '28198b369067e88dab9fefe85484dbf4', 'Cashier', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 1, 7),
(11, 'Beng', 'Montaño', 'jocelyn', 'be79583f5180581691d1d0be1699b621', 'Cashier', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 1, 3),
(12, 'Jocemae', 'Clarito', 'jocemae', 'ad5a2beac47773ece8fda62567584fc5', 'Cashier', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 1, 6),
(13, 'Jovelyn', 'Gorgod', 'jovelyn', 'beb1884e62787fc41d904780c9544fa5', 'Cashier', '2024-08-08 15:42:14', '2024-08-08 20:59:43', 1, 1, 5),
(14, 'Jessa', 'Arabis', 'jessa', 'a5b85dcc021937f1fb0148939ede8cf3', 'Cashier', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 1, 8),
(15, 'Josephine', 'Salibay', 'josephine', '', 'Cashier', '2024-01-02 12:03:20', '2024-01-02 12:03:20', 0, 1, 9),
(16, 'Grace', 'Espino', 'marygrace', '', 'Cashier', '2024-01-18 10:25:04', '2024-01-18 10:25:04', 0, 1, 10);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
