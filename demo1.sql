-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost
-- Üretim Zamanı: 31 Oca 2025, 01:20:27
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `demo1`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `adminler`
--

CREATE TABLE `adminler` (
  `yonetici_id` int(11) NOT NULL,
  `giris_yapildi` int(11) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `avans_talepleri`
--

CREATE TABLE `avans_talepleri` (
  `id` int(11) NOT NULL,
  `calisan_id` int(11) DEFAULT NULL,
  `aciklama` text DEFAULT NULL,
  `talep_durumu` varchar(255) DEFAULT 'Beklemede',
  `alep_edilen_tutar` decimal(10,2) DEFAULT NULL,
  `talep_tarihi` date DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `calisanlar`
--

CREATE TABLE `calisanlar` (
  `id` int(11) NOT NULL,
  `ad` text DEFAULT NULL,
  `soyad` text DEFAULT NULL,
  `giris_yapildi` int(11) DEFAULT 0,
  `maas` text DEFAULT '0',
  `parola` text DEFAULT NULL,
  `tc_no` text DEFAULT NULL,
  `departman` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `izin_talepleri`
--

CREATE TABLE `izin_talepleri` (
  `id` int(11) NOT NULL,
  `baslangic_tarihi` text DEFAULT NULL,
  `bitis_tarihi` text DEFAULT NULL,
  `calisan_id` int(11) DEFAULT NULL,
  `izin_turu` varchar(255) DEFAULT NULL,
  `onay_durumu` enum('Beklemede','Reddedildi','Onaylandı') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanici_girisler`
--

CREATE TABLE `kullanici_girisler` (
  `id` int(11) NOT NULL,
  `ad` varchar(255) DEFAULT NULL,
  `giris_yapan_id` text DEFAULT NULL,
  `tarih` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `mesajlar`
--

CREATE TABLE `mesajlar` (
  `msj_id` int(11) NOT NULL,
  `alici` varchar(255) DEFAULT NULL,
  `gonderen` varchar(255) DEFAULT NULL,
  `msj` text DEFAULT NULL,
  `tarih` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `adminler`
--
ALTER TABLE `adminler`
  ADD PRIMARY KEY (`yonetici_id`);

--
-- Tablo için indeksler `avans_talepleri`
--
ALTER TABLE `avans_talepleri`
  ADD PRIMARY KEY (`id`),
  ADD KEY `calisan_id` (`calisan_id`);

--
-- Tablo için indeksler `calisanlar`
--
ALTER TABLE `calisanlar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `parola` (`parola`) USING HASH,
  ADD UNIQUE KEY `tc_no` (`tc_no`) USING HASH;

--
-- Tablo için indeksler `izin_talepleri`
--
ALTER TABLE `izin_talepleri`
  ADD PRIMARY KEY (`id`),
  ADD KEY `calisan_id` (`calisan_id`);

--
-- Tablo için indeksler `kullanici_girisler`
--
ALTER TABLE `kullanici_girisler`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `mesajlar`
--
ALTER TABLE `mesajlar`
  ADD PRIMARY KEY (`msj_id`);

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `avans_talepleri`
--
ALTER TABLE `avans_talepleri`
  ADD CONSTRAINT `avans_talepleri_ibfk_1` FOREIGN KEY (`calisan_id`) REFERENCES `calisanlar` (`id`);

--
-- Tablo kısıtlamaları `izin_talepleri`
--
ALTER TABLE `izin_talepleri`
  ADD CONSTRAINT `izin_talepleri_ibfk_1` FOREIGN KEY (`calisan_id`) REFERENCES `calisanlar` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
