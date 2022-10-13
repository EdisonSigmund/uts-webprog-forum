-- SQLBook: Code
CREATE DATABASE `notes`;
USE `notes`;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `attachments` (
  `id` int(11) NOT NULL,
  `file` varchar(255) NOT NULL,
  `note_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `title` varchar(50) NOT NULL DEFAULT 'Untitled Note',
  `content` text NOT NULL,
  `owner` int(11) NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `token` varchar(50) DEFAULT NULL,
  `is_shared` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `shared_notes` (
  `id` int(11) NOT NULL,
  `shared_to` int(11) NOT NULL,
  `note_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `username` varchar(25) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` varchar(10) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT 'UNBANNED'
  /* 
    tombol buat ngubah kolom status dari unbanned jadi banned
    pas login di cek statusnya kalo banned gabisa fungsinya sama kayak guest acc
    (cuman liet gabisa post + like + comment) 
  */
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `username_changes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username_old` varchar(255) NOT NULL,
  `username_new` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `shared_notes`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `[username]` (`username`),
  ADD UNIQUE KEY `[email]` (`email`);

ALTER TABLE `username_changes`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `shared_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `username_changes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

INSERT INTO `user` (`id`, `fullname`, `username`, `email`, `password`, `role`, `foto`) 
VALUES (0, 'Guest', 'Guest', '', '', 'Guest', NULL)
-- SQLBook: Code
