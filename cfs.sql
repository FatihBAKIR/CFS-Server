SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE TABLE IF NOT EXISTS `addons` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `file` TEXT NULL,
  `active` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB AUTO_INCREMENT=5;

CREATE TABLE IF NOT EXISTS `apiSessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `sessionHash` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=612 ;

CREATE TABLE IF NOT EXISTS `ayarlar` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ayarisim` text,
  `deger` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin5 AUTO_INCREMENT=7 ;

CREATE TABLE IF NOT EXISTS `indirmeler` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `dosya` int(10) DEFAULT '0',
  `ip` text,
  `zaman` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin5 AUTO_INCREMENT=1996 ;

CREATE TABLE IF NOT EXISTS `klasorler` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `kullanici` int(10) DEFAULT '0',
  `ust_klasor` int(10) DEFAULT '0',
  `public` int(10) DEFAULT '0',
  `sifre` text COLLATE utf8_unicode_ci,
  `isim` text COLLATE utf8_unicode_ci,
  `aktif` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=42 ;

CREATE TABLE IF NOT EXISTS `kullanicilar` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `kullanici` text,
  `sifre` text,
  `mail` text,
  `dosya_limiti` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin5 AUTO_INCREMENT=24 ;

CREATE TABLE IF NOT EXISTS `sahiplik` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `dosya` int(10) DEFAULT '0',
  `kullanici` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin5 AUTO_INCREMENT=199 ;

CREATE TABLE IF NOT EXISTS `userSettings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `prefkey` text NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

CREATE TABLE IF NOT EXISTS `versioning` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `base_file` int(11) NOT NULL,
  `file` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35 ;

CREATE TABLE IF NOT EXISTS `yuklemeler` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `aktif` int(10) NOT NULL DEFAULT '1',
  `zaman` int(10) DEFAULT '0',
  `dosyaismi` text,
  `baslik` text,
  `depolanandosya` text,
  `sifre` text,
  `contenttype` text,
  `boyut` int(100) DEFAULT NULL,
  `ip` text,
  `dizin` int(11) DEFAULT '0',
  `pub` int(11) NOT NULL,
  `tags` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin5 AUTO_INCREMENT=211 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
