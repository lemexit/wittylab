-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 25, 2017 at 06:45 PM
-- Server version: 10.1.10-MariaDB
-- PHP Version: 7.0.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wittylab`
--

-- --------------------------------------------------------

--
-- Table structure for table `lit_ads`
--

CREATE TABLE `lit_ads` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` enum('728','468','300','resp','preroll') DEFAULT NULL,
  `code` text,
  `impression` int(12) DEFAULT '0',
  `enabled` enum('0','1') DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lit_blog`
--

CREATE TABLE `lit_blog` (
  `id` int(11) NOT NULL,
  `userid` int(11) DEFAULT '1',
  `approved` int(1) DEFAULT '1',
  `publish` int(1) DEFAULT '1',
  `slug` varchar(255) DEFAULT NULL,
  `name` varchar(250) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(300) DEFAULT NULL,
  `content` text,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lit_category`
--

CREATE TABLE `lit_category` (
  `id` bigint(11) NOT NULL,
  `parentid` bigint(12) DEFAULT '0',
  `type` varchar(16) DEFAULT NULL,
  `name` varchar(60) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `slug` varchar(60) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `lit_category`
--

INSERT INTO `lit_category` (`id`, `parentid`, `type`, `name`, `description`, `slug`) VALUES
(1, 0, 'video', 'Sports', '', 'sports'),
(2, 0, 'video', 'Movies', '', 'movies'),
(3, 0, 'video', 'Life Events', '', 'life-events'),
(4, 0, 'video', 'Public Events', '', 'public-events'),
(5, 0, 'video', 'Songs', '', 'songs'),
(6, 0, 'video', 'TV Shows', '', 'tv-shows'),
(7, 0, 'video', 'Others', '', 'others');

-- --------------------------------------------------------

--
-- Table structure for table `lit_comment`
--

CREATE TABLE `lit_comment` (
  `id` int(11) NOT NULL,
  `mediaid` bigint(11) DEFAULT NULL,
  `userid` bigint(11) DEFAULT NULL,
  `parentid` bigint(20) DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `body` text,
  `type` enum('media','post') DEFAULT 'media'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lit_favorite`
--

CREATE TABLE `lit_favorite` (
  `id` int(11) NOT NULL,
  `userid` bigint(11) UNSIGNED DEFAULT NULL,
  `mediaid` bigint(11) UNSIGNED DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lit_media`
--

CREATE TABLE `lit_media` (
  `id` int(11) NOT NULL,
  `uniqueid` varchar(255) DEFAULT NULL,
  `type` varchar(7) DEFAULT 'video',
  `catid` int(11) DEFAULT '1',
  `featured` int(11) DEFAULT '0',
  `title` varchar(128) DEFAULT NULL,
  `url` varchar(128) DEFAULT NULL,
  `description` text,
  `file` varchar(128) DEFAULT '',
  `link` text,
  `embed` text,
  `thumb` varchar(255) DEFAULT NULL,
  `ext_thumb` text,
  `userid` mediumint(8) DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `release_date` varchar(20) DEFAULT NULL,
  `nsfw` int(1) DEFAULT '0',
  `votes` int(12) DEFAULT '0',
  `views` bigint(100) DEFAULT '0',
  `tags` text,
  `approved` int(1) DEFAULT '1',
  `likes` bigint(12) DEFAULT '0',
  `dislikes` bigint(12) DEFAULT '0',
  `comments` int(11) DEFAULT '0',
  `source` text,
  `subscribe` int(1) DEFAULT '0',
  `duration` int(9) NOT NULL DEFAULT '0',
  `social` int(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `lit_media`
--

INSERT INTO `lit_media` (`id`, `uniqueid`, `type`, `catid`, `featured`, `title`, `url`, `description`, `file`, `link`, `embed`, `thumb`, `ext_thumb`, `userid`, `date`, `release_date`, `nsfw`, `votes`, `views`, `tags`, `approved`, `likes`, `dislikes`, `comments`, `source`, `subscribe`, `duration`, `social`) VALUES
(1, 'JSpyF', 'music', 1, 0, 'Kichu Bolo | Shayan | Bangla Song ', 'kichu-bolo-shayan-bangla-song-', 'Song : Kichu Bolo\r\nSinger : Shayan\r\nLyric : Shayan\r\nTune : Shayan\r\nMusic : Provhat\r\nStarring: Shahiduzzaman Rasel and Anika\r\nColor: HM Sohel\r\nEdit: Fokrul Islam\r\nDOP: Sani Khan\r\nDirector: Shamim Hossain\r\nFactory: Desh Media\r\nLabel : Eagle Music\r\n', '86a2da4e467e94e454bc8415653748b9.mp4', NULL, NULL, 'fa25dd99383802f5b62900b4f90c57c6.mp4', NULL, 2, '2017-12-17 13:40:00', 'Dec 23, 2017 14:00:3', 0, 0, 1, '', 1, 0, 0, 0, NULL, 0, 0, 0),
(2, 'VObKn', 'music', 1, 0, 'Brishty | Masha Islam | New Bangla Music Video ', 'brishty-masha-islam-new-bangla-music-video-', 'Song : Brishty\r\nAlbum: Tumi Chara Ghum\r\nSinger: Masha Islam\r\nLyricist: Shahrid Belal\r\nComposer: Shahrid Belal\r\nModel : Anan &amp; Arohi\r\nD O P : S.M. Tushar\r\nEdit &amp; FX : S.M. Tushar\r\nProduce By : Adbox BD Ltd.\r\nOnline Partner: Adbox BD Ltd.\r\nDirection: S.M. Tushar', 'ebad3861ca1ed471c36b62f8d53dce7d.mp4', NULL, NULL, 'b7c51f19ce8c65d591241929eeee9c55.mp4', NULL, 2, '2017-12-17 13:42:04', 'Dec 23, 2017 14:00:3', 0, 0, 6, '', 1, 0, 0, 0, NULL, 0, 0, 0),
(3, 'TQzg8', 'music', 1, 0, 'Panjabiwala | Tribute to Legend | Abdul Gafur Hali ', 'panjabiwala-tribute-to-legend-abdul-gafur-hali-', 'PRAN Potato Cracker Presents &quot;Panjabiwala &quot; (পাঞ্জাবিওলা). This Music Video is Tribute to &quot;Abdul Gafur Hali&quot;. \r\n\r\nSUBSCRIBE NOW: www.youtube.com/PRANSnacksTime\r\nFacebook Page: www.facebook.com/PranSnacksTime\r\n\r\nPanjabiwala (পাঞ্জাবিওলা) - A Musical Fiction by Redoan Rony \r\nOriginal Version-\r\nSong: Panjabiwala \r\nTune &amp; Music: Abdul Gafur Hali\r\nOriginal Singer: Abdul Gafur Hali\r\nLyric: Abdul Gafur Hali\r\n\r\nNew Version by PRAN Snacks Time-\r\nDirection: Redoan Rony\r\nMusic Re-Arrangement: Fatman Films \r\nCovered by: Dola\r\nStarring: Sarika and Zaib\r\nProduction: Popcornlive.TV', 'dc866ac4bb2ef5e9a10c121d511fbf3c.mp4', NULL, NULL, '9ed19c46e24eb099c643b58df0837d3a.mp4', NULL, 2, '2017-12-17 13:44:10', 'Dec 23, 2017 14:00:3', 0, 0, 3, '', 1, 0, 0, 0, NULL, 0, 0, 0),
(4, 'VoVIh', 'music', 1, 0, 'Hridoy Khan ft Sa Abir / valobasi tomay / official Song', 'hridoy-khan-ft-sa-abir-valobasi-tomay-official-song', 'A GMC Production\r\nSong : Valobasi Tomay\r\nSinger : Sa Abir\r\nLyrics, Tune, Music : Shafeek Ripu\r\nCast : Asikh, Diya, Shaidullah\r\nDop, edit : GMC Sohan\r\nConcept : Khan Saheb\r\nDirected By : GMC Sohan', '74f45da6d88d9a6bdd7f1c96a3a943de.mp4', NULL, NULL, 'c07721960a5ade1a52600ca0860d8936.mp4', NULL, 2, '2017-12-17 13:46:55', 'Dec 23, 2017 14:00:3', 0, 0, 4, '', 1, 0, 0, 0, NULL, 0, 0, 0),
(5, '9h5dG', 'music', 1, 0, ' Khuje Khuje | by Arfin Rumey & Porshi', '-khuje-khuje-by-arfin-rumey-porshi', 'Singer : Arfin Rumey &amp; Porshi\r\nAlbum : Porshi 2\r\nLyric : Anurup Aich\r\nMusic : Arfin Rumey\r\nVideo Production : Rommo Khan\r\nLabel : Agniveena', '43854ea113c4a67080e49d78b23c9f1f.mp4', NULL, NULL, '0b602a804c897824b239cdbb177b5a9d.mp4', NULL, 2, '2017-12-17 13:56:30', 'Dec 23, 2017 14:00:3', 0, 1, 1, '', 1, 1, 0, 0, NULL, 0, 0, 0),
(6, 'nGXYH', 'music', 1, 0, 'Jony Mohona [Directed by Shimul Hawladar', 'jony-mohona-directed-by-shimul-hawladar', 'Bangla HD Song  HD 1080p Full  Video Music \r\nyoutueb.www.Sumonlali.com', '5d5f07e41f54a258d82226dd9ac6f393.mp4', NULL, NULL, '6cee806d665f566668e2aee1eac560fc.mp4', NULL, 2, '2017-12-17 13:58:50', 'Dec 23, 2017 14:00:3', 0, 1, 3, '', 1, 1, 0, 0, NULL, 0, 0, 0),
(7, '2areP', 'music', 1, 0, 'Ichchey Manush | Full Music Video | Shawon Gaanwala | Bangla New Song | ', 'ichchey-manush-full-music-video-shawon-gaanwala-bangla-new-song-', 'Track : Ichchey Manush\r\nLyric  : Tushar Hasan\r\nVoice and Tune : Shawon GaanWala\r\nComposer : Amzad Hossain\r\nMusic Label : eTunes\r\n\r\nRBT Codes : \r\nGP, Robi, Airtel, TeleTalk - 5427356\r\nBanglalink - 565415\r\n\r\nDirector: Shahrear Polock\r\nDOP: Nazmul Hasan\r\nCast : Sallha Khanam Nadia, Farhan Ahmed Jovan\r\nProduction : prekkHa greeHoo visual factory', '36858139e23908ec15b6b2e9697df229.mp4', NULL, NULL, '25e5f8fc141e0b7fd25fc176a57e9bbd.mp4', NULL, 2, '2017-12-17 14:01:55', 'Dec 23, 2017 14:00:3', 0, 1, 2, '', 1, 1, 0, 0, NULL, 0, 0, 0),
(11, 'q6wtW', 'video', 5, 0, 'Wittylab', 'wittylab', '', 'af61a055d6f5277f26f958bd68ac22e8.mp4', NULL, NULL, '8fcd3ce248258a5a3db051f0539c36d4.mp4', NULL, 1, '2017-12-22 20:29:58', 'Dec 27, 2017 14:00:3', 0, 1, 8, NULL, 1, 1, 0, 0, NULL, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `lit_page`
--

CREATE TABLE `lit_page` (
  `id` int(11) NOT NULL,
  `publish` int(1) DEFAULT '1',
  `slug` varchar(255) DEFAULT NULL,
  `name` varchar(250) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(300) DEFAULT NULL,
  `content` text,
  `menu` int(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lit_playlist`
--

CREATE TABLE `lit_playlist` (
  `id` int(11) NOT NULL,
  `uniqueid` varchar(255) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `lastid` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `public` int(11) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `num` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lit_point`
--

CREATE TABLE `lit_point` (
  `id` int(11) NOT NULL,
  `userid` int(11) DEFAULT NULL,
  `actionid` int(12) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `point` int(11) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lit_rating`
--

CREATE TABLE `lit_rating` (
  `id` int(11) NOT NULL,
  `userid` mediumint(8) UNSIGNED DEFAULT '0',
  `mediaid` mediumint(8) UNSIGNED DEFAULT '0',
  `rating` varchar(10) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `lit_rating`
--

INSERT INTO `lit_rating` (`id`, `userid`, `mediaid`, `rating`, `date`) VALUES
(1, 1, 6, 'liked', '2017-12-18 11:10:27'),
(5, 2, 7, 'liked', '2017-12-23 12:54:47'),
(3, 2, 5, 'liked', '2017-12-18 20:01:28'),
(11, 2, 11, 'liked', '2017-12-24 21:17:02');

-- --------------------------------------------------------

--
-- Table structure for table `lit_setting`
--

CREATE TABLE `lit_setting` (
  `config` varchar(255) NOT NULL,
  `value` longtext
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `lit_setting`
--

INSERT INTO `lit_setting` (`config`, `value`) VALUES
('url', 'http://192.168.0.101/wittylab/main'),
('title', 'Wittylab'),
('description', 'Video Social Networking'),
('keywords', 'video social networking'),
('logo', 'auto_site_logo.png'),
('default_lang', ''),
('email', 'imemrul@gmail.com'),
('twitter', ''),
('facebook', ''),
('google', ''),
('require_activation', '1'),
('captcha', '0'),
('maintenance', '0'),
('homelimit', '8'),
('pagelimit', '16'),
('rsslimit', '25'),
('sharing', '1'),
('shorturl', 'google'),
('custom_shorturl', ''),
('comments', '1'),
('comment_sys', 'facebook'),
('disqus_username', ''),
('comment_blacklist', 'fuck, madarchod'),
('ads', '0'),
('ad300', ''),
('ad468', ''),
('ad728', ''),
('adrep', ''),
('adpreroll', ''),
('preroll_timer', ''),
('user', '1'),
('submission', '2'),
('user_activate', '0'),
('captcha_public', ''),
('captcha_private', ''),
('fb_connect', '1'),
('facebook_app_id', ''),
('facebook_secret', ''),
('tw_connect', '1'),
('twitter_secret', ''),
('twitter_key', ''),
('gl_connect', '1'),
('google_cid', ''),
('google_cs', ''),
('offline_message', ''),
('theme', 'bioscoop'),
('local_thumbs', '1'),
('font', ''),
('update_notification', '0'),
('smtp', '{"host":"","port":"","user":"admin","pass":"admin"}'),
('autoapprove', '1'),
('max_size', '50'),
('mode', 'bioscoop'),
('type', '{"blog":"0","video":"1","music":"0","vine":"0","picture":"0","post":"0"}'),
('player', 'videojs'),
('points', '0'),
('amount_points', '{"submit":"100","comment":"2","register":"50","like":"5","subscribe":"25"}'),
('menus', ''),
('plugins', ''),
('extra', ''),
('upload', '1'),
('ga', ''),
('color', '#f8cb1c'),
('yt_api', ''),
('vm_api', ''),
('dm_api', ''),
('merge_comments', '1'),
('aws', ''),
('api', '1'),
('api_key', '70Ypol5z'),
('count_media', '8'),
('s3_bucket', ''),
('s3_public', ''),
('s3_private', ''),
('s3_region', ''),
('s3', '0'),
('perrow', '3'),
('carousel', '0');

-- --------------------------------------------------------

--
-- Table structure for table `lit_subscription`
--

CREATE TABLE `lit_subscription` (
  `id` int(11) NOT NULL,
  `authorid` int(11) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lit_temp`
--

CREATE TABLE `lit_temp` (
  `id` int(11) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `filter` varchar(10) DEFAULT NULL,
  `data` text,
  `viewed` int(1) DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='This table is used to store temporary data.';

--
-- Dumping data for table `lit_temp`
--

INSERT INTO `lit_temp` (`id`, `type`, `filter`, `data`, `viewed`, `date`) VALUES
(1, 'notification', '2', '{"type":"liked","user":"1","media":6}', 0, '2017-12-18 11:10:27'),
(5, 'notification', '2', '{"type":"liked","user":"2","media":7}', 0, '2017-12-23 12:54:47'),
(3, 'notification', '2', '{"type":"liked","user":"2","media":5}', 0, '2017-12-18 20:01:28'),
(11, 'notification', '1', '{"type":"liked","user":"2","media":11}', 0, '2017-12-24 21:17:02');

-- --------------------------------------------------------

--
-- Table structure for table `lit_toplaylist`
--

CREATE TABLE `lit_toplaylist` (
  `id` int(11) NOT NULL,
  `playlistid` int(11) DEFAULT NULL,
  `mediaid` int(11) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lit_user`
--

CREATE TABLE `lit_user` (
  `id` int(255) NOT NULL,
  `auth` varchar(255) DEFAULT 'system',
  `authid` varchar(255) DEFAULT NULL,
  `name` varchar(60) DEFAULT '',
  `dob` varchar(255) DEFAULT NULL,
  `admin` int(1) DEFAULT '0' COMMENT 'Admin?',
  `username` varchar(20) DEFAULT '',
  `email` varchar(50) DEFAULT '',
  `mobile` varchar(11) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `active` int(1) DEFAULT '0',
  `lastlogin` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `country` varchar(255) DEFAULT '',
  `digest` int(1) DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `verifno` varchar(20) DEFAULT NULL,
  `auth_key` varchar(255) DEFAULT NULL,
  `public` int(1) DEFAULT '1',
  `profile` text,
  `subscribers` bigint(20) DEFAULT '0',
  `points` bigint(100) DEFAULT '0',
  `nsfw` int(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `lit_user`
--

INSERT INTO `lit_user` (`id`, `auth`, `authid`, `name`, `dob`, `admin`, `username`, `email`, `mobile`, `password`, `avatar`, `active`, `lastlogin`, `country`, `digest`, `date`, `verifno`, `auth_key`, `public`, `profile`, `subscribers`, `points`, `nsfw`) VALUES
(1, 'system', NULL, 'Mr. Admin', NULL, 1, 'admin', 'imemrul@gmail.com', NULL, '$2a$08$N0oQF5b.uzNbsxiVl3TAoe07sDvLtVuZXxxV3AOT4.DW3cWavAiJi', NULL, 1, '2017-12-25 08:18:11', '', 0, '2017-12-17 00:14:52', 'czIlQFOn3xsPYVIP3jQJ', '$2a$08$j7bVEXcZL2ihAWAqAGap3uW2jnYOnmHi4rUweHw1EkuVo/EvoAM5W', 1, NULL, 0, 0, 0),
(2, 'system', NULL, 'Mr Rana', '1985-01-15', 0, 'rana', 'rana@site.com', NULL, '$2a$08$V3G0BRbI0DXgzz3WPeZ6YOHaS8zchqsST.0.enEZZI9UGj1yzJjlW', 'ZINOJgHA_avatar.jpg', 1, '2017-12-25 09:36:46', 'bd', 0, '2017-12-17 00:33:26', 'Vg4wBE61Ls2OFGXm', '$2a$08$dpqunLCq.hr/VOtJMzMhMOq6X3gsqkfv2h/vprXiRL7Vqst.XIRNm', 1, '{"name":"Rana","description":"","cover":"cover-2.jpg"}', 0, 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lit_ads`
--
ALTER TABLE `lit_ads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lit_blog`
--
ALTER TABLE `lit_blog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lit_category`
--
ALTER TABLE `lit_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lit_comment`
--
ALTER TABLE `lit_comment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lit_favorite`
--
ALTER TABLE `lit_favorite`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lit_media`
--
ALTER TABLE `lit_media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`),
  ADD KEY `userid` (`userid`);
ALTER TABLE `lit_media` ADD FULLTEXT KEY `title` (`title`,`description`,`tags`);

--
-- Indexes for table `lit_page`
--
ALTER TABLE `lit_page`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lit_playlist`
--
ALTER TABLE `lit_playlist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lit_point`
--
ALTER TABLE `lit_point`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lit_rating`
--
ALTER TABLE `lit_rating`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lit_setting`
--
ALTER TABLE `lit_setting`
  ADD PRIMARY KEY (`config`);

--
-- Indexes for table `lit_subscription`
--
ALTER TABLE `lit_subscription`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lit_temp`
--
ALTER TABLE `lit_temp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lit_toplaylist`
--
ALTER TABLE `lit_toplaylist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lit_user`
--
ALTER TABLE `lit_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nick` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lit_ads`
--
ALTER TABLE `lit_ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `lit_blog`
--
ALTER TABLE `lit_blog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `lit_category`
--
ALTER TABLE `lit_category`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `lit_comment`
--
ALTER TABLE `lit_comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `lit_favorite`
--
ALTER TABLE `lit_favorite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `lit_media`
--
ALTER TABLE `lit_media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `lit_page`
--
ALTER TABLE `lit_page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `lit_playlist`
--
ALTER TABLE `lit_playlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `lit_point`
--
ALTER TABLE `lit_point`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `lit_rating`
--
ALTER TABLE `lit_rating`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `lit_subscription`
--
ALTER TABLE `lit_subscription`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `lit_temp`
--
ALTER TABLE `lit_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `lit_toplaylist`
--
ALTER TABLE `lit_toplaylist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `lit_user`
--
ALTER TABLE `lit_user`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
