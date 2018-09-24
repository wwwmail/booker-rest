-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Сен 24 2018 г., 16:19
-- Версия сервера: 5.6.38
-- Версия PHP: 7.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `user4`
--

-- --------------------------------------------------------

--
-- Структура таблицы `app_events`
--

CREATE TABLE `app_events` (
  `id` int(11) NOT NULL,
  `recursion` int(11) DEFAULT '0',
  `recursion_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `description` text,
  `date` date DEFAULT NULL,
  `starttime` datetime DEFAULT NULL,
  `endtime` datetime DEFAULT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `app_events`
--

INSERT INTO `app_events` (`id`, `recursion`, `recursion_id`, `user_id`, `room_id`, `description`, `date`, `starttime`, `endtime`, `created`) VALUES
(79, 1, NULL, 2, 1, 'some desc first event frank', '2018-09-25', '2018-09-25 13:30:00', '2018-09-25 14:00:00', '2018-09-24 10:50:45'),
(80, 1, 79, 2, 1, 'some desc first event frank', '2018-09-25', '2018-09-25 13:30:00', '2018-09-25 14:00:00', '2018-09-24 10:50:45'),
(81, 1, 79, 2, 1, 'some desc first event frank', '2018-10-09', '2018-10-09 13:30:00', '2018-10-09 14:00:00', '2018-09-24 10:50:45'),
(82, 1, 79, 2, 1, 'some desc first event frank', '2018-10-16', '2018-10-16 13:30:00', '2018-10-16 14:00:00', '2018-09-24 10:50:45');

-- --------------------------------------------------------

--
-- Структура таблицы `app_rooms`
--

CREATE TABLE `app_rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `app_users`
--

CREATE TABLE `app_users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `expire` datetime DEFAULT NULL,
  `is_admin` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `app_users`
--

INSERT INTO `app_users` (`id`, `email`, `password`, `first_name`, `last_name`, `token`, `expire`, `is_admin`) VALUES
(1, 'www@mail.cz', '$2y$13$JDJ5JDEzJEx3QlFaNmcxbeTHv7J6Pbmo9XPXrLlEEYVw37HQHi4Be', 'Admin1 33 3', 'Admin 222 2222 33', '611e3a9550d02db08ebcc7a853e02ecd', '2018-09-21 12:29:37', NULL),
(2, '12@mail.cz', '$2y$13$JDJ5JDEzJC9IcXB2ek10RueLL5YP4VbNgZcvvHqyQVxFyoGmV7PQi', 'Test', 'DEv', 'd7677985008155a65bbd7ad37292a24e', '2018-09-24 13:59:41', NULL),
(3, 'qq@mail.cz', '$2y$13$JDJ5JDEzJC9IcXB2ek10RueLL5YP4VbNgZcvvHqyQVxFyoGmV7PQi', 'Frank', 'Lampard', 'fc79173234b0a7dc3077ba69b221c6fe', '2018-09-22 14:41:45', 1),
(7, 'test@mail.cz', 'test', 'Jonyr', 'doe', NULL, NULL, NULL),
(12, 'sheva199.92@mail.ru', '$2y$13$JDJ5JDEzJG5TdThSSkpyLuJ4LI8XK2T610IKlZW6hfIXvPiIjvRw2', 'Jon', 'doe', '9e4319f7ac70dd10de73f861c839b846', '2018-09-21 18:43:00', NULL),
(13, 'mkmk@mail.mk', '$2y$13$JDJ5JDEzJEJ1YlFrS0FGV.PbuL8MpJVJTRKRUYPmvXYlcvk7ZBQwW', 'test', 'Jon', '7d06df3d3cc7eea1c518d7089ff3de8a', '2018-09-21 10:16:30', NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `app_events`
--
ALTER TABLE `app_events`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `app_rooms`
--
ALTER TABLE `app_rooms`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `app_users`
--
ALTER TABLE `app_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `app_events`
--
ALTER TABLE `app_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT для таблицы `app_rooms`
--
ALTER TABLE `app_rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `app_users`
--
ALTER TABLE `app_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
