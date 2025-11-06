-- Database initialization script for ChurchSPB Quiz Application
-- "ПИР ЛЮБВИ" - Love Feast Event

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Создание таблицы questionnaire
CREATE TABLE IF NOT EXISTS `questionnaire` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `header` varchar(500) NOT NULL,
  `comment` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=2;

-- Вставка данных "ПИР ЛЮБВИ"
INSERT INTO `questionnaire` (`id`, `name`, `header`, `comment`) VALUES
(1, 'ПИР ЛЮБВИ', 'Если нужное количество блюд набрано, а вы всё равно хотели бы принести <b>салат, или мясное, или гарнир,</b> то используйте <b>«Другое»</b>.', '');

-- Создание таблицы questionnaire_list
CREATE TABLE IF NOT EXISTS `questionnaire_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_list` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(2) NOT NULL,
  `sort` int(3) DEFAULT NULL,
  `limits` int(5) DEFAULT NULL,
  `required` tinyint(1) DEFAULT NULL,
  `quizzer` char(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=29;

-- Вставка элементов анкеты для "ПИР ЛЮБВИ"
INSERT INTO `questionnaire_list` (`id`, `id_list`, `name`, `type`, `sort`, `limits`, `required`, `quizzer`) VALUES
(1, 1, 'Салат — 1-1,5 кг', 'ch', 2, 8, 0, ''),
(2, 1, 'Сладости', 'ch', 11, 4, 0, ''),
(3, 1, 'Мясное — 1-1,5 кг', 'ch', 3, 8, NULL, ''),
(4, 1, 'Запечённая картошка — 1 кг', 'ch', 3, 5, NULL, ''),
(5, 2, 'Выпечка (не более  1 кг/чел).', 'ch', 6, 10, NULL, ''),
(6, 2, 'Фрукты — 1 кг', 'ch', 7, 5, NULL, ''),
(7, 1, 'Соки — 2 л', 'ch', 11, 3, NULL, ''),
(8, 1, 'Другое', 'in', 16, NULL, NULL, ''),
(9, 1, 'Комментарий', 'in', 17, NULL, 1, ''),
(10, 1, 'Груши — 1 кг', 'ch', 7, 1, NULL, ''),
(11, 1, 'Виноград — 1 кг', 'ch', 5, 2, NULL, ''),
(12, 1, 'Мандарины — 1 кг', 'ch', 9, 2, NULL, ''),
(13, 2, 'Другие фрукты — 1 кг', 'ch', 10, 1, NULL, ''),
(14, 1, 'Служение — принимать продукты', 'ch', 13, 2, NULL, ''),
(15, 1, 'Служение на кухне (сёстры)', 'ch', 14, 5, NULL, ''),
(16, 1, 'Служение — уборка после пира любви', 'ch', 15, 5, NULL, ''),
(17, 1, 'СЛУЖЕНИЕ', 'he', 12, NULL, NULL, ''),
(18, 1, 'ЕДА', 'he', 1, NULL, NULL, ''),
(19, 2, 'Арбуз — 1 шт', 'ch', 7, 2, NULL, ''),
(20, 2, 'Бананы — 1 кг', 'ch', 4, 1, NULL, ''),
(21, 1, 'Апельсины — 1 кг', 'ch', 3, 1, NULL, ''),
(22, 2, 'Абрикосы — 1 кг', 'ch', 7, 2, NULL, ''),
(23, 2, 'Нектарины — 1 кг', 'ch', 8, 2, NULL, ''),
(24, 2, 'Черешня — 1 кг', 'ch', 9, 2, NULL, ''),
(25, 2, 'Дыня — 1 шт', 'ch', 7, 2, NULL, ''),
(26, 2, 'Хурма/королёк — 1 кг', 'ch', 9, 2, NULL, ''),
(27, 2, 'Киви — 1 кг', 'ch', 7, 1, NULL, ''),
(28, 2, 'Персики — 1 кг', 'ch', 8, 2, NULL, '');

-- Создание таблицы questionnaire_data
CREATE TABLE IF NOT EXISTS `questionnaire_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_list` int(11) DEFAULT NULL,
  `value` text NOT NULL,
  `date` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_list` (`id_list`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1456;
