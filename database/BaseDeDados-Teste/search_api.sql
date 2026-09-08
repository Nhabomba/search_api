-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 08, 2026 at 03:27 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `search_api`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-weather:4b39c73be29c5e44fadcd8365c6145f2', 'a:2:{s:8:\"location\";a:7:{s:4:\"name\";s:6:\"Maputo\";s:8:\"latitude\";d:-25.96553;s:9:\"longitude\";d:32.58322;s:7:\"country\";s:11:\"Moçambique\";s:11:\"countryCode\";s:2:\"MZ\";s:6:\"admin1\";s:16:\"Cidade de Maputo\";s:10:\"population\";i:1254837;}s:8:\"forecast\";a:5:{s:8:\"latitude\";d:-25.975395;s:9:\"longitude\";d:32.557377;s:8:\"timezone\";s:13:\"Africa/Maputo\";s:7:\"current\";a:7:{s:4:\"time\";s:16:\"2026-09-08T16:00\";s:8:\"interval\";i:900;s:14:\"temperature_2m\";d:21.7;s:20:\"relative_humidity_2m\";i:63;s:20:\"apparent_temperature\";d:21.5;s:12:\"weather_code\";i:3;s:14:\"wind_speed_10m\";d:10.8;}s:12:\"currentUnits\";a:7:{s:4:\"time\";s:7:\"iso8601\";s:8:\"interval\";s:7:\"seconds\";s:14:\"temperature_2m\";s:3:\"°C\";s:20:\"relative_humidity_2m\";s:1:\"%\";s:20:\"apparent_temperature\";s:3:\"°C\";s:12:\"weather_code\";s:8:\"wmo code\";s:14:\"wind_speed_10m\";s:4:\"km/h\";}}}', 1788876875),
('laravel-cache-weather:b5bedbaa266e15ba3cf5a3707543d874', 'a:2:{s:8:\"location\";a:7:{s:4:\"name\";s:22:\"Vila-de-Santiago-Maior\";s:8:\"latitude\";d:-16.15639;s:9:\"longitude\";d:33.58667;s:7:\"country\";s:11:\"Moçambique\";s:11:\"countryCode\";s:2:\"MZ\";s:6:\"admin1\";s:18:\"Província de Tete\";s:10:\"population\";i:357000;}s:8:\"forecast\";a:5:{s:8:\"latitude\";d:-16.133568;s:9:\"longitude\";d:33.611374;s:8:\"timezone\";s:13:\"Africa/Maputo\";s:7:\"current\";a:7:{s:4:\"time\";s:16:\"2026-09-08T16:00\";s:8:\"interval\";i:900;s:14:\"temperature_2m\";d:27.1;s:20:\"relative_humidity_2m\";i:43;s:20:\"apparent_temperature\";d:24.8;s:12:\"weather_code\";i:3;s:14:\"wind_speed_10m\";d:22.9;}s:12:\"currentUnits\";a:7:{s:4:\"time\";s:7:\"iso8601\";s:8:\"interval\";s:7:\"seconds\";s:14:\"temperature_2m\";s:3:\"°C\";s:20:\"relative_humidity_2m\";s:1:\"%\";s:20:\"apparent_temperature\";s:3:\"°C\";s:12:\"weather_code\";s:8:\"wmo code\";s:14:\"wind_speed_10m\";s:4:\"km/h\";}}}', 1788876885),
('laravel-cache-weather:964a39a2bbee4dd81d39629f7d56944e', 'a:2:{s:8:\"location\";a:7:{s:4:\"name\";s:9:\"Inhambane\";s:8:\"latitude\";d:-23.865;s:9:\"longitude\";d:35.38333;s:7:\"country\";s:11:\"Moçambique\";s:11:\"countryCode\";s:2:\"MZ\";s:6:\"admin1\";s:23:\"Província de Inhambane\";s:10:\"population\";i:95388;}s:8:\"forecast\";a:5:{s:8:\"latitude\";d:-23.866432;s:9:\"longitude\";d:35.428574;s:8:\"timezone\";s:13:\"Africa/Maputo\";s:7:\"current\";a:7:{s:4:\"time\";s:16:\"2026-09-08T16:00\";s:8:\"interval\";i:900;s:14:\"temperature_2m\";d:22.7;s:20:\"relative_humidity_2m\";i:57;s:20:\"apparent_temperature\";d:21;s:12:\"weather_code\";i:0;s:14:\"wind_speed_10m\";d:19.5;}s:12:\"currentUnits\";a:7:{s:4:\"time\";s:7:\"iso8601\";s:8:\"interval\";s:7:\"seconds\";s:14:\"temperature_2m\";s:3:\"°C\";s:20:\"relative_humidity_2m\";s:1:\"%\";s:20:\"apparent_temperature\";s:3:\"°C\";s:12:\"weather_code\";s:8:\"wmo code\";s:14:\"wind_speed_10m\";s:4:\"km/h\";}}}', 1788876894),
('laravel-cache-weather:afad690d4c4ed309f89a4a0264bdc540', 'a:2:{s:8:\"location\";a:7:{s:4:\"name\";s:5:\"Pemba\";s:8:\"latitude\";d:-12.97395;s:9:\"longitude\";d:40.51775;s:7:\"country\";s:11:\"Moçambique\";s:11:\"countryCode\";s:2:\"MZ\";s:6:\"admin1\";s:26:\"Província de Cabo Delgado\";s:10:\"population\";i:232932;}s:8:\"forecast\";a:5:{s:8:\"latitude\";d:-13.0404215;s:9:\"longitude\";d:40.536854;s:8:\"timezone\";s:13:\"Africa/Maputo\";s:7:\"current\";a:7:{s:4:\"time\";s:16:\"2026-09-08T16:15\";s:8:\"interval\";i:900;s:14:\"temperature_2m\";d:26;s:20:\"relative_humidity_2m\";i:67;s:20:\"apparent_temperature\";d:25.8;s:12:\"weather_code\";i:0;s:14:\"wind_speed_10m\";d:26;}s:12:\"currentUnits\";a:7:{s:4:\"time\";s:7:\"iso8601\";s:8:\"interval\";s:7:\"seconds\";s:14:\"temperature_2m\";s:3:\"°C\";s:20:\"relative_humidity_2m\";s:1:\"%\";s:20:\"apparent_temperature\";s:3:\"°C\";s:12:\"weather_code\";s:8:\"wmo code\";s:14:\"wind_speed_10m\";s:4:\"km/h\";}}}', 1788877579),
('laravel-cache-weather:9363065036dfd6f1787cf2721cae62ca', 'a:2:{s:8:\"location\";a:7:{s:4:\"name\";s:7:\"Nampula\";s:8:\"latitude\";d:-15.11646;s:9:\"longitude\";d:39.2666;s:7:\"country\";s:11:\"Moçambique\";s:11:\"countryCode\";s:2:\"MZ\";s:6:\"admin1\";s:21:\"Província de Nampula\";s:10:\"population\";i:770379;}s:8:\"forecast\";a:5:{s:8:\"latitude\";d:-15.079085;s:9:\"longitude\";d:39.280376;s:8:\"timezone\";s:13:\"Africa/Maputo\";s:7:\"current\";a:7:{s:4:\"time\";s:16:\"2026-09-08T16:45\";s:8:\"interval\";i:900;s:14:\"temperature_2m\";d:25.5;s:20:\"relative_humidity_2m\";i:50;s:20:\"apparent_temperature\";d:24.3;s:12:\"weather_code\";i:2;s:14:\"wind_speed_10m\";d:17.5;}s:12:\"currentUnits\";a:7:{s:4:\"time\";s:7:\"iso8601\";s:8:\"interval\";s:7:\"seconds\";s:14:\"temperature_2m\";s:3:\"°C\";s:20:\"relative_humidity_2m\";s:1:\"%\";s:20:\"apparent_temperature\";s:3:\"°C\";s:12:\"weather_code\";s:8:\"wmo code\";s:14:\"wind_speed_10m\";s:4:\"km/h\";}}}', 1788879317),
('laravel-cache-weather:33231833060cea8ba50514c47e487d26', 'a:2:{s:8:\"location\";a:7:{s:4:\"name\";s:10:\"São Paulo\";s:8:\"latitude\";d:-23.5475;s:9:\"longitude\";d:-46.63611;s:7:\"country\";s:6:\"Brasil\";s:11:\"countryCode\";s:2:\"BR\";s:6:\"admin1\";s:10:\"São Paulo\";s:10:\"population\";i:12400232;}s:8:\"forecast\";a:5:{s:8:\"latitude\";d:-23.514938;s:9:\"longitude\";d:-46.610504;s:8:\"timezone\";s:17:\"America/Sao_Paulo\";s:7:\"current\";a:7:{s:4:\"time\";s:16:\"2026-09-08T11:45\";s:8:\"interval\";i:900;s:14:\"temperature_2m\";d:17.9;s:20:\"relative_humidity_2m\";i:76;s:20:\"apparent_temperature\";d:18.9;s:12:\"weather_code\";i:3;s:14:\"wind_speed_10m\";d:1.8;}s:12:\"currentUnits\";a:7:{s:4:\"time\";s:7:\"iso8601\";s:8:\"interval\";s:7:\"seconds\";s:14:\"temperature_2m\";s:3:\"°C\";s:20:\"relative_humidity_2m\";s:1:\"%\";s:20:\"apparent_temperature\";s:3:\"°C\";s:12:\"weather_code\";s:8:\"wmo code\";s:14:\"wind_speed_10m\";s:4:\"km/h\";}}}', 1788879609),
('laravel-cache-weather:29c695137ae2c4c138f87bfbd9052ec1', 'a:2:{s:8:\"location\";a:7:{s:4:\"name\";s:7:\"Toronto\";s:8:\"latitude\";d:43.70643;s:9:\"longitude\";d:-79.39864;s:7:\"country\";s:7:\"Canadá\";s:11:\"countryCode\";s:2:\"CA\";s:6:\"admin1\";s:8:\"Ontário\";s:10:\"population\";i:2794356;}s:8:\"forecast\";a:5:{s:8:\"latitude\";d:43.70455;s:9:\"longitude\";d:-79.404625;s:8:\"timezone\";s:15:\"America/Toronto\";s:7:\"current\";a:7:{s:4:\"time\";s:16:\"2026-09-08T11:00\";s:8:\"interval\";i:900;s:14:\"temperature_2m\";d:22.2;s:20:\"relative_humidity_2m\";i:66;s:20:\"apparent_temperature\";d:23.6;s:12:\"weather_code\";i:0;s:14:\"wind_speed_10m\";d:6.4;}s:12:\"currentUnits\";a:7:{s:4:\"time\";s:7:\"iso8601\";s:8:\"interval\";s:7:\"seconds\";s:14:\"temperature_2m\";s:3:\"°C\";s:20:\"relative_humidity_2m\";s:1:\"%\";s:20:\"apparent_temperature\";s:3:\"°C\";s:12:\"weather_code\";s:8:\"wmo code\";s:14:\"wind_speed_10m\";s:4:\"km/h\";}}}', 1788880535);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_09_08_000001_create_weather_consultations_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `weather_consultations`
--

DROP TABLE IF EXISTS `weather_consultations`;
CREATE TABLE IF NOT EXISTS `weather_consultations` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requested_city` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requested_country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin1` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `timezone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `weather_time` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `consulted_at` timestamp NOT NULL,
  `current` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `weather_consultations`
--

INSERT INTO `weather_consultations` (`id`, `requested_city`, `requested_country`, `city`, `country`, `country_code`, `admin1`, `latitude`, `longitude`, `timezone`, `weather_time`, `consulted_at`, `current`, `created_at`, `updated_at`) VALUES
('e0a5345c-d3cc-4a87-b881-b8e90cbe44de', 'Inhambane', 'Mozambique', 'Inhambane', 'Moçambique', 'MZ', 'Província de Inhambane', -23.8664320, 35.4285740, 'Africa/Maputo', '2026-09-08T16:00', '2026-09-08 12:09:54', '{\"humidity\": 57, \"windSpeed\": 19.5, \"temperature\": 22.7, \"weatherCode\": 0, \"humidityUnit\": \"%\", \"windSpeedUnit\": \"km/h\", \"temperatureUnit\": \"°C\", \"weatherCodeUnit\": \"wmo code\", \"apparentTemperature\": 21, \"apparentTemperatureUnit\": \"°C\"}', '2026-09-08 12:09:54', '2026-09-08 12:09:54'),
('60057cde-6f59-4cd0-ad09-47ff63b5c3b0', 'Maputo', 'Mozambique', 'Maputo', 'Moçambique', 'MZ', 'Cidade de Maputo', -25.9753950, 32.5573770, 'Africa/Maputo', '2026-09-08T16:00', '2026-09-08 12:09:35', '{\"humidity\": 63, \"windSpeed\": 10.8, \"temperature\": 21.7, \"weatherCode\": 3, \"humidityUnit\": \"%\", \"windSpeedUnit\": \"km/h\", \"temperatureUnit\": \"°C\", \"weatherCodeUnit\": \"wmo code\", \"apparentTemperature\": 21.5, \"apparentTemperatureUnit\": \"°C\"}', '2026-09-08 12:09:35', '2026-09-08 12:09:35'),
('be23ff2c-60f3-4722-884a-1615d0533bae', 'Tete', 'Mozambique', 'Vila-de-Santiago-Maior', 'Moçambique', 'MZ', 'Província de Tete', -16.1335680, 33.6113740, 'Africa/Maputo', '2026-09-08T16:00', '2026-09-08 12:09:45', '{\"humidity\": 43, \"windSpeed\": 22.9, \"temperature\": 27.1, \"weatherCode\": 3, \"humidityUnit\": \"%\", \"windSpeedUnit\": \"km/h\", \"temperatureUnit\": \"°C\", \"weatherCodeUnit\": \"wmo code\", \"apparentTemperature\": 24.8, \"apparentTemperatureUnit\": \"°C\"}', '2026-09-08 12:09:45', '2026-09-08 12:09:45'),
('34b2cf0f-6c71-4074-93d5-275a5a03e34c', 'Pemba', 'Mozambique', 'Pemba', 'Moçambique', 'MZ', 'Província de Cabo Delgado', -13.0404215, 40.5368540, 'Africa/Maputo', '2026-09-08T16:15', '2026-09-08 12:21:19', '{\"humidity\": 67, \"windSpeed\": 26, \"temperature\": 26, \"weatherCode\": 0, \"humidityUnit\": \"%\", \"windSpeedUnit\": \"km/h\", \"temperatureUnit\": \"°C\", \"weatherCodeUnit\": \"wmo code\", \"apparentTemperature\": 25.8, \"apparentTemperatureUnit\": \"°C\"}', '2026-09-08 12:21:20', '2026-09-08 12:21:20'),
('4be5ff88-b80f-40cf-80ed-c47c7f237e1e', 'Nampula', 'Mozambique', 'Nampula', 'Moçambique', 'MZ', 'Província de Nampula', -15.0790850, 39.2803760, 'Africa/Maputo', '2026-09-08T16:45', '2026-09-08 12:50:17', '{\"humidity\": 50, \"windSpeed\": 17.5, \"temperature\": 25.5, \"weatherCode\": 2, \"humidityUnit\": \"%\", \"windSpeedUnit\": \"km/h\", \"temperatureUnit\": \"°C\", \"weatherCodeUnit\": \"wmo code\", \"apparentTemperature\": 24.3, \"apparentTemperatureUnit\": \"°C\"}', '2026-09-08 12:50:17', '2026-09-08 12:50:17'),
('05c55c82-5727-46af-b007-91494b98b7e0', 'São Paulo', NULL, 'São Paulo', 'Brasil', 'BR', 'São Paulo', -23.5149380, -46.6105040, 'America/Sao_Paulo', '2026-09-08T11:45', '2026-09-08 12:55:09', '{\"humidity\": 76, \"windSpeed\": 1.8, \"temperature\": 17.9, \"weatherCode\": 3, \"humidityUnit\": \"%\", \"windSpeedUnit\": \"km/h\", \"temperatureUnit\": \"°C\", \"weatherCodeUnit\": \"wmo code\", \"apparentTemperature\": 18.9, \"apparentTemperatureUnit\": \"°C\"}', '2026-09-08 12:55:09', '2026-09-08 12:55:09');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
