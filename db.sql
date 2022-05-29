-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Gostitelj: 127.0.0.1
-- Čas nastanka: 29. maj 2022 ob 19.05
-- Različica strežnika: 10.4.24-MariaDB
-- Različica PHP: 8.1.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Zbirka podatkov: `st`
--

-- --------------------------------------------------------

--
-- Struktura tabele `images`
--

CREATE TABLE `images` (
  `url` text NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Odloži podatke za tabelo `images`
--

INSERT INTO `images` (`url`, `id`) VALUES
('../uploads/index_1653839728.jpg', 113),
('../uploads/thumbnail-n_1653839728.jpg', 114),
('../uploads/piran-3_1653840011.jpg', 115),
('../uploads/attractions_piran_1_1_1653840011.jpg', 116),
('../uploads/5-podpesko_jezero_poleti_1653840369.jpeg', 117),
('../uploads/images_1653840787.jpg', 120),
('../uploads/secovljske-soline-solinar_1653841862.jpg', 121),
('../uploads/mediaspeed-11_1653842833.jpg', 122),
('../uploads/slide-2-vinarium_1653842833.jpg', 123),
('../uploads/postonjska2_1653843053.jpg', 124),
('../uploads/postojsnka_1653843053.jpg', 125);

-- --------------------------------------------------------

--
-- Struktura tabele `spots`
--

CREATE TABLE `spots` (
  `lat` double NOT NULL,
  `lon` double NOT NULL,
  `publisher` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Odloži podatke za tabelo `spots`
--

INSERT INTO `spots` (`lat`, `lon`, `publisher`, `id`, `title`, `description`, `created`) VALUES
(46.362315230199954, 14.08154789173686, 78, 82, 'Blejsko jezero', 'Blejsko jezero leži na gorenjskem. Jezero pa ima tudi otok, kateri je primeren za vrhunsko ozadje na vaši sliki.', '2022-05-29 17:55:28'),
(45.529939752996654, 13.563484186940226, 78, 84, 'Piran', 'Piran je primeren za vse ljubitelje morja. Z zelo znano cerkvico po kateri vsi prepoznamo Piran pa je primern za vašo popolno sliko.', '2022-05-29 18:00:11'),
(45.96885214040396, 14.431768668148347, 79, 85, 'Podpeško jezero', 'Zelo znano jezero v osrčju Slovenije. Poleg pa je tudi izvrstna restavracija in pot do Krima.', '2022-05-29 18:06:09'),
(45.48268790326616, 13.596055416294814, 79, 86, 'Sečovlske soline', 'Super kraj za samoto in slikanje med bazeni morja.', '2022-05-29 18:31:02'),
(46.56643969233276, 16.462161871450615, 80, 87, 'Vinarium', 'Vrhunski kotiček za zajem slik kar 3-h držav. Tu smo bili že velikokrat in kar ne morem se naužiti lepote, ki se jo vidi iz vrha stolpa.\r\nRes zelo priporočam.', '2022-05-29 18:47:13'),
(45.690134226076225, 14.168767745484198, 80, 88, 'Postojnska jama', 'Najbol znana Slovenska jama. Notri se odpelješ z vlakcom in že na poti se lahko naredi ogromno selfijev. Le paziti moraš, da telefon ne pade dol (ne gre hitro tako, da ni težko paziti)', '2022-05-29 18:50:53');

-- --------------------------------------------------------

--
-- Struktura tabele `spots_images`
--

CREATE TABLE `spots_images` (
  `spot` int(11) NOT NULL,
  `image` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Odloži podatke za tabelo `spots_images`
--

INSERT INTO `spots_images` (`spot`, `image`) VALUES
(82, 113),
(82, 114),
(84, 115),
(84, 116),
(85, 117),
(85, 120),
(86, 121),
(87, 122),
(87, 123),
(88, 124),
(88, 125);

-- --------------------------------------------------------

--
-- Struktura tabele `tokens`
--

CREATE TABLE `tokens` (
  `token` text NOT NULL,
  `expire` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Odloži podatke za tabelo `tokens`
--

INSERT INTO `tokens` (`token`, `expire`) VALUES
('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX2lkIjo3OCwiaWF0IjoxNjUzODM5NTY1LCJleHAiOjg2NDAwfQ.kYqGDojWk-HHxsBNz5HTlnLXgN-SRCUfKnn-CFLZbHU', '2022-05-30 15:52:45'),
('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX2lkIjo3OSwiaWF0IjoxNjUzODQwMjIzLCJleHAiOjg2NDAwfQ.ryf3yZKIga5_gXYtwXGmTLCBvXerc1WtHO9bkF5ifbE', '2022-05-30 16:03:43');

-- --------------------------------------------------------

--
-- Struktura tabele `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Odloži podatke za tabelo `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(78, 'uporabnik1', '$2y$10$ahZcYWjBmt9OeANWayna/.aGRpB5nUyIf1VY9w4wo44VlUNjrHW5y'),
(79, 'uporabnik2', '$2y$10$yn6DW8viz/XNg6BH.40W2u4AgHr5QhJd4VfpuMrTgIx0jVwLvTDfS'),
(80, 'uporabnik3', '$2y$10$11q93YtJu3Hfh4pHjwgUde3uZ6mzKYNQlf6M9MnzfAK2BvG6OEski');

--
-- Indeksi zavrženih tabel
--

--
-- Indeksi tabele `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`);

--
-- Indeksi tabele `spots`
--
ALTER TABLE `spots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `publisher` (`publisher`);

--
-- Indeksi tabele `spots_images`
--
ALTER TABLE `spots_images`
  ADD KEY `spot` (`spot`),
  ADD KEY `image` (`image`);

--
-- Indeksi tabele `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT zavrženih tabel
--

--
-- AUTO_INCREMENT tabele `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT tabele `spots`
--
ALTER TABLE `spots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT tabele `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- Omejitve tabel za povzetek stanja
--

--
-- Omejitve za tabelo `spots`
--
ALTER TABLE `spots`
  ADD CONSTRAINT `spots_ibfk_1` FOREIGN KEY (`publisher`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Omejitve za tabelo `spots_images`
--
ALTER TABLE `spots_images`
  ADD CONSTRAINT `spots_images_ibfk_1` FOREIGN KEY (`spot`) REFERENCES `spots` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `spots_images_ibfk_2` FOREIGN KEY (`image`) REFERENCES `images` (`id`) ON DELETE CASCADE;
COMMIT;