CREATE TABLE `supercookie` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(30) DEFAULT NULL,
`browser` varchar(20) DEFAULT NULL,
`OS` varchar(20) DEFAULT NULL,
`pngData` varchar(20) DEFAULT NULL,
`cookieData` varchar(20) DEFAULT NULL,
`userData` varchar(20) DEFAULT NULL,
`sessionData` varchar(20) DEFAULT NULL,
`windowData` varchar(20) DEFAULT NULL,
`historyData` varchar(20) DEFAULT NULL,
`etagData` varchar(20) DEFAULT NULL,
`globalData` varchar(20) DEFAULT NULL,
`cacheData` varchar(20) DEFAULT NULL,
`idbData` varchar(20) DEFAULT NULL,
`lsoData` varchar(20) DEFAULT NULL,
`slData` varchar(20) DEFAULT NULL,
`dbData` varchar(20) DEFAULT NULL,
`localData` varchar(20) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

