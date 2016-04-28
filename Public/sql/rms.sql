DROP TABLE IF EXISTS  `rms_access`;
CREATE TABLE `rms_access` (
  `role_id` smallint(6) unsigned NOT NULL,
  `node_id` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) NOT NULL,
  `module` varchar(50) DEFAULT NULL,
  KEY `groupId` (`role_id`) USING BTREE,
  KEY `nodeId` (`node_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
TRUNCATE TABLE `rms_access`;
insert into `rms_access`(`role_id`,`node_id`,`level`,`module`) values
('5','54','1',null),
('4','60','2',null),
('4','58','1',null),
('4','56','1',null),
('4','53','1',null),
('4','50','1',null),
('4','35','1',null),
('4','34','1',null),
('4','33','1',null),
('4','29','1',null),
('4','16','1',null),
('4','28','1',null),
('4','27','1',null),
('5','53','1',null),
('5','49','1',null),
('5','43','1',null),
('5','42','1',null),
('5','35','1',null),
('5','34','1',null),
('5','33','1',null),
('5','31','1',null),
('5','29','1',null),
('4','26','2',null),
('7','50','1',null),
('7','49','1',null),
('6','57','1',null),
('6','56','1',null),
('6','55','1',null),
('6','54','1',null),
('6','52','1',null),
('6','43','1',null),
('6','40','1',null),
('6','38','1',null),
('5','16','1',null),
('5','28','1',null),
('5','27','1',null),
('5','26','2',null),
('5','25','2',null),
('5','24','2',null),
('6','36','1',null),
('6','35','1',null),
('6','34','1',null),
('6','30','1',null),
('6','16','1',null),
('5','19','2',null),
('6','19','2',null),
('6','1','1',null),
('7','48','1',null),
('7','47','1',null),
('7','46','1',null),
('7','45','1',null),
('7','43','1',null),
('7','42','1',null),
('7','40','1',null),
('7','39','1',null),
('7','35','1',null),
('7','34','1',null),
('7','33','1',null),
('7','32','1',null),
('7','31','1',null),
('7','30','1',null),
('7','29','1',null),
('7','16','1',null),
('7','28','1',null),
('7','27','1',null),
('7','26','2',null),
('7','25','2',null),
('4','25','2',null),
('4','24','2',null),
('7','53','1',null),
('7','54','1',null),
('7','56','1',null),
('5','1','1',null),
('4','19','2',null),
('4','1','1',null),
('8','36','1',null),
('8','38','1',null),
('8','40','1',null);

DROP TABLE IF EXISTS  `rms_bookaction`;
CREATE TABLE `rms_bookaction` (
  `bookactionid` int(10) NOT NULL AUTO_INCREMENT,
  `bookorderid` int(10) NOT NULL,
  `action` varchar(500) DEFAULT NULL,
  `logtime` varchar(30) NOT NULL,
  PRIMARY KEY (`bookactionid`),
  KEY `bookactionid` (`bookactionid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=70 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_bookdate`;
CREATE TABLE `rms_bookdate` (
  `bookdateid` int(10) NOT NULL AUTO_INCREMENT,
  `bookorderid` int(10) NOT NULL,
  `bookdate` varchar(50) DEFAULT NULL,
  `logtime` varchar(30) NOT NULL,
  PRIMARY KEY (`bookdateid`),
  KEY `bookdateid` (`bookdateid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_bookorder`;
CREATE TABLE `rms_bookorder` (
  `bookorderid` int(19) NOT NULL AUTO_INCREMENT COMMENT '订单号',
  `ordersn` varchar(50) NOT NULL COMMENT '订单编号',
  `clientname` varchar(20) DEFAULT NULL,
  `address` varchar(400) NOT NULL,
  `telphone` varchar(20) NOT NULL,
  `ordertxt` varchar(400) NOT NULL,
  `beizhu` varchar(400) NOT NULL,
  `totalmoney` decimal(10,2) NOT NULL,
  `custtime` varchar(10) NOT NULL,
  `custdate` varchar(20) NOT NULL COMMENT '日期',
  `ap` varchar(10) NOT NULL,
  `datetxt` varchar(100) NOT NULL COMMENT '日期简述',
  `lastdatetime` datetime NOT NULL,
  `telname` varchar(20) NOT NULL,
  `rectime` varchar(20) NOT NULL COMMENT '录入时间',
  `recdate` varchar(20) NOT NULL COMMENT '录入日期',
  `state` varchar(10) NOT NULL,
  `invoiceheader` varchar(400) NOT NULL COMMENT '发票抬头',
  `invoicebody` varchar(20) NOT NULL COMMENT '发票内容',
  `shippingname` varchar(50) NOT NULL COMMENT '送餐方式',
  `shippingmoney` decimal(8,2) NOT NULL COMMENT '送餐费',
  PRIMARY KEY (`bookorderid`),
  UNIQUE KEY `bookorderid` (`bookorderid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=85 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_bookproducts`;
CREATE TABLE `rms_bookproducts` (
  `bookproductsid` int(19) NOT NULL AUTO_INCREMENT COMMENT '订货id',
  `bookorderid` int(19) NOT NULL COMMENT '预订单号',
  `code` varchar(10) CHARACTER SET utf8 NOT NULL,
  `name` varchar(20) CHARACTER SET utf8 NOT NULL,
  `shortname` varchar(20) CHARACTER SET utf8 NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `number` int(10) NOT NULL,
  `money` decimal(8,2) NOT NULL,
  PRIMARY KEY (`bookproductsid`),
  UNIQUE KEY `bookproductsid` (`bookproductsid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=59 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_companymgr`;
CREATE TABLE `rms_companymgr` (
  `companymgrid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `distributionCode` char(1) NOT NULL COMMENT '分配代码',
  `printtype` varchar(20) NOT NULL COMMENT '打印类型',
  PRIMARY KEY (`companymgrid`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_messages`;
CREATE TABLE `rms_messages` (
  `messagesid` int(10) NOT NULL AUTO_INCREMENT,
  `content` varchar(400) NOT NULL COMMENT '内容',
  `receiver` varchar(20) NOT NULL COMMENT '接收者 用户',
  `status` int(1) NOT NULL,
  `sender` varchar(20) NOT NULL COMMENT '发送者',
  `time` varchar(20) NOT NULL COMMENT '发送时间',
  PRIMARY KEY (`messagesid`)
) ENGINE=MyISAM AUTO_INCREMENT=310 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_module`;
CREATE TABLE `rms_module` (
  `moduleid` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `presence` int(19) NOT NULL DEFAULT '1',
  PRIMARY KEY (`moduleid`),
  UNIQUE KEY `tab_name_idx` (`name`) USING BTREE,
  KEY `tab_tabid_idx` (`moduleid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
TRUNCATE TABLE `rms_module`;
insert into `rms_module`(`moduleid`,`name`,`presence`) values
('1','Notice','0'),
('2','Telcustomer','0'),
('3','Products','0'),
('4','OrderForm','0'),
('5','OrderHandle','0'),
('6','OrderDistribution','0'),
('7','OrderFormHandle','1'),
('8','BookOrder','0'),
('9','OrderHistory','0'),
('14','OrderMonit','0'),
('10','SendnameMgr','0'),
('11','CompanyMgr','0'),
('12','SmsMgr','0'),
('13','User','0'),
('15','GeneralReport','0'),
('16','OrderPrintHandle','0'),
('17','OrderDeliver','0'),
('18','Role','0'),
('19','Node','0'),
('20','Messages','0'),
('21','Organization','0'),
('22','SecondPointMgr','0'),
('23','OrderSecondPoint','0'),
('24','TodayMenu','0'),
('25','Zhuangxiang','0'),
('26','InvoiceMgr','0'),
('27','ProductsMonit','0'),
('28','SendnameProducts','0');

DROP TABLE IF EXISTS  `rms_node`;
CREATE TABLE `rms_node` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `remark` varchar(255) DEFAULT NULL,
  `sort` smallint(6) unsigned DEFAULT NULL,
  `pid` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `level` (`level`) USING BTREE,
  KEY `name` (`name`) USING BTREE,
  KEY `pid` (`pid`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

TRUNCATE TABLE `rms_node`;
insert into `rms_node`(`id`,`name`,`title`,`status`,`remark`,`sort`,`pid`,`level`) values
('1','Notice','公告','1',null,null,'0','1'),
('27','Telcustomer','来电客户管理','1',null,'1','0','1'),
('28','Products','产品','1',null,'1','0','1'),
('16','Messages','群发消息','1',null,'1','0','1'),
('19','index','列表','0',null,null,'1','2'),
('24','createview','新建公告','0',null,null,'1','2'),
('25','editview','编辑公告','0',null,null,'1','2'),
('26','delete','删除公告','0',null,null,'1','2'),
('29','OrderForm','订餐单','1',null,'1','0','1'),
('30','OrderHandle','订单配送','1',null,'1','0','1'),
('31','OrderDistribution','订单分配','1',null,'1','0','1'),
('32','OrderFormHandle','订餐配送','1',null,'1','0','1'),
('33','BookOrder','订单预订','1',null,'1','0','1'),
('34','OrderMonit','订单情况','1',null,'1','0','1'),
('35','OrderHistory','历史订单','1',null,'1','0','1'),
('36','OrderPrintHandle','打印派单','1',null,'1','0','1'),
('38','OrderDeliver','发货单','1',null,'1','0','1'),
('39','OrderSecordPoint','分送点配送','1',null,'1','0','1'),
('40','SendnameMgr','送餐员管理','1',null,'1','0','1'),
('42','CompanyMgr','配送店管理','1',null,'1','0','1'),
('43','SmsMgr','短信管理','1',null,'1','0','1'),
('44','SecondPointMgr','分送点管理','1',null,'1','0','1'),
('45','User','用户管理','1',null,'1','0','1'),
('46','Role','角色管理','1',null,'1','0','1'),
('47','Node','节点管理','1',null,'1','0','1'),
('48','Organization','组织机构管理','1',null,'1','0','1'),
('49','GeneralReport','常用报表','1',null,'1','0','1'),
('50','Telphone','来电显示','1',null,'1','0','1'),
('53','TodayMenu',' 今日菜单','1',null,'1','0','1'),
('52','printer','打印功能','1',null,'1','0','1'),
('54','ProductsMonit','餐售情况','1',null,'1','0','1'),
('55','Zhuangxiang','装箱单','1',null,'1','0','1'),
('56','SendnameProducts','送餐情况','1',null,'1','0','1'),
('57','InvoiceMgr','发票管理','1',null,'1','0','1'),
('58','Telphone','来电显示','1',null,'1','0','1'),
('60','CCLinkServer','华旗呼叫中心','0',null,null,'58','2');

--
DROP TABLE IF EXISTS  `rms_notice`;
CREATE TABLE `rms_notice` (
  `noticeid` int(10) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`noticeid`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_orderaction`;
CREATE TABLE `rms_orderaction` (
  `actionid` int(10) NOT NULL AUTO_INCREMENT,
  `orderformid` int(10) NOT NULL,
  `action` varchar(500) DEFAULT NULL,
  `logtime` varchar(30) NOT NULL,
  PRIMARY KEY (`actionid`)
) ENGINE=MyISAM AUTO_INCREMENT=45611 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_orderbook`;
CREATE TABLE `rms_orderbook` (
  `orderbookid` int(19) NOT NULL AUTO_INCREMENT COMMENT '订单号',
  `clientname` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `address` varchar(400) CHARACTER SET utf8 NOT NULL,
  `telphone` varchar(20) CHARACTER SET utf8 NOT NULL,
  `ordertxt` varchar(400) CHARACTER SET utf8 NOT NULL,
  `beizhu` varchar(400) CHARACTER SET utf8 NOT NULL,
  `totalmoney` decimal(10,2) NOT NULL,
  `custtime` varchar(10) CHARACTER SET utf8 NOT NULL,
  `ap` varchar(10) CHARACTER SET utf8 NOT NULL,
  `lastdatetime` datetime NOT NULL,
  `custname` varchar(20) CHARACTER SET utf8 NOT NULL,
  `company` varchar(20) CHARACTER SET utf8 NOT NULL,
  `telname` varchar(20) CHARACTER SET utf8 NOT NULL,
  `rectime` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT '录入时间',
  `recdate` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT '录入日期',
  `state` varchar(10) CHARACTER SET utf8 NOT NULL,
  `handlestate` int(1) NOT NULL,
  `distributionstate` int(1) NOT NULL DEFAULT '0' COMMENT '分配状态',
  PRIMARY KEY (`orderbookid`),
  UNIQUE KEY `orderbookid` (`orderbookid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_orderform`;
CREATE TABLE `rms_orderform` (
  `orderformid` int(19) NOT NULL AUTO_INCREMENT COMMENT '订单号',
  `ordersn` varchar(50) NOT NULL COMMENT '订单编号',
  `clientname` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `address` varchar(400) CHARACTER SET utf8 NOT NULL,
  `telphone` varchar(20) CHARACTER SET utf8 NOT NULL,
  `ordertxt` varchar(400) CHARACTER SET utf8 NOT NULL,
  `beizhu` varchar(400) CHARACTER SET utf8 NOT NULL,
  `totalmoney` decimal(10,2) NOT NULL,
  `custtime` varchar(10) CHARACTER SET utf8 NOT NULL,
  `custdate` varchar(20) NOT NULL COMMENT '日期',
  `ap` varchar(10) CHARACTER SET utf8 NOT NULL,
  `lastdatetime` datetime NOT NULL,
  `sendname` varchar(20) CHARACTER SET utf8 NOT NULL,
  `company` varchar(20) CHARACTER SET utf8 NOT NULL,
  `telname` varchar(20) CHARACTER SET utf8 NOT NULL,
  `rectime` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT '录入时间',
  `recdate` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT '录入日期',
  `state` varchar(10) CHARACTER SET utf8 NOT NULL,
  `hurrytime` varchar(20) NOT NULL,
  `hurrynumber` int(3) NOT NULL,
  `handlestate` int(1) NOT NULL,
  `distributionstate` int(1) NOT NULL DEFAULT '0' COMMENT '分配状态',
  `invoiceheader` varchar(400) CHARACTER SET utf8 NOT NULL COMMENT '发票抬头',
  `invoicebody` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT '发票内容',
  `shippingname` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT '送餐方式',
  `shippingmoney` decimal(8,2) NOT NULL COMMENT '送餐费',
  PRIMARY KEY (`orderformid`),
  UNIQUE KEY `orderformid` (`orderformid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=363 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_ordermonit`;
CREATE TABLE `rms_ordermonit` (
  `ordermonitid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `totalnumber` int(5) NOT NULL,
  `notask` int(5) NOT NULL,
  `task` int(5) NOT NULL,
  `fast` int(5) NOT NULL,
  `web` int(5) NOT NULL,
  `cancel` int(5) NOT NULL,
  `returnorder` int(5) NOT NULL,
  `totalmoney` decimal(8,2) NOT NULL,
  PRIMARY KEY (`ordermonitid`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_orderprintcontent`;
CREATE TABLE `rms_orderprintcontent` (
  `orderprintcontentid` int(10) NOT NULL AUTO_INCREMENT,
  `orderprinthandleid` int(10) NOT NULL,
  `orderformid` int(10) NOT NULL,
  `content` varchar(300) NOT NULL,
  PRIMARY KEY (`orderprintcontentid`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_orderprinter`;
CREATE TABLE `rms_orderprinter` (
  `orderprintid` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `orderformid` int(12) NOT NULL COMMENT '订单号',
  `custdate` varchar(20) NOT NULL,
  PRIMARY KEY (`orderprintid`)
) ENGINE=MyISAM AUTO_INCREMENT=3998 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='打印表id';
DROP TABLE IF EXISTS  `rms_orderprinthandle`;
CREATE TABLE `rms_orderprinthandle` (
  `orderprinthandle` int(10) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `shortontent` varchar(50) NOT NULL,
  PRIMARY KEY (`orderprinthandle`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_orderproducts`;
CREATE TABLE `rms_orderproducts` (
  `orderproductsid` int(19) NOT NULL AUTO_INCREMENT COMMENT '订货id',
  `orderformid` int(19) NOT NULL COMMENT '订单号',
  `code` varchar(10) CHARACTER SET utf8 NOT NULL,
  `name` varchar(20) CHARACTER SET utf8 NOT NULL,
  `shortname` varchar(20) CHARACTER SET utf8 NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `number` int(10) NOT NULL,
  `money` decimal(8,2) NOT NULL,
  PRIMARY KEY (`orderproductsid`),
  UNIQUE KEY `ordergoodsid` (`orderproductsid`) USING BTREE,
  KEY `phoneorderid` (`orderformid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=98520 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_orderstate`;
CREATE TABLE `rms_orderstate` (
  `orderstatusid` int(11) NOT NULL AUTO_INCREMENT,
  `orderformid` int(11) NOT NULL,
  `create` int(1) NOT NULL DEFAULT '0' COMMENT '订单生成',
  `createtime` varchar(20) NOT NULL COMMENT '订单生成时间',
  `createcontent` varchar(50) NOT NULL COMMENT '生成描述',
  `distribution` int(1) NOT NULL DEFAULT '0' COMMENT '分配',
  `distributiontime` varchar(20) NOT NULL COMMENT '分配时间',
  `distributioncontent` varchar(50) NOT NULL COMMENT '分配描述',
  `handle` int(1) NOT NULL DEFAULT '0' COMMENT '配送状态',
  `handletime` varchar(20) NOT NULL COMMENT '配送时间',
  `handlecontent` varchar(50) NOT NULL COMMENT '配送描述',
  `success` int(1) NOT NULL DEFAULT '0' COMMENT '完成状态',
  `successtime` varchar(20) NOT NULL COMMENT '完成时间',
  `successcontent` varchar(50) NOT NULL COMMENT '完成描述',
  `cancel` int(1) NOT NULL DEFAULT '0' COMMENT '退餐状态',
  `canceltime` varchar(20) NOT NULL COMMENT '退餐时间',
  `cancelcontent` varchar(50) NOT NULL COMMENT '退餐描述',
  PRIMARY KEY (`orderstatusid`)
) ENGINE=MyISAM AUTO_INCREMENT=23175 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_orderyingshouexchange`;
CREATE TABLE `rms_orderyingshouexchange` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `orderformid` int(10) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5753 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS  `rms_organization`;
CREATE TABLE `rms_organization` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `sort` smallint(6) unsigned DEFAULT NULL,
  `pid` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `level` (`level`) USING BTREE,
  KEY `name` (`name`) USING BTREE,
  KEY `pid` (`pid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

TRUNCATE TABLE `rms_organization`;
insert into `rms_organization`(`id`,`name`,`sort`,`pid`,`level`) values
('15','闸北','1','0','1'),
('2','客服中心','1','0','1'),
('3','调度',null,'2','2'),
('14','金桥','1','0','1'),
('12','工厂','1','0','1'),
('16','普陀','1','0','1'),
('18','杨浦','1','0','1'),
('19','闵行','1','0','1'),
('20','浦东','1','0','1'),
('21','黄浦','1','0','1'),
('22','江场','1','0','1'),
('23','徐汇','1','0','1'),
('24','长宁','1','0','1'),
('25','静安','1','0','1');


DROP TABLE IF EXISTS  `rms_products`;
CREATE TABLE `rms_products` (
  `productsid` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(50) NOT NULL COMMENT '产品名称',
  `shortname` varchar(20) NOT NULL,
  `code` char(10) NOT NULL COMMENT '代码',
  `price` decimal(8,2) NOT NULL COMMENT '销售价',
  `brief` varchar(400) NOT NULL COMMENT '产品简介',
  PRIMARY KEY (`productsid`)
) ENGINE=MyISAM AUTO_INCREMENT=144 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='订餐菜单表';
DROP TABLE IF EXISTS  `rms_productsmonit`;
CREATE TABLE `rms_productsmonit` (
  `productsmonitid` int(10) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`productsmonitid`),
  UNIQUE KEY `productsmonitid` (`productsmonitid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=474679 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_role`;
CREATE TABLE `rms_role` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `pid` smallint(6) DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `show_tab` varchar(20) NOT NULL COMMENT '显示的导航',
  `first_start_module` varchar(200) NOT NULL COMMENT '一开始启动的模块',
  `print_driver` varchar(1) NOT NULL DEFAULT '0' COMMENT '是否启动打印驱动',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

TRUNCATE TABLE `rms_role`;
insert into `rms_role`(`id`,`name`,`pid`,`status`,`remark`,`show_tab`,`first_start_module`,`print_driver`) values
('5','contactman',null,'0','联络员','Sales','Notice,OrderDistribution',''),
('3','admin',null,'0','系统管理员','System','User',''),
('4','telname',null,'0','接线员','Sales','Notice,OrderForm',''),
('6','dispatcher',null,'0','调度员','Sales','Notice,OrderHandle','1'),
('7','customer',null,'0','客服经理','','',''),
('8','shipping',null,'0','发货员','','','');

DROP TABLE IF EXISTS  `rms_role_user`;
CREATE TABLE `rms_role_user` (
  `role_id` mediumint(9) unsigned DEFAULT NULL,
  `user_id` mediumint(10) DEFAULT NULL,
  KEY `group_id` (`role_id`) USING BTREE,
  KEY `user_id` (`user_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

TRUNCATE TABLE `rms_role_user`;
insert into `rms_role_user`(`role_id`,`user_id`) values
('4','24'),
('3','3'),
('6','31'),
(null,null),
(null,null),
(null,null),
(null,null),
('0','32'),
('0','33'),
('6','34'),
('4','35'),
(null,null),
('4','37'),
('4','38'),
('4','39'),
('7','40'),
(null,null),
(null,null),
(null,null),
(null,null),
(null,null),
(null,null),
('5','36'),
('6','29'),
('6','30'),
('6','28'),
('6','27'),
('3','26'),
('4','41'),
('4','54'),
('6','70'),
('6','60'),
('6','73'),
('6','69'),
('6','68'),
('6','66'),
('6','67'),
('6','65'),
('6','64'),
('6','63'),
('6','62'),
('6','61'),
('4','58'),
('4','57'),
('4','56'),
('4','55'),
('5','53'),
('7','52');

DROP TABLE IF EXISTS  `rms_secondpointmgr`;
CREATE TABLE `rms_secondpointmgr` (
  `secondpointmgrid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `code` char(10) NOT NULL,
  PRIMARY KEY (`secondpointmgrid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_sendnamemgr`;
CREATE TABLE `rms_sendnamemgr` (
  `sendnamemgrid` int(10) NOT NULL AUTO_INCREMENT,
  `code` char(5) NOT NULL,
  `name` varchar(20) NOT NULL,
  `telphone` char(20) NOT NULL,
  `company` varchar(20) NOT NULL COMMENT '分公司名字',
  `sendtype` varchar(20) NOT NULL COMMENT '消息类型',
  PRIMARY KEY (`sendnamemgrid`)
) ENGINE=MyISAM AUTO_INCREMENT=250 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_sendnameproducts`;
CREATE TABLE `rms_sendnameproducts` (
  `sendnameproductsid` int(10) NOT NULL AUTO_INCREMENT,
  `productsname` varchar(40) NOT NULL COMMENT '产品名字',
  `shortname` varchar(40) NOT NULL,
  `extid` int(10) NOT NULL COMMENT '外部id',
  `type` varchar(20) NOT NULL,
  `number` int(5) NOT NULL,
  `sendname` varchar(20) NOT NULL,
  `company` varchar(20) DEFAULT NULL,
  `date` varchar(20) NOT NULL,
  `ap` varchar(10) NOT NULL,
  PRIMARY KEY (`sendnameproductsid`)
) ENGINE=MyISAM AUTO_INCREMENT=6759 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_session`;
CREATE TABLE `rms_session` (
  `session_id` varchar(255) NOT NULL,
  `session_expire` int(11) NOT NULL,
  `session_data` blob,
  `content` varchar(50) NOT NULL,
  PRIMARY KEY (`session_id`),
  UNIQUE KEY `session_id` (`session_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_smsmgr`;
CREATE TABLE `rms_smsmgr` (
  `smsmgrid` int(11) NOT NULL AUTO_INCREMENT,
  `telphone` varchar(20) NOT NULL COMMENT '电话号码',
  `content` varchar(500) NOT NULL COMMENT '发送内容',
  `firstdate` varchar(20) NOT NULL COMMENT '输入时间',
  `sendname` varchar(20) NOT NULL COMMENT '送餐员姓名',
  `issend` int(1) NOT NULL COMMENT '发送状态',
  `smstype` varchar(20) NOT NULL COMMENT '消息类型',
  `company` varchar(20) NOT NULL COMMENT '分公司',
  `orderformid` int(12) NOT NULL COMMENT '订单号',
  `senddate` varchar(20) NOT NULL COMMENT '发送时间',
  PRIMARY KEY (`smsmgrid`),
  KEY `smsmgrid` (`smsmgrid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5694 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `rms_tab`;
CREATE TABLE `rms_tab` (
  `tabid` int(19) NOT NULL,
  `tab_label` varchar(100) NOT NULL,
  `sequence` int(10) NOT NULL,
  `visible` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tabid`),
  KEY `parenttab_parenttabid_parenttabl_label_visible_idx` (`tabid`,`tab_label`,`visible`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

TRUNCATE TABLE `rms_tab`;
insert into `rms_tab`(`tabid`,`tab_label`,`sequence`,`visible`) values
('1','Home','1','0'),
('2','Accounts','2','0'),
('3','Products','3','0'),
('4','Sales','4','0'),
('5','Pos','5','0'),
('6','Base','6','0'),
('7','Reports','7','0'),
('8','System','8','0');


DROP TABLE IF EXISTS  `rms_tab_module_rel`;
CREATE TABLE `rms_tab_module_rel` (
  `tabid` int(19) NOT NULL,
  `moduleid` int(19) NOT NULL,
  `sequence` int(3) NOT NULL,
  KEY `fk_2_vtiger_parenttabrel` (`tabid`) USING BTREE,
  KEY `parenttabrel_tabid_parenttabid_idx` (`moduleid`,`tabid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

TRUNCATE TABLE `rms_tab_module_rel`;
insert into `rms_tab_module_rel`(`tabid`,`moduleid`,`sequence`) values
('4','8','5'),
('4','9','6'),
('6','11','2'),
('7','15','1'),
('6','12','3'),
('6','26','5'),
('7','27','3'),
('3','3','1'),
('7','14','2'),
('4','16','8'),
('4','17','9'),
('8','18','2'),
('8','19','3'),
('1','20','2'),
('8','21','4'),
('6','22','4'),
('4','23','10'),
('6','10','1'),
('3','24','2'),
('4','6','3'),
('4','5','2'),
('2','2','1'),
('4','4','1'),
('1','1','1'),
('4','28','12'),
('4','25','11'),
('4','7','4'),
('8','13','1');


DROP TABLE IF EXISTS  `rms_teladdress`;
CREATE TABLE `rms_teladdress` (
  `teladdressid` int(10) NOT NULL AUTO_INCREMENT,
  `telcustomerid` int(10) NOT NULL,
  `address` varchar(400) NOT NULL,
  `company` varchar(20) NOT NULL,
  PRIMARY KEY (`teladdressid`),
  UNIQUE KEY `addressid` (`teladdressid`) USING BTREE,
  KEY `telcustomerid` (`telcustomerid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=488686 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_telcustomer`;
CREATE TABLE `rms_telcustomer` (
  `telcustomerid` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(50) NOT NULL COMMENT '客户姓名',
  `telphone` varchar(20) NOT NULL,
  `address` varchar(400) NOT NULL,
  `rectime` varchar(20) NOT NULL,
  PRIMARY KEY (`telcustomerid`),
  UNIQUE KEY `accountsid` (`telcustomerid`) USING BTREE,
  KEY `telcustomerid` (`telcustomerid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=222572 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_telhistory`;
CREATE TABLE `rms_telhistory` (
  `telhistoryid` int(10) NOT NULL AUTO_INCREMENT,
  `telphone` char(20) NOT NULL,
  `telname` varchar(20) NOT NULL,
  `teltime` varchar(20) NOT NULL,
  `teldate` varchar(20) NOT NULL,
  `teltask` varchar(400) NOT NULL,
  PRIMARY KEY (`telhistoryid`)
) ENGINE=MyISAM AUTO_INCREMENT=510 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_telinvoice`;
CREATE TABLE `rms_telinvoice` (
  `telinvoiceid` int(11) NOT NULL AUTO_INCREMENT,
  `telcustomerid` int(11) NOT NULL,
  `header` varchar(400) NOT NULL COMMENT '发票抬头',
  PRIMARY KEY (`telinvoiceid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_todaymenu`;
CREATE TABLE `rms_todaymenu` (
  `todaymenuid` int(10) NOT NULL AUTO_INCREMENT,
  `date` varchar(20) NOT NULL COMMENT '日期',
  `content` text NOT NULL COMMENT '菜单内容',
  PRIMARY KEY (`todaymenuid`)
) ENGINE=MyISAM AUTO_INCREMENT=71 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_tongjiorder`;
CREATE TABLE `rms_tongjiorder` (
  `rms_tongjiorderid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `content` varchar(40) NOT NULL,
  `number` int(10) NOT NULL,
  `date` varchar(20) NOT NULL,
  `ap` varchar(10) NOT NULL,
  PRIMARY KEY (`rms_tongjiorderid`),
  KEY `rms_tongjiorderid` (`rms_tongjiorderid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2976 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_tongjiordernumber`;
CREATE TABLE `rms_tongjiordernumber` (
  `rms_tongjiproductsonid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8 NOT NULL,
  `content` varchar(40) CHARACTER SET utf8 NOT NULL,
  `number` int(10) NOT NULL,
  `date` varchar(20) CHARACTER SET utf8 NOT NULL,
  `ap` varchar(10) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`rms_tongjiproductsonid`),
  KEY `rms_tongjiproductsonid` (`rms_tongjiproductsonid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_tongjiproducts`;
CREATE TABLE `rms_tongjiproducts` (
  `rms_tongjiproductsid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `content` varchar(40) NOT NULL,
  `number` int(10) NOT NULL,
  `date` varchar(20) NOT NULL,
  `ap` varchar(10) NOT NULL,
  PRIMARY KEY (`rms_tongjiproductsid`),
  KEY `rms_tongjiproductsid` (`rms_tongjiproductsid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=105969 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_tongjiproductsnc`;
CREATE TABLE `rms_tongjiproductsnc` (
  `rms_tongjiproductsNCid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `content` varchar(40) NOT NULL,
  `number` int(10) NOT NULL,
  `date` varchar(20) NOT NULL,
  `ap` varchar(10) NOT NULL,
  PRIMARY KEY (`rms_tongjiproductsNCid`),
  KEY `rms_tongjiproductsNCid` (`rms_tongjiproductsNCid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_tongjiproductsnumber`;
CREATE TABLE `rms_tongjiproductsnumber` (
  `rms_productsnumbertongjiid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `content` varchar(40) NOT NULL,
  `number` int(10) NOT NULL,
  `date` varchar(20) NOT NULL,
  `ap` varchar(10) NOT NULL,
  PRIMARY KEY (`rms_productsnumbertongjiid`),
  UNIQUE KEY `rms_productsnumbertongjiid` (`rms_productsnumbertongjiid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_tongjiproductsordernumber`;
CREATE TABLE `rms_tongjiproductsordernumber` (
  `rms_tongjiproductsonid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8 NOT NULL,
  `content` varchar(40) CHARACTER SET utf8 NOT NULL,
  `number` int(10) NOT NULL,
  `date` varchar(20) CHARACTER SET utf8 NOT NULL,
  `ap` varchar(10) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`rms_tongjiproductsonid`),
  KEY `rms_tongjiproductsonid` (`rms_tongjiproductsonid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4004 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_tongjiproductstotalmoney`;
CREATE TABLE `rms_tongjiproductstotalmoney` (
  `rms_tongjiproductstmid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `content` varchar(40) NOT NULL,
  `number` int(10) NOT NULL,
  `date` varchar(20) NOT NULL,
  `ap` varchar(10) NOT NULL,
  PRIMARY KEY (`rms_tongjiproductstmid`),
  KEY `rms_tongjiproductstmid` (`rms_tongjiproductstmid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=6756 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_tongjitelname`;
CREATE TABLE `rms_tongjitelname` (
  `rms_tongjitelnameid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `content` varchar(20) NOT NULL,
  `number` int(10) NOT NULL,
  `date` varchar(20) NOT NULL,
  `ap` varchar(10) NOT NULL,
  PRIMARY KEY (`rms_tongjitelnameid`),
  KEY `rms_tongjitelnameid` (`rms_tongjitelnameid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1094 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_user`;
CREATE TABLE `rms_user` (
  `userid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `password` varchar(10) NOT NULL,
  `truename` varchar(30) NOT NULL COMMENT '真实姓名',
  `rolename` varchar(50) NOT NULL,
  `lastlog` varchar(20) NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM AUTO_INCREMENT=74 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

TRUNCATE TABLE `rms_user`;
insert into `rms_user`(`userid`,`name`,`password`,`truename`,`rolename`,`lastlog`) values
('59','admin','123','系统管理员','','2014-10-07 09:58:40');



DROP TABLE IF EXISTS  `rms_userorganization`;
CREATE TABLE `rms_userorganization` (
  `user_id` smallint(6) unsigned NOT NULL,
  `organization_id` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) NOT NULL,
  KEY `organizationId` (`organization_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;
DROP TABLE IF EXISTS  `rms_zhuangxiangaction`;
CREATE TABLE `rms_zhuangxiangaction` (
  `actionid` int(10) NOT NULL AUTO_INCREMENT,
  `zhuangxiangid` int(10) NOT NULL,
  `action` varchar(500) DEFAULT NULL,
  `logtime` varchar(30) NOT NULL,
  PRIMARY KEY (`actionid`),
  KEY `actionid` (`actionid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=91 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_zhuangxiangform`;
CREATE TABLE `rms_zhuangxiangform` (
  `zhuangxiangid` int(10) NOT NULL AUTO_INCREMENT,
  `code` char(20) NOT NULL,
  `name` varchar(40) NOT NULL,
  `totalmoney` decimal(8,2) NOT NULL,
  `state` varchar(20) NOT NULL,
  `ap` varchar(10) NOT NULL,
  `rectime` varchar(20) NOT NULL,
  `recdate` varchar(20) NOT NULL,
  `changtime` varchar(20) NOT NULL,
  `zhuangxiangtxt` varchar(50) NOT NULL,
  `company` varchar(20) NOT NULL,
  `inputname` varchar(20) NOT NULL COMMENT '输入者',
  PRIMARY KEY (`zhuangxiangid`)
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS  `rms_zhuangxiangproducts`;
CREATE TABLE `rms_zhuangxiangproducts` (
  `zhuangxiangproductsid` int(19) NOT NULL AUTO_INCREMENT COMMENT '订货id',
  `zhuangxiangid` int(19) NOT NULL COMMENT '订单号',
  `code` varchar(10) NOT NULL,
  `name` varchar(20) NOT NULL,
  `shortname` varchar(40) NOT NULL COMMENT '产品简称',
  `price` decimal(8,2) NOT NULL,
  `number` int(10) NOT NULL,
  `money` decimal(8,2) NOT NULL,
  PRIMARY KEY (`zhuangxiangproductsid`),
  UNIQUE KEY `zhuangxiangproductsid` (`zhuangxiangproductsid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=260 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
