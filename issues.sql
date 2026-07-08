-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Июл 08 2026 г., 11:14
-- Версия сервера: 8.4.8-8-beget-1-2
-- Версия PHP: 8.3.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `human2xn_jou`
--

-- --------------------------------------------------------

--
-- Структура таблицы `issues`
--
-- Создание: Май 05 2026 г., 12:09
-- Последнее обновление: Июл 08 2026 г., 06:47
--

DROP TABLE IF EXISTS `issues`;
CREATE TABLE `issues` (
  `id` bigint UNSIGNED NOT NULL,
  `titleid` int UNSIGNED DEFAULT NULL,
  `issn` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ISSN журнала',
  `eissn` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Электронный ISSN',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `volume` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `number` int UNSIGNED DEFAULT NULL,
  `alt_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Сквозной номер выпуска',
  `part` smallint UNSIGNED DEFAULT NULL COMMENT 'Часть выпуска',
  `issue_pages` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Диапазон страниц выпуска',
  `issue_type` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ISS' COMMENT 'Тип выпуска: ISS, OFI, SPI',
  `year` smallint UNSIGNED NOT NULL,
  `month` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_en` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Название выпуска (англ)',
  `published_at` date DEFAULT NULL,
  `pdf_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pdf_file_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Путь к загруженному PDF файлу выпуска',
  `pdf_original_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Оригинальное имя PDF файла выпуска',
  `pdf_file_size` bigint DEFAULT NULL COMMENT 'Размер PDF файла выпуска в байтах',
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int DEFAULT NULL,
  `doi` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'DOI журнала',
  `issue_doi` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'DOI выпуска',
  `edn` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'EDN (eLIBRARY Document Number)',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Описание выпуска (рус)',
  `description_en` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Описание выпуска (англ)',
  `publisher` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Издатель',
  `issue_files` json DEFAULT NULL COMMENT 'Файлы выпуска (обложка и др.)',
  `cover_image_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Путь к загруженной обложке',
  `cover_original_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Оригинальное имя файла обложки',
  `cover_image` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ссылка на обложку выпуска'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `issues`
--

INSERT INTO `issues` (`id`, `titleid`, `issn`, `eissn`, `created_at`, `updated_at`, `volume`, `number`, `alt_number`, `part`, `issue_pages`, `issue_type`, `year`, `month`, `title`, `title_en`, `published_at`, `pdf_url`, `pdf_file_path`, `pdf_original_name`, `pdf_file_size`, `is_published`, `sort_order`, `doi`, `issue_doi`, `edn`, `description`, `description_en`, `publisher`, `issue_files`, `cover_image_path`, `cover_original_name`, `cover_image`) VALUES
(5, NULL, '3034-6827', NULL, '2026-03-17 10:35:06', '2026-05-06 04:28:04', '1', 1, NULL, 1, '1-168', 'ISS', 2025, '4', NULL, NULL, '2025-04-30', 'https://journalmh.org/storage/issue_pdfs/1777547168______________________________________________________.________1._____1._2025_______.pdf', 'issue_pdfs/1777547168______________________________________________________.________1._____1._2025_______.pdf', 'Современная гуманитаристика. Том 1. № 1. 2025 год.pdf', 1749755, 1, 0, NULL, NULL, NULL, NULL, NULL, 'Чувашский государственный институт гуманитарных наук', NULL, 'issue_covers/1777549890_cover_Obl.jpg', 'Obl.jpg', NULL),
(12, NULL, '3034-6827', NULL, '2026-05-04 09:40:13', '2026-05-05 05:58:41', '1', 2, NULL, NULL, '1-140', 'ISS', 2025, '6', NULL, NULL, '2025-06-23', NULL, 'issue_pdfs/1777971521______2026________1_____2.pdf', 'СГ_2026_Том 1_№ 2.pdf', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Чувашский государственный институт гуманитарных наук', NULL, 'issue_covers/1777971521_cover______2026________1_____2__________________page-0001.jpg', 'СГ_2026_Том 1_№ 2 (обложка)_page-0001.jpg', NULL),
(14, NULL, '3034-6827', NULL, '2026-05-04 10:18:42', '2026-05-04 10:18:42', '1', 3, NULL, NULL, '1-176', 'ISS', 2025, '9', NULL, NULL, '2025-09-30', NULL, 'issue_pdfs/1777900722______________________________________________________.________1_____3._2025___.pdf', 'Современная гуманитаристика. Том 1, №3. 2025 г.pdf', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Чувашский государственный институт гуманитарных наук', NULL, 'issue_covers/1777900722_cover______1____3.jpg', 'СГ_1_№3.jpg', NULL),
(15, NULL, '3034-6827', NULL, '2026-05-05 10:10:41', '2026-05-05 10:10:41', '1', 4, NULL, NULL, '1-156', 'ISS', 2025, '12', NULL, NULL, '2025-12-25', NULL, 'issue_pdfs/1777986641______________________________________________________.________1._____4._2025_______.pdf', 'Современная гуманитаристика. Том 1. № 4. 2025 год.pdf', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Чувашский государственный институт гуманитарных наук', NULL, 'issue_covers/1777986641_cover_______________.________1______4.jpg', 'Обложка. Том 1, № 4.jpg', NULL),
(16, NULL, '3034-6827', NULL, '2026-05-05 10:14:27', '2026-05-05 10:15:03', '2', 1, NULL, NULL, '1-156', 'ISS', 2026, '4', NULL, NULL, '2026-04-15', 'https://journalmh.org/storage/issue_pdfs/1777986867____________________________________________________.________2._____1._2026_______.pdf', 'issue_pdfs/1777986867____________________________________________________.________2._____1._2026_______.pdf', 'Современная гманитаристика. Том 2. № 1. 2026 год.pdf', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Чувашский государственный институт гуманитарных наук', NULL, 'issue_covers/1777986867_cover_______________.________2______1.jpg', 'Обложка. Том 2, № 1.jpg', NULL),
(17, NULL, '3034-6827', NULL, '2026-07-07 09:33:14', '2026-07-08 03:47:54', '2', 2, NULL, NULL, '1-164', 'ISS', 2026, '7', NULL, NULL, '2026-07-06', NULL, 'issue_pdfs/1783493272_________________________________________________________2____2___________1_.pdf', 'Современная гуманитаристика_Т2_№2_РИНЦ (1).pdf', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Чувашский государственный институт гуманитарных наук', NULL, 'issue_covers/1783427594_cover___________________________2_________.jpg', 'Обложка_номер_2_лицо.jpg', NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `issues`
--
ALTER TABLE `issues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `issues_year_index` (`year`),
  ADD KEY `issues_published_at_index` (`published_at`),
  ADD KEY `issues_is_published_index` (`is_published`),
  ADD KEY `issues_sort_order_index` (`sort_order`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `issues`
--
ALTER TABLE `issues`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
