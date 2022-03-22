-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Värd: 127.0.0.1
-- Tid vid skapande: 13 jun 2021 kl 09:44
-- Serverversion: 5.5.68-MariaDB
-- PHP-version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databas: `young_friends_org`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `yf_user`
--

DROP TABLE IF EXISTS `yf_user`;
CREATE TABLE IF NOT EXISTS `yf_user` (
  `autoId` bigint(20) unsigned NOT NULL,
  `uid` bigint(20) unsigned DEFAULT NULL,
  `tableKey` varchar(255) NOT NULL,
  `firstName` text,
  `sureName` text,
  `username` text NOT NULL,
  `email` text,
  `password` blob,
  `mobile` text,
  `phone` text,
  `address` text NOT NULL,
  `zip` int(11) NOT NULL DEFAULT '1',
  `city` text NOT NULL,
  `autoansv` int(11) NOT NULL DEFAULT '1',
  `synlig` int(11) NOT NULL DEFAULT '1',
  `regdate` datetime NOT NULL,
  `betalt` date DEFAULT NULL,
  `testmember` date DEFAULT NULL COMMENT 'För att markera period för provmedlemskap',
  `gender` tinyint(1) DEFAULT NULL COMMENT '0 = man 1= kvinna',
  `lastlogindate` datetime DEFAULT NULL COMMENT 'För att ha koll på när någon loggade in senast',
  `retrun` smallint(1) NOT NULL DEFAULT '0' COMMENT 'För att ha koll på om vi ska sändas om till start sidan för återkommande aktiviteter',
  `aemail` smallint(1) NOT NULL DEFAULT '1' COMMENT 'För att vi ska veta om vi ska sända ett email när någon anmäler/avanmäler sig till en aktivitet',
  `asms` smallint(1) NOT NULL DEFAULT '0' COMMENT 'För att vi ska veta om vi ska sända ett SMS när någon anmäler/avanmäler sig till en aktivitet'
) ENGINE=MyISAM AUTO_INCREMENT=177 DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `yf_user`
--

INSERT INTO `yf_user` (`autoId`, `uid`, `tableKey`, `firstName`, `sureName`, `username`, `email`, `password`, `mobile`, `phone`, `address`, `zip`, `city`, `autoansv`, `synlig`, `regdate`, `betalt`, `testmember`, `gender`, `lastlogindate`, `retrun`, `aemail`, `asms`) VALUES
(1, 1, '4C8XmE5HrCa9JE', 'Anders ', 'Wallin', 'volvo360', 'andwallin@gmail.com', 0x24327924313024534e44746c31392f4b494241446a667649754e2f6965416f474938734d706e4c45746e42584631322f4c784e4d4f38703471356232, '0705311224', '042-4573286', 'Kadettgatan 68 A', 25455, 'Helsingborg', 1, 1, '0000-00-00 00:00:00', '2022-03-31', NULL, 0, '2021-05-17 07:54:28', 1, 1, 1),
(53, 53, 'Dsp7wUXjBbT7Q9', 'Ed', 'Sundevåg', 'dykared', 'dykared@gmail.com', 0x3234326338386266363135623133393131366663316264656332613033343535, '0730438456', '042-145673', 'Hammarbergsgatan 3', 25453, 'Helsingborg', 1, 1, '2009-11-02 00:00:00', '2013-03-31', NULL, 0, '2012-03-30 12:13:43', 0, 1, 0),
(44, 44, 'Y7EXd3QywHe6pR', 'Kate', 'Nilsson', 'louise', 'nilsson.k@ektv.nu', 0x3531343136313138303732653839343037326336653739383537623035346130, '0709424185', '0431-14690', 'Storgatan 79 B', 26235, 'Ängelholm', 1, 1, '2009-08-10 00:00:00', '2022-03-31', NULL, 1, '2021-05-17 07:32:09', 0, 1, 0),
(6, 6, 'nu8zcWUEmYTSVe', 'Magnus', 'Wedin', 'wedin', 'wedinm@gmail.com', 0x6566303764663637646664356134373230306562323937396633653031303939, '0768816236', '', 'Öresundsgatan 32', 26138, 'Landskrona', 1, 1, '0000-00-00 00:00:00', '2022-03-31', NULL, 0, '2021-05-16 15:45:41', 0, 1, 0),
(42, 42, 'NQKwd2BStBpgy7', 'Pär', 'Lindkvist', 'pelle', 'par.lindkvist@passagen.se', 0x3535373433333166643066666362306132363865353966386335613063633564, '0730680733', '0418-24230', 'Storgatan 33', 26131, 'Landskrona', 1, 1, '2009-08-06 00:00:00', '2010-03-31', NULL, 0, '0000-00-00 00:00:00', 0, 1, 0),
(9, 9, '6hjXd79faD7jmd', 'Jörgen', 'Olsson', 'Deco', 'olsjor@bredband.net', 0x3535353837613931303838323031363332313230316536656262633966353935, '0704963186', '0418-59742', 'Skolallén 8', 26132, 'Landskrona', 1, 1, '2009-01-25 00:00:00', '2010-03-31', NULL, 0, '2009-02-10 19:15:06', 0, 1, 0),
(10, 10, 'B8Xx8XUDh9c3Kn', 'Patrik', 'Kumlin', 'patrikhbg', 'kumlinpatrik@gmail.com', 0x6566303764663637646664356134373230306562323937396633653031303939, '0733607195', '042-129982', 'Brunnbäcksgatan 23 B', 25231, 'Helsingborg', 1, 1, '2009-01-26 00:00:00', '2022-03-31', NULL, 0, '2021-05-15 13:39:28', 0, 1, 0),
(20, 20, 'DtAjmDVcqJ8XAZ', 'Pär', 'Andersson', 'Pär', 'per_andersson4@msn.com', 0x6566303764663637646664356134373230306562323937396633653031303939, '0730383534', '042-83534', 'Södra Storgatan 28C', 26740, 'Bjuv', 0, 1, '2009-02-16 00:00:00', '2017-03-31', NULL, 0, '2016-03-19 18:15:24', 0, 0, 0),
(41, 41, 'hEMWeh6A3d2Y8H', 'Eva', 'Pettersson', 'sseevpt', 'sseevpt@gmail.com', 0x6566303764663637646664356134373230306562323937396633653031303939, '0705690441', '', 'Häradsgatan 2d', 25659, 'Helsingborg', 1, 1, '2009-07-18 00:00:00', '2022-03-31', NULL, 1, '2021-05-16 13:55:50', 0, 1, 0),
(17, 17, 'W3cc5tBdr2JvYz', 'Anders', 'Wentrup', 'anwe', 'anders@hbglug.se', 0x3130613863303564356233623366636237313031303638333464323231653866, '0735286839', '', 'Borgsgatan 19', 26775, 'Ekeby', 1, 1, '2009-02-07 00:00:00', '2022-03-31', NULL, 0, '2021-02-12 14:17:13', 0, 1, 0),
(39, 39, '9d3y6UT9mnMPzH', 'Jenny', 'Olsson', 'balthzar', 'oljenny74@hotmail.com', 0x6637653261383433383434353363393037646330393863346332323764383832, '0706173875', '', 'Murargatan 10', 26137, 'Landskrona', 1, 1, '2009-03-06 00:00:00', '2012-03-31', NULL, 1, '2009-10-25 14:03:56', 0, 1, 0),
(0, 0, 'pQ52He95bKegqW', 'Young', 'Friends', 'YF', 'info@young-friends.org', '', '0739582465', '', 'Karmelitergatan 3', 26240, 'Landskrona', 1, 1, '0000-00-00 00:00:00', '2022-03-31', NULL, 1, '0000-00-00 00:00:00', 0, 1, 0),
(115, 115, 'aTGBcpKeMG5nRu', 'Christian', 'Malmquist', 'Chrismalm', 'chrismalm@hotmail.com', 0x6566303764663637646664356134373230306562323937396633653031303939, '0760008921', '076-0008921', 'Lägervägen 5B', 25456, 'Helsingborg', 1, 1, '2014-01-09 23:51:45', '2017-03-31', '0000-00-00', 0, '2017-01-04 17:31:41', 0, 1, 1),
(46, 46, 'wP6afJpHUAeewd', 'Annica', 'Holmgren', 'Annica', 'annicaholmgren@bredband2.com', 0x3762656536373062376663613830616232313030653133663965386464356437, '0709465109', '042-281410', 'Harlyckegatan 9A', 25658, 'Helsingborg', 1, 1, '2009-08-20 00:00:00', '2011-03-31', NULL, 1, '2010-12-17 19:30:08', 0, 1, 0),
(47, 47, 'YgXAQ8vgvrndVX', 'Ulf', 'Johannesson', 'offe', 'uffejo@gmail.com', 0x3933346235333538303062316362613866393661356437326637326631363131, '0703687848', '', 'DalhemsvÃ¤gen 131A', 25460, 'Helsingborg', 1, 1, '2009-09-03 00:00:00', '2010-03-31', NULL, 0, '2010-02-23 21:46:26', 0, 1, 0),
(49, 49, 'NHeUjEKbE8FnE6', 'Olov', 'Hansson', 'Usura', '007_@home.se', 0x3737383339643563663533643039653632626466636133643331373266616231, '0730718557', '042-121314', 'Karl X Gustavs gata 35', 25439, 'Helsingborg', 1, 1, '2009-09-13 00:00:00', '2012-03-31', NULL, 0, '2012-01-21 14:38:50', 0, 1, 0),
(50, 50, 'mfUjBTSN6dYhkQ', 'Mona', 'Arousell', 'mona', 'mona.arousell@gmail.com ', 0x6538376239633131343932643038373163323961383661333133346139346337, '0706938288', '', 'Forsby 2154B', 26492, 'Klippan', 1, 1, '2009-09-15 00:00:00', '2022-03-31', NULL, 1, '2021-05-13 21:00:39', 0, 1, 0),
(51, 51, 'J6374rY43fZbUz', 'Håkan ', 'Georgsson', 'Håkan', 'georgssonhakan@live.se', 0x6566303764663637646664356134373230306562323937396633653031303939, '0768711629', '043110521', 'Kristian II:s vÃ¤g 1a', 26234, 'Ängelholm', 1, 1, '2009-09-19 00:00:00', '2011-03-31', NULL, 0, '2011-02-20 09:56:43', 0, 1, 1),
(52, 52, 'wp3VWxpjc39BJs', 'Åsa', 'Wikenfalk', 'Åsa', 'asakmwik@gmail.com', 0x6566303764663637646664356134373230306562323937396633653031303939, '0708710875', '042-146525', 'Norrtäljegatan 32E', 25252, 'Helsingborg', 1, 1, '2009-09-20 00:00:00', '2022-03-31', NULL, 1, '2021-05-14 16:50:35', 0, 1, 0),
(55, 55, 'YvHEgPsJcx5WZe', 'Eva', 'Håkansson', 'Miss E', 'bothilda@hotmail.com', 0x6131326334663334656436353661666434373766356566383936323130623064, '0703945529', '0418-24761', 'Artillerigatan 7A', 26133, 'Landskrona', 1, 1, '2009-12-04 00:00:00', '2011-03-31', NULL, 1, '2010-10-15 20:53:27', 0, 1, 0),
(134, 134, 'mH2aRtKGeby3fn', 'Kristina', 'Djondric', 'kristina.d76', 'kristina.d76@hotmail.com', 0x3135616234363562303766316537373064326361373437636135363733383461, '0760190142', '', 'Vaktgatan 1B', 25456, 'Helsingborg', 1, 1, '2016-04-09 20:00:00', '2017-03-31', NULL, 1, '2016-09-23 19:04:11', 0, 1, 0),
(94, 94, 'RbNAPd62rZ5vTT', 'Mats', 'Jönsson', 'Mats', 'mats334@hotmail.com', 0x3930353338636131373836613037663833333331306331363766313936646139, '0701438852', '0701438852', 'Bollbrogatan 3', 25225, 'Helsingborg', 1, 1, '2011-06-10 16:54:33', '2012-03-31', '0000-00-00', 0, '2011-06-11 12:29:48', 0, 1, 0),
(95, 95, 'juteRz3UJXnpw5', 'Magnus', 'Gunnarsson', '', 'noreplay@young-friends.org', '', '0733227441', '', 'Ekeby vägen 254', 25592, 'Helsingborg', 1, 1, '2010-01-01 09:51:49', '2012-03-31', NULL, 0, '0000-00-00 00:00:00', 0, 1, 0),
(65, 65, 'Y68mY4VYyquKzW', 'Mia', 'Tenghagen', 'Mia', 'miatenghagen@gmail.com', 0x6637653261383433383434353363393037646330393863346332323764383832, '0709601075', '042-79033', 'Vallåkra gård 320 C', 26030, 'Vallåkra', 1, 1, '2010-02-08 00:00:00', '2011-03-31', NULL, 1, '0000-00-00 00:00:00', 0, 1, 0),
(64, 64, 'RBmc5ErGfFGAbZ', 'Markus', 'Tegsell', 'Markus', 'markustegsell@hotmail.com', 0x6637653261383433383434353363393037646330393863346332323764383832, '0708648139', '042-348180', 'Storgatan 50 B', 26331, 'Höganäs', 1, 1, '2010-02-09 00:00:00', '2011-03-31', NULL, 0, '2010-02-09 12:11:31', 0, 1, 0),
(107, 107, 'qgrVk4Kkc5VeQH', 'Niklas', 'Uneborg', 'niklasu', 'niklas.une@gmail.com2', 0x6566303764663637646664356134373230306562323937396633653031303939, '', '', 'Prinscarlsgatan 15A', 26337, 'Höganäs', 0, 1, '2012-04-11 23:54:30', '2013-03-31', '2018-09-10', 0, '2012-04-30 18:06:55', 0, 0, 0),
(68, 68, 'hwEq4qcG5KDJrb', 'Brigitte', 'Enfeldt', 'Bodhran', 'brigitteenfeldt@hotmail.com', 0x3633353431333330333164356262376662313238333236353730383561353938, '0705726200', '', 'O D Krooks gata 74', 25443, 'Helsingborg', 1, 1, '2010-03-26 15:02:40', '2011-03-31', NULL, 1, '2010-12-17 13:09:27', 0, 1, 0),
(106, 106, 'StX74zZdV9XGfs', 'Veronica', 'Nordgren', 'madde', 'madde45@hotmail.com', 0x6561613332633936663632303035336366343432616433323235383037366239, '0733130537', '042-57335', 'Östergatan 16G', 26531, 'Åstorp', 1, 1, '2012-03-27 16:45:07', '2013-03-31', '0000-00-00', 1, '2012-04-08 18:09:42', 0, 1, 0),
(83, 83, '3QDqg7yVbnrgMB', 'Camilla', 'Nilsson', 'Camilla', 'camnils980002@yahoo.se', 0x6566303764663637646664356134373230306562323937396633653031303939, '0730489925', '042-398200', 'Trädgårdsstigen 1B', 26030, 'Vallåkra', 1, 1, '2010-10-22 01:37:53', '2011-03-31', '0000-00-00', 1, '2010-12-25 22:32:59', 0, 1, 0),
(104, 104, '7TCmC9TaU4Sa9S', 'Patrik', 'Karlsson', 'jamiro', 'info@cemebo.se', 0x6566303764663637646664356134373230306562323937396633653031303939, '0702332326', '', 'Kollegievägen 38', 22473, 'Lund', 1, 1, '2012-02-11 09:55:14', '2022-03-31', '0000-00-00', 0, '2021-05-15 08:02:10', 0, 1, 0),
(88, 88, 'Zu4b4WYmJRNPCU', 'Tomas', 'Persson', 'Tomas', 'tomas_persson@hotmail.se', 0x6566303764663637646664356134373230306562323937396633653031303939, '0729890374', '0729890374', 'Möllevångsvägen 10', 22240, 'Lund', 1, 1, '2011-01-17 19:05:37', '2017-03-31', '0000-00-00', 0, '2017-01-27 23:40:11', 0, 1, 0),
(105, 105, 'qC6YC9UeXeenxw', 'x', 'x', 'Katerina', 'xxx@xxx', 0x3930393534333439613065343264386534343236613436373262646531366239, '', '', 'xxx', 0, 'xxx', 0, 0, '2012-03-20 19:12:59', '2017-03-31', '0000-00-00', 1, '2016-08-23 23:13:55', 0, 0, 0),
(79, 79, 'UCp5rJP68SuH3q', 'Carola', 'Pettersson', '', 'noreplay@young-friends.org', '', '0702106728', '', 'Fricksgatan 1C', 26252, 'Ängelholm', 0, 0, '2010-09-15 08:49:22', '2012-03-31', NULL, 1, '0000-00-00 00:00:00', 0, 0, 0),
(89, 89, 'Dxn3r9vRPFm8NF', 'Johan', 'Svensson', 'Splinter cell', 'johan.karlsfalt@telia.com', 0x6566303764663637646664356134373230306562323937396633653031303939, '0708619291', '', 'Karlsfält 1366', 26875, 'Tågarp', 1, 1, '2011-07-23 11:38:15', '2017-03-31', '0000-00-00', 0, '2013-06-21 16:01:49', 0, 1, 0),
(136, 136, 'WS87r8U58D7Zyc', 'Ann ', 'Gutenwik ', 'Ann Gutenwik ', 'annflieshigh@hotmail.com', 0x6437363231313939356562623339316239333135346435323537376163353130, '0708183285', '', 'Oregatan 21', 28834, 'Vinslöv ', 1, 1, '2016-05-26 21:08:54', '2022-03-31', '0000-00-00', 1, '2020-10-15 10:15:07', 0, 1, 0),
(125, 125, 'EpY28nArnWzmyK', 'Magnus', 'Agerström', 'Magnus', 'magnus@agerstrom.se', 0x3534383538626632613934333730363034656332613236326432356463363634, '0733667141', '', 'Övre Nytorgsgatan 78B', 25249, 'Helsingborg', 1, 1, '2015-01-10 01:13:11', '2016-03-31', '0000-00-00', 0, '2016-02-02 19:57:27', 0, 1, 0),
(155, 155, 'NPYAg4EGvxZz2m', 'Daniel', 'Lindblad', 'mythdaniel', 'daniel_90_lindblad@hotmail.com', 0x3761323662396237363031373461323833303830326236653830366236303665, '0730486931', '043314062', 'Majenfors PL 3426', 28593, 'Markaryd', 1, 1, '2017-10-04 17:36:21', '2019-03-31', '0000-00-00', 0, '2019-01-12 00:28:55', 0, 1, 0),
(149, 149, '39R3UQDYunwE9D', 'Magnus ', 'Demoling', 'MagnusD', 'magnusdemoling@hotmail.com', 0x3230643739613764393839366465633565346662616565666431353565623761, '0768424193', '', 'Karlsgatan 7', 25224, 'Helsingborg', 1, 1, '2017-04-24 20:03:08', '2018-03-31', '0000-00-00', 0, '2017-05-18 21:43:37', 0, 1, 0),
(148, 148, 'X7f9bbsXpQCqPb', 'Fredrik', 'Hjärpe', 'FredrikH', 'fredrik.hjarpe@telia.com', 0x6431613932636161343533613065316532633935386261386136376638653066, '0706042887', '0706042887', 'Kielergatan, 28C', 25269, 'Råå', 1, 1, '2017-04-07 14:43:12', '2022-03-31', '0000-00-00', 0, '2021-01-29 21:34:53', 0, 1, 1),
(142, 142, '76h25KmpVwYFFT', 'Anna', 'Nilsson ', 'Anna82', 'anna-victum@hotmail.com', 0x6566303764663637646664356134373230306562323937396633653031303939, '0730810031', '042-283424', 'V. Fridhemsgatan 8B', 25229, 'Helsingborg', 1, 1, '2017-01-08 15:06:03', '2021-03-31', '0000-00-00', 1, '2021-03-31 18:14:14', 0, 1, 0),
(165, 165, 'atQ52gygncJJHQ', 'Niklas', 'une', 'niklasune', 'niklas.une@gmail.com', 0x6566303764663637646664356134373230306562323937396633653031303939, '0725347781', '0725347781', 'Lergatan', 26336, 'Höganäs', 1, 1, '2018-08-18 15:13:38', '2022-03-31', NULL, 0, '2020-09-10 19:41:31', 0, 1, 0),
(163, 163, 'eHeeEFvw8QTSWP', 'Jörgen', 'Laursen', 'jorgenl', 'anders@hbglug.se', 0x6566303764663637646664356134373230306562323937396633653031303939, '0723313394', '', 'Södra hunnetorpsvägen 108', 25662, 'Helsingborg', 0, 1, '2018-06-07 00:00:00', '2022-03-31', NULL, 0, '2018-06-29 13:08:03', 0, 0, 0),
(169, 169, 'h24Nn9jRZ4sms3', 'Johan', 'Gustavi', 'johan', 'jogustavi@gmail.com', 0x6361623239643665363530373936613666313963376633623837373438393334, '0707197071', '', 'Brunnsallén 27', 25657, 'Ramlösa', 1, 1, '2019-07-21 11:03:23', '2022-03-31', NULL, 0, '2019-09-06 09:45:02', 0, 1, 0),
(174, 174, 'r9dYtt4J6jTNCn', 'Martin', 'Wanerholm', 'Martinw', 'wanerholm.martin@telia.com', 0x3131336632346365316337343766653565636662343365333030623566396561, '0702490161', '042-121335', 'Skånegatan 21', 25247, 'Helsingborg', 1, 1, '2020-03-15 20:54:44', '2021-03-31', NULL, 0, NULL, 0, 1, 0),
(175, 175, '7tEf5enDHvN8wz', 'Joachim', 'Warfvinge', 'Joachim', 'joachim.warfvinge@yahoo.com', 0x3239643161643830343539373836363734313366386362383736646132633834, '0768663661', '0768663661', 'Kolonigatan 4', 26333, 'Höganäs', 1, 1, '2020-03-16 12:48:59', NULL, '2020-05-08', 0, NULL, 0, 1, 0),
(176, 176, '6CNH2n2RVKCHtg', 'Rikard', 'Nilsson', 'riknil', 'rikard.n@hotmail.com', 0x6134303230313861356535376438653036303339613763653538636430353262, '0708-720014', '0708-720014', 'Valhallagatan 18b', 26162, 'Glumslöv', 1, 1, '2020-04-17 08:27:09', NULL, '2020-05-08', 0, NULL, 0, 1, 0);

--
-- Index för dumpade tabeller
--

--
-- Index för tabell `yf_user`
--
ALTER TABLE `yf_user`
  ADD PRIMARY KEY (`autoId`);

--
-- AUTO_INCREMENT för dumpade tabeller
--

--
-- AUTO_INCREMENT för tabell `yf_user`
--
ALTER TABLE `yf_user`
  MODIFY `autoId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=177;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
