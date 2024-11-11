-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Окт 28 2024 г., 15:59
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `cloud-storage`
--

-- --------------------------------------------------------

--
-- Структура таблицы `directories`
--

CREATE TABLE `directories` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `directories`
--

INSERT INTO `directories` (`id`, `user_id`, `parent_id`, `name`, `created_at`) VALUES
(2, 5, NULL, 'new folder 2 name', '2024-10-22 00:46:58'),
(4, 5, 2, 'Folder 4', '2024-10-22 00:47:49'),
(5, 8, NULL, 'Folder 1', '2024-10-25 23:49:22'),
(6, 8, NULL, 'Folder 2', '2024-10-25 23:49:28'),
(7, 8, NULL, 'Folder 3', '2024-10-25 23:49:36'),
(8, 8, NULL, 'Folder 3', '2024-10-25 23:49:53'),
(9, 8, 2, 'Splaching', '2024-10-25 23:50:04');

-- --------------------------------------------------------

--
-- Структура таблицы `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `directory_id` int(11) DEFAULT NULL,
  `real_name` varchar(255) NOT NULL,
  `encrypted_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `file_shares`
--

CREATE TABLE `file_shares` (
  `id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shared_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `name`, `created_at`, `role`) VALUES
(1, 'anna.karenina@mail.com', '$2y$10$XxhAoV9cIjMg6X1L2toa9ej0Xzrvdt2ZgHfn72kIzRTc7PSpmjEZa', 'Anna Karenina', '2024-10-22 00:21:52', 'user'),
(2, 'anton.kutz@mail.com', '$2y$10$urku4UdukMiSSn3.m0kWnOTMiNvs.QfMD98S596dySoXncv280agK', 'Anton', '2024-10-22 00:22:30', 'user'),
(3, 'yakov.volg@mail.com', '$2y$10$4133iF.oNTvSwRHsOMRCkOQ1R5B5wkF3IH5vuMW/5IMpQdCVIi0Km', 'Yakov Volg', '2024-10-22 00:23:00', 'user'),
(4, 'user@mail.com', '$2y$10$wQwNO1GCII6wWoCSYAL5w.KZ68Na70en56fXm5XwQ4Dbmo9ZDi5Ry', 'User', '2024-10-22 00:23:17', 'user'),
(5, 'admin@mail.com', '$2y$10$Y38KdTrpbFAfGxgvE.gpTeblFIT.M/yRmB7QZZKr3iCOrIZ4Ml3ye', 'Super Admin', '2024-10-22 00:23:28', 'admin'),
(8, 'userTest@test.com', '$2y$10$lTS33e4rUqy7gltMON7uru5WUPKzoxY4m0hy.VUNFoNJSV.wyiZ3.', 'User Test 25/10', '2024-10-25 22:36:27', 'user');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `directories`
--
ALTER TABLE `directories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Индексы таблицы `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `file_shares`
--
ALTER TABLE `file_shares`
  ADD PRIMARY KEY (`id`),
  ADD KEY `file_id` (`file_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `shared_by` (`shared_by`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `directories`
--
ALTER TABLE `directories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT для таблицы `file_shares`
--
ALTER TABLE `file_shares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `directories`
--
ALTER TABLE `directories`
  ADD CONSTRAINT `directories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `directories_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `directories` (`id`);

--
-- Ограничения внешнего ключа таблицы `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `file_shares`
--
ALTER TABLE `file_shares`
  ADD CONSTRAINT `file_shares_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `file_shares_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `file_shares_ibfk_3` FOREIGN KEY (`shared_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
