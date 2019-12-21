-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 14, 2019 at 01:02 PM
-- Server version: 10.1.40-MariaDB-cll-lve
-- PHP Version: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `techzhhd_naijasubweb`
--

-- --------------------------------------------------------

--
-- Table structure for table `cashoutrequest`
--

CREATE TABLE `cashoutrequest` (
  `id` int(11) NOT NULL,
  `receiver_address` varchar(255) DEFAULT NULL,
  `amount` double UNSIGNED NOT NULL,
  `paid` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `extrauserinfo`
--

CREATE TABLE `extrauserinfo` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `extrauserinfo`
--

INSERT INTO `extrauserinfo` (`id`, `name`, `username`, `password`, `email`, `phone`) VALUES
(1, 'Aremu Adeolu Edward', 'Adeolu315@gmail.com', '087afcfd3730c0f044e543fb7d447b30', 'Adeolu315@gmail.com', '2348109223940'),
(2, 'Oluwasegun Adesina', 'StayconTelecoms', 'f734981a5e3d84035478e77630f06210', 'Vicshegs10@gmail.com', '2348148115029'),
(3, 'Stephen Adeniran', 'adeniranstephen2@gmail.com', 'abe55d92c2a0dfbe57cffcc54d2aa5ab', 'adeniranstephen2@gmail.com', '2348068718492'),
(4, 'Joshua femi ', 'wireless446', 'f603c13e7a6c2cefc2810a99a0890930', 'phemyj46@gmail.com', '2348165651113'),
(5, 'Ogundana Oluwasola', 'Ogundana1', '819cbe19321fcb5acbe3de117aa675aa', 'ogundanasola@yahoo.com', '2347033906624'),
(6, 'Adegbusi Dare', 'Pastorial', '4c6c028f27843feba7827cada54709ea', 'Dareadegbusi@yahoo.com', '234001'),
(7, 'Funke', 'Efkay', '41c28aef631a97d5d7480125234f8c9f', 'funkebello09@gmail.com', '2347034830865'),
(8, 'abdullahi HUSSAINI', 'xprince91', '159d91079ff52c710612b1cc94f6ca69', 'paysclone@gmail.com', '2347051519461'),
(11, ' Dare Emmanuel', 'Apastorial', '4c6c028f27843feba7827cada54709ea', 'dareadegbusi@gmail.com', '234002'),
(12, 'Nduka Sunday Chinwike', 'Nduka45', '891534902077e5756336e1880b222d2d', 'mcsunny4real@gmail.com', '2348065861058'),
(13, 'Ebube Marcellinus ', 'Ebumars ', '3b786f2601963b2e952d81cc8bc6e954', 'ebumars4christ@gmail.com', '2348056597595'),
(14, 'Sola Oladosu', 'Shoaze', '7bc56d3c65326e9c7452a9f7266d15db', 'shoaze2010@gmail.com', '2348069154235'),
(23, 'Tele Ham', NULL, '$2y$11$DAExykHm6W8VVoqvRlrt9Oe1xxeOOrqcdnjB7kqLz9C8lAHMIH0Sm', 'tele@gmail.com', '08107926081'),
(22, 'Akindutire', 'akindutire', '87b06d9734d3f6af87315d55cdb38084', 'akindutireayo@yahoo.com', '08107926083'),
(24, 'OLATUNDE Segun', 'tele@gmail.com', '78857e755b612e043b2d9e17c5f0cec6', 'olatshege@gmail.com', '08142384174');

-- --------------------------------------------------------

--
-- Table structure for table `mailinglist`
--

CREATE TABLE `mailinglist` (
  `email` varchar(255) NOT NULL,
  `isEnabled` int(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `mailinglist`
--

INSERT INTO `mailinglist` (`email`, `isEnabled`) VALUES
('akindutireayo@yahoo.com', 1),
('olatshege@gmail.com', 1);

-- --------------------------------------------------------

--
-- Table structure for table `membership_plan`
--

CREATE TABLE `membership_plan` (
  `id` int(11) NOT NULL,
  `tag` varchar(255) NOT NULL,
  `level` int(11) DEFAULT NULL,
  `discount` double UNSIGNED DEFAULT NULL,
  `cost` double UNSIGNED NOT NULL,
  `reward` double UNSIGNED NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `membership_plan`
--

INSERT INTO `membership_plan` (`id`, `tag`, `level`, `discount`, `cost`, `reward`, `updated_at`, `created_at`) VALUES
(1, 'STARTER', 1, 0, 100, 0, '2019-02-16 13:54:34', '2019-02-16 13:52:34'),
(2, 'RESELLER', 2, 2, 1500, 0, '2019-04-10 15:16:29', '2019-02-16 13:55:27'),
(3, 'DEALER', 3, 5, 5000, 0, '2019-04-10 15:16:39', '2019-02-16 13:56:03');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `ptype` int(11) DEFAULT NULL,
  `pcat` int(11) DEFAULT NULL,
  `pname` varchar(255) DEFAULT NULL,
  `pcost` double UNSIGNED DEFAULT NULL,
  `pcurrency` varchar(4) NOT NULL,
  `pdesc` text,
  `pdiscount` double NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `ptype`, `pcat`, `pname`, `pcost`, `pcurrency`, `pdesc`, `pdiscount`, `created_at`) VALUES
(1, 1, 1, '1GB', 470, 'NGN', '', 0, '2019-04-07 12:36:15'),
(12, 1, 2, '2GB', 850, 'NGN', NULL, 0, '2019-03-25 02:57:32'),
(15, 1, 1, '2GB', 940, 'NGN', NULL, 0, '2019-04-07 12:36:25'),
(16, 1, 1, '3GB', 1410, 'NGN', NULL, 0, '2019-04-07 12:37:03'),
(17, 1, 1, '4GB', 1880, 'NGN', NULL, 0, '2019-04-07 12:37:25'),
(18, 1, 1, '5GB', 2350, 'NGN', NULL, 0, '2019-04-07 12:37:50'),
(19, 1, 1, '10GB', 4700, 'NGN', NULL, 0, '2019-04-07 12:38:03'),
(20, 1, 3, '1GB', 650, 'NGN', NULL, 0, '2019-03-25 02:53:05'),
(21, 1, 3, '2GB', 1300, 'NGN', NULL, 0, '2019-03-25 02:53:23'),
(22, 1, 3, '3GB', 1950, 'NGN', NULL, 0, '2019-03-25 02:53:40'),
(23, 1, 3, '4GB', 2300, 'NGN', NULL, 0, '2019-03-25 02:54:02'),
(24, 1, 3, '5GB', 3200, 'NGN', NULL, 0, '2019-03-25 02:54:19'),
(25, 1, 3, '10GB', 6000, 'NGN', NULL, 0, '2019-03-25 02:54:36'),
(26, 1, 4, '500MB', 440, 'NGN', NULL, 0, '2019-03-25 02:55:09'),
(27, 1, 4, '1GB', 900, 'NGN', NULL, 0, '2019-03-25 02:55:25'),
(28, 1, 4, '1.5GB', 1080, 'NGN', NULL, 0, '2019-03-25 02:55:48'),
(29, 1, 4, '2.5GB', 1800, 'NGN', NULL, 0, '2019-03-25 02:56:38'),
(30, 1, 4, '4GB', 2700, 'NGN', NULL, 0, '2019-03-25 02:56:19'),
(31, 1, 4, '5.5GB', 3600, 'NGN', NULL, 0, '2019-03-25 02:57:02'),
(32, 1, 2, '4.5GB', 1700, 'NGN', NULL, 0, '2019-03-25 02:58:00'),
(33, 1, 2, '7.2GB', 2125, 'NGN', NULL, 0, '2019-03-25 02:58:21'),
(34, 1, 2, '8GB', 2550, 'NGN', NULL, 0, '2019-03-25 02:58:37'),
(35, 1, 2, '12.5GB', 3400, 'NGN', NULL, 0, '2019-03-25 02:58:54'),
(36, 1, 2, '15.6GB', 4250, 'NGN', NULL, 0, '2019-03-25 02:59:11'),
(37, 1, 2, '25GB', 6800, 'NGN', NULL, 0, '2019-03-25 02:59:32'),
(38, 1, 5, '1.5GB', 950, 'NGN', NULL, 0, '2019-03-25 05:40:45'),
(39, 1, 5, '3.5GB', 1900, 'NGN', NULL, 0, '2019-03-25 05:40:59'),
(40, 1, 5, '5GB', 2375, 'NGN', NULL, 0, '2019-03-25 05:41:14'),
(41, 1, 5, '7.5GB', 3450, 'NGN', NULL, 0, '2019-03-25 05:41:29');

-- --------------------------------------------------------

--
-- Table structure for table `product_cat`
--

CREATE TABLE `product_cat` (
  `id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `cat` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `product_cat`
--

INSERT INTO `product_cat` (`id`, `type_id`, `cat`) VALUES
(1, 1, 'MTN'),
(2, 1, 'GLO'),
(3, 1, '9Mobile SME '),
(4, 1, '9Mobile Gifting'),
(5, 1, 'AIRTEL');

-- --------------------------------------------------------

--
-- Table structure for table `product_type`
--

CREATE TABLE `product_type` (
  `id` int(11) NOT NULL,
  `type` varchar(15) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `product_type`
--

INSERT INTO `product_type` (`id`, `type`) VALUES
(1, 'DATA BUNDLE');

-- --------------------------------------------------------

--
-- Table structure for table `pwdmutationlock`
--

CREATE TABLE `pwdmutationlock` (
  `email` varchar(100) NOT NULL,
  `msg` varchar(255) NOT NULL,
  `created_at` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `salespoint`
--

CREATE TABLE `salespoint` (
  `id` int(11) NOT NULL,
  `trade_key` varchar(100) NOT NULL,
  `trade_type` varchar(255) NOT NULL,
  `ifrom_address` text NOT NULL,
  `ito_address` text NOT NULL,
  `valueorqtyexchanged` double UNSIGNED NOT NULL,
  `rawamt` double UNSIGNED NOT NULL,
  `icurrency` varchar(4) NOT NULL,
  `extracharge` double UNSIGNED NOT NULL,
  `proofoftrade` varchar(255) DEFAULT NULL,
  `proofoftradeformat` varchar(255) DEFAULT NULL,
  `tradehistory` varchar(512) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `skey` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `type`, `skey`, `value`, `created_at`) VALUES
(1, 'REWARD', 'REFERRAL_BONUS', '2.0', '2019-03-26 00:06:39'),
(2, 'SERVICE_CHARGE', 'AIRTIME_RECHARGE_RATE', '0.00', '2019-04-10 15:26:12'),
(3, 'SERVICE_CHARGE', 'DATA_BUNDLE_RATE', '0.00', '2019-04-07 12:44:48'),
(10, 'EXCHANGE_RATE', 'BITCOIN_SELLING_RATE_IN_NGN', '350', '2019-04-10 14:23:55'),
(5, 'SERVICE_CHARGE', 'CASHOUT_CHARGE', '180', '2019-03-22 22:00:21'),
(6, 'RULE', 'MINIMUM_TOPUP', '100.00', '2019-03-16 15:38:53'),
(7, 'RULE', 'MAXIMUM_TOPUP_USING_BANK', '2000.00', '2019-03-16 15:38:47'),
(9, 'TOKEN', 'BILL_API_TOKEN', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiI1YzBiZDRjMjFkNGUwNTU3MjhjNmQyOWMiLCJleHAiOjE1NTQ4MTUyNTgwNTN9.bcgVe9GqC5XM26HbBpzNDwsBMZqhauu43Xvq60LXFWo', '2019-04-07 13:07:38'),
(12, 'SERVICE_CHARGE', 'TOPUP_VIA_CARD_RATE', '1.5', '2019-03-12 00:20:55'),
(11, 'EXCHANGE_RATE', 'BITCOIN_BUYING_RATE_IN_NGN', '340', '2019-04-10 14:23:47'),
(13, 'REWARD', 'DATA_BUNDLE_REWARD_RATE_ON_RESELLER_DOWNLINE', '1.5', '2019-04-10 14:23:47'),
(14, 'REWARD', 'DATA_BUNDLE_DISCOUNT_RATE_FOR_DEALER', '2.0', '2019-04-10 14:23:47'),
(15, 'SERVICE_CHARGE', 'TOPUP_VIA_AIRTIME', '15', '2019-03-12 00:20:55'),
(16, 'REWARD', 'CABLE_TV_DISCOUNT_RATE_FOR_DEALER', '0.5', '2019-04-10 14:23:47'),
(17, 'REWARD', 'AIRTIME_PURCHASE_DISCOUNT_RATE_FOR_DEALER', '0.5', '2019-04-10 14:23:47'),
(18, 'REWARD', 'REFERRAL_BONUS_DATA_BUNDLE_FOR_DEALER', '2.0', '2019-04-10 14:23:47'),
(19, 'SERVICE_CHARGE', 'MTN_AIRTIME_SELLING_CHARGE_RATE', '13.0', '2019-04-10 15:26:12'),
(20, 'SERVICE_CHARGE', '9MOBILE_AIRTIME_SELLING_CHARGE_RATE', '20.0', '2019-04-10 15:26:12'),
(21, 'SERVICE_CHARGE', 'GLO_AIRTIME_SELLING_CHARGE_RATE', '30.0', '2019-04-10 15:26:12'),
(22, 'SERVICE_CHARGE', 'AIRTEL_AIRTIME_SELLING_CHARGE_RATE', '25.0', '2019-04-10 15:40:20'),
(23, 'RULE', 'MAXIMUM_SELLING_OF_AIRTIME', '10000.00', '2019-03-16 15:38:47'),
(24, 'RULE', 'MINIMUM_SELLING_OF_AIRTIME', '500.00', '2019-03-16 15:38:47'),
(25, 'REWARD', 'MTN_AIRTIME_PURCHASE_DISCOUNT_RATE', '4.00', '2019-03-16 15:38:47'),
(26, 'REWARD', '9MOBILE_AIRTIME_PURCHASE_DISCOUNT_RATE', '4.00', '2019-03-16 15:38:47'),
(27, 'REWARD', 'AIRTEL_AIRTIME_PURCHASE_DISCOUNT_RATE', '4.00', '2019-03-16 15:38:47'),
(28, 'REWARD', 'GLO_AIRTIME_PURCHASE_DISCOUNT_RATE', '5.00', '2019-05-01 15:09:25'),
(29, 'NETWORK_PROVIDER', 'MTN', 'MTN', '2019-04-17 13:41:13'),
(30, 'NETWORK_PROVIDER', '9MOBILE', '9MOBILE', '2019-04-17 13:41:13'),
(31, 'NETWORK_PROVIDER', 'GLO', 'GLO', '2019-04-17 13:41:13'),
(32, 'NETWORK_PROVIDER', 'AIRTEL', 'AIRTEL', '2019-04-17 13:41:13');

-- --------------------------------------------------------

--
-- Table structure for table `topuprequest`
--

CREATE TABLE `topuprequest` (
  `id` int(11) NOT NULL,
  `bearer_address` varchar(100) NOT NULL,
  `mode` varchar(255) DEFAULT NULL,
  `amount` double UNSIGNED DEFAULT NULL,
  `slipidororderid` varchar(255) DEFAULT NULL,
  `bearer` varchar(255) DEFAULT NULL,
  `voucherpinorairtimepin` varchar(255) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `topuprequest`
--

INSERT INTO `topuprequest` (`id`, `bearer_address`, `mode`, `amount`, `slipidororderid`, `bearer`, `voucherpinorairtimepin`, `note`, `status`, `created_at`) VALUES
(1, 'd987322df1c017dbe1a33eb1ea4c067c7389d1b1808e3e9f75c6b81fcbaa397c', 'BANK', 2000, 'Ttttt', 'OLATUNDE Segun', NULL, 'Access ', 'CONFIRMED', '2019-04-07 12:59:37'),
(2, 'd987322df1c017dbe1a33eb1ea4c067c7389d1b1808e3e9f75c6b81fcbaa397c', 'BANK', 2000, 'Ttttt', 'OLATUNDE Segun', NULL, 'Access ', 'CONFIRMED', '2019-04-07 12:59:27');

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `id` int(11) NOT NULL,
  `previous_trans_hash` text NOT NULL,
  `trans_hash` text NOT NULL,
  `ifrom` varchar(255) DEFAULT NULL,
  `ito` varchar(255) DEFAULT NULL,
  `amt_exchanged` double UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `mode` varchar(255) NOT NULL,
  `from_ip` varchar(16) NOT NULL,
  `message` varchar(255) DEFAULT NULL,
  `nonce` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`id`, `previous_trans_hash`, `trans_hash`, `ifrom`, `ito`, `amt_exchanged`, `type`, `mode`, `from_ip`, `message`, `nonce`, `status`, `updated_at`, `created_at`) VALUES
(1, '', 'yØ(úJ‹P', 'Ù‡2-ñÀÛá£>±êL|s‰Ñ±€Ž>ŸuÆ¸Ëª9|', 'Ë‘ƒ*Ú¸\"w\0+\ZfÄ£\"*öÂÝ¿6B”Zù³ý4', 1500, 'ACCOUNT_UPGRADE', 'INTRA_WALLET', '41.190.3.246', 'sÝiÊ[¾¥1Ã\\¾¹0L¨[wrjù„Á}ð‹·Úþê³ÀñÂ8îÎùƒÒŽ‘ÚùÑ4', 'sn}8ÃÐúV‚ƒjß+A)¦(', 'CONFIRMED', '2019-04-07 21:42:00', '2019-04-07 21:42:00');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `user_type` varchar(255) NOT NULL,
  `isAdmin` int(1) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `referer` text,
  `membership_plan_id` int(11) DEFAULT NULL,
  `previous_plans` varchar(255) DEFAULT NULL,
  `hidden` int(1) DEFAULT NULL,
  `suspended` int(1) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `trans_lock` int(1) DEFAULT NULL,
  `gender` varchar(8) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `user_type`, `isAdmin`, `password`, `email`, `mobile`, `referer`, `membership_plan_id`, `previous_plans`, `hidden`, `suspended`, `photo`, `created_at`, `trans_lock`, `gender`) VALUES
(1, 'MEMBER', 0, '', 'akindutireayo@yahoo.com', '08107926083', 'NULL', 1, '1;', 0, 0, '1554679742IMG_20190313_172409_5.jpg', '2019-04-07 23:29:02', 0, NULL),
(2, 'PRIME', 1, '', 'tele@gmail.com', '08107926081', 'NULL', NULL, NULL, 0, 0, NULL, '0000-00-00 00:00:00', 0, 'Male'),
(3, 'MEMBER', 0, '', 'olatshege@gmail.com', '08142384174', 'NULL', 2, '1;2;', 0, 0, NULL, '2019-04-07 21:42:00', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wallet`
--

CREATE TABLE `wallet` (
  `id` int(11) NOT NULL,
  `owned_by` int(11) NOT NULL,
  `isPrime` int(1) DEFAULT NULL,
  `public_key` varchar(255) NOT NULL,
  `private_key` varchar(255) DEFAULT NULL,
  `sign_publickey` varchar(255) DEFAULT NULL,
  `sign_privatekey` varchar(255) DEFAULT NULL,
  `balance` double DEFAULT NULL,
  `credits` double UNSIGNED NOT NULL,
  `debits` double NOT NULL,
  `acc_no` varchar(255) NOT NULL,
  `acc_name` varchar(255) NOT NULL,
  `bank` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `wallet`
--

INSERT INTO `wallet` (`id`, `owned_by`, `isPrime`, `public_key`, `private_key`, `sign_publickey`, `sign_privatekey`, `balance`, `credits`, `debits`, `acc_no`, `acc_name`, `bank`, `created_at`) VALUES
(1, 22, 0, '0fcea11e9ac131c960274253d49d1f6d9f6a3c9c73f509cd9ffaf020a1540a19', 'dfdf929a6f9c593bc143cb3335fd7213bbfbd02bb1b7aaddd8e438d901ca3aad', 'e922013e160629cf3fee6d5957430fc88942e9b25aa58617d58e76bbd3a843e1', '402f0620fd255cf5c8d7b6d7c797df512b364f12f658c74378671ab703a8311fe922013e160629cf3fee6d5957430fc88942e9b25aa58617d58e76bbd3a843e1', -100, 0, -100, '0', '0', '', '2019-04-07 02:51:14'),
(2, 24, 0, 'd987322df1c017dbe1a33eb1ea4c067c7389d1b1808e3e9f75c6b81fcbaa397c', '268ad8fa4ea6dafb63bdda5d9eb22454c1f9d0f3a71edaea19e4ec97f6790976', '1b0a9c06186ab988aa7e26624b012c8862967f2c79615fef37348ca05c53e044', '92cc4f63c7974cc393671b75dfdde0119b6fd7c5187659888ce460852ed548351b0a9c06186ab988aa7e26624b012c8862967f2c79615fef37348ca05c53e044', 2400, 4000, -1600, '0124096733', 'Segun Michael', 'Gtb', '2019-04-08 14:15:46'),
(3, 23, 1, '12cb91832adab82277002b1a66c4a30c222af6c2ddbf3642945af9b3fd341005', '04161dafdb0fbb57eed9a5542398514f8e2fce62c57705c873287206a86989a5', '897c1c3a4be545c8b5106b96549478352719b7b064c66d8899d02cebc29c3365', '8d5fa1c602ee767420e40cacd5e68288ce9b557a6590c2cdab2ac700ea82c1c0897c1c3a4be545c8b5106b96549478352719b7b064c66d8899d02cebc29c3365', 4000, 4000, 0, '0', '0', '', '2019-04-07 12:59:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cashoutrequest`
--
ALTER TABLE `cashoutrequest`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `extrauserinfo`
--
ALTER TABLE `extrauserinfo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `mailinglist`
--
ALTER TABLE `mailinglist`
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `membership_plan`
--
ALTER TABLE `membership_plan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_cat`
--
ALTER TABLE `product_cat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_type`
--
ALTER TABLE `product_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_ty` (`type`);

--
-- Indexes for table `pwdmutationlock`
--
ALTER TABLE `pwdmutationlock`
  ADD UNIQUE KEY `msg` (`msg`),
  ADD UNIQUE KEY `email` (`email`) USING BTREE;

--
-- Indexes for table `salespoint`
--
ALTER TABLE `salespoint`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `trade_key` (`trade_key`),
  ADD UNIQUE KEY `tradehistory` (`tradehistory`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `topuprequest`
--
ALTER TABLE `topuprequest`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `voucherpinorartimepin` (`voucherpinorairtimepin`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ifrom` (`ifrom`),
  ADD KEY `fk_ito` (`ito`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `mobile` (`mobile`),
  ADD KEY `fk_membership_plan_id` (`membership_plan_id`);

--
-- Indexes for table `wallet`
--
ALTER TABLE `wallet`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `public_key` (`public_key`),
  ADD KEY `fk_owned_by` (`owned_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cashoutrequest`
--
ALTER TABLE `cashoutrequest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `extrauserinfo`
--
ALTER TABLE `extrauserinfo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `membership_plan`
--
ALTER TABLE `membership_plan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `product_cat`
--
ALTER TABLE `product_cat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `product_type`
--
ALTER TABLE `product_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `salespoint`
--
ALTER TABLE `salespoint`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `topuprequest`
--
ALTER TABLE `topuprequest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wallet`
--
ALTER TABLE `wallet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
