<?php
  预订表
  CREATE TABLE IF NOT EXISTS `rms_bookorder` (
  `bookorderid` int(19) NOT NULL auto_increment COMMENT '订单号',
  `ordersn` varchar(50) NOT NULL COMMENT '订单编号',
  `clientname` varchar(20) character set utf8 default NULL,
  `address` varchar(400) character set utf8 NOT NULL,
  `telphone` varchar(20) character set utf8 NOT NULL,
  `ordertxt` varchar(400) character set utf8 NOT NULL,
  `beizhu` varchar(400) character set utf8 NOT NULL,
  `totalmoney` decimal(10,2) NOT NULL,
  `custtime` varchar(10) character set utf8 NOT NULL,
  `custdate` varchar(20) NOT NULL COMMENT '日期',
  `ap` varchar(10) character set utf8 NOT NULL,
  `lastdatetime` datetime NOT NULL,
  `telname` varchar(20) character set utf8 NOT NULL,
  `rectime` varchar(20) character set utf8 NOT NULL COMMENT '录入时间',
  `recdate` varchar(20) character set utf8 NOT NULL COMMENT '录入日期',
  `state` varchar(10) character set utf8 NOT NULL,
  `invoiceheader` varchar(400) character set utf8 NOT NULL COMMENT '发票抬头',
  `invoicebody` varchar(20) character set utf8 NOT NULL COMMENT '发票内容',
  `shippingname` varchar(50) character set utf8 NOT NULL COMMENT '送餐方式',
  `shippingmoney` decimal(8,2) NOT NULL COMMENT '送餐费',
  PRIMARY KEY  (`bookorderid`),
  UNIQUE KEY `bookorderid` (`bookorderid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `rms_bookproducts` (
  `bookproductsid` int(19) NOT NULL auto_increment COMMENT '订货id',
  `bookorderid` int(19) NOT NULL COMMENT '预订单号',
  `code` varchar(10) character set utf8 NOT NULL,
  `name` varchar(20) character set utf8 NOT NULL,
  `shortname` varchar(20) character set utf8 NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `number` int(10) NOT NULL,
  `money` decimal(8,2) NOT NULL,
  PRIMARY KEY  (`bookproductsid`),
  UNIQUE KEY `bookproductsid` (`bookproductsid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `rms_bookaction` (
  `bookactionid` int(10) NOT NULL auto_increment,
  `bookorderid` int(10) NOT NULL,
  `action` varchar(500) default NULL,
  `logtime` varchar(30) NOT NULL,
  PRIMARY KEY  (`bookactionid`),
  KEY `bookactionid` (`bookactionid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


//预订日期
CREATE TABLE IF NOT EXISTS `rms_bookdate` (
  `bookdateid` int(10) NOT NULL auto_increment,
  `bookorderid` int(10) NOT NULL,
  `bookdate` varchar(50) default NULL,
  `logtime` varchar(30) NOT NULL,
  PRIMARY KEY  (`bookdateid`),
  KEY `bookdateid` (`bookdateid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


//产品销售统计
CREATE TABLE IF NOT EXISTS `rms_productsmonit` (
  `productsmonitid` int(10) NOT NULL auto_increment,
  `name` varchar(30) NOT NULL,
  `p1` varchar(20) NOT NULL,
  `p2` varchar(20) NOT NULL,
  `p3` varchar(20) NOT NULL,
  `p4` varchar(20) NOT NULL,
  `p5` varchar(20) NOT NULL,
  `p6` varchar(20) NOT NULL,
  `p7` varchar(20) NOT NULL,
  `p8` varchar(20) NOT NULL,
  `p9` varchar(20) NOT NULL,
  `p10` varchar(20) NOT NULL,
  `p11` varchar(20) NOT NULL,
  `p12` varchar(20) NOT NULL,
  `p13` varchar(20) NOT NULL,
  `p14` varchar(20) NOT NULL,
  `p15` varchar(20) NOT NULL,
  `p16` varchar(20) NOT NULL,
  `p17` varchar(20) NOT NULL,
  `p18` varchar(20) NOT NULL,
  `p19` varchar(20) NOT NULL,
  `p20` varchar(20) NOT NULL,
  `p21` varchar(20) NOT NULL,
  `p22` varchar(20) NOT NULL,
  `p23` varchar(20) NOT NULL,
  `p24` varchar(20) NOT NULL,
  `p25` varchar(20) NOT NULL,
  `p26` varchar(20) NOT NULL,
  `p27` varchar(20) NOT NULL,
  `p28` varchar(20) NOT NULL,
  `p29` varchar(20) NOT NULL,
  `p30` varchar(20) NOT NULL,
  `p31` varchar(20) NOT NULL,
  `p32` varchar(20) NOT NULL,
  `p33` varchar(20) NOT NULL,
  `p34` varchar(20) NOT NULL,
  `p35` varchar(20) NOT NULL,
  `p36` varchar(20) NOT NULL,
  `p37` varchar(20) NOT NULL,
  `p38` varchar(20) NOT NULL,
  `p39` varchar(20) NOT NULL,
  `p40` varchar(20) NOT NULL,
  `p41` varchar(20) NOT NULL,
  `p42` varchar(20) NOT NULL,
  `p43` varchar(20) NOT NULL,
  `p44` varchar(20) NOT NULL,
  `p45` varchar(20) NOT NULL,
  `p46` varchar(20) NOT NULL,
  `p47` varchar(20) NOT NULL,
  `p48` varchar(20) NOT NULL,
  `p49` varchar(20) NOT NULL,
  `p50` varchar(20) NOT NULL, 
  PRIMARY KEY  (`productsmonitid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

?>
