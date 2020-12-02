2012-01-13
   RMS����˼�����綩�͹���ϵͳ��
2014-09-29  设想
            刷新，产品的内容提前放到数组中，这个，移动光标的速度可以快一点。
            设置一个刷新的标示表，以用户的session作为识别的手段，然后在里面建立一个缓存区，只有当里面的内容改变，才可以刷新客户端的内容。
            这样，可以大大节省流量。


            /*------- INSERT SQL---------*/
insert into `rms_orderform_05` (`orderformid`,`ordersn`,`clientname`,`address`,`telphone`,`ordertxt`,`beizhu`,`totalmoney`,`paidmoney`,`shouldmoney`,`goodsmoney`,`custtime`,`custdate`,`ap`,`lastdatetime`,`sendname`,`company`,`telname`,`rectime`,`recdate`,`state`,`hurrytime`,`hurrynumber`,`handlestate`,`distributionstate`,`invoiceheader`,`invoicebody`,`invoicetype`,`gmf_nsrsbh`,`gmf_dzdh`,`gmf_yhzh`,`shippingname`,`shippingmoney`,`longitude`,`latitude`,`sendlongitude`,`sendlatitude`,`origin`,`app_tk`,`sendtype`,`domain`,`successtime`,`isjiezhang`,`jiezhangmoney`,`needjiezhang`)
 values(<orderformid>,<ordersn>,<clientname>,<address>,<telphone>,<ordertxt>,<beizhu>,<totalmoney>,<paidmoney>,<shouldmoney>,<goodsmoney>,<custtime>,<custdate>,<ap>,<lastdatetime>,<sendname>,<company>,<telname>,<rectime>,<recdate>,<state>,<hurrytime>,<hurrynumber>,<handlestate>,<distributionstate>,<invoiceheader>,<invoicebody>,<invoicetype>,<gmf_nsrsbh>,<gmf_dzdh>,<gmf_yhzh>,<shippingname>,<shippingmoney>,<longitude>,<latitude>,<sendlongitude>,<sendlatitude>,<origin>,<app_tk>,<sendtype>,<domain>,<successtime>,<isjiezhang>,<jiezhangmoney>,<needjiezhang>);

/*------- UPDATE SQL---------*/
update `rms_orderform_05` SET 
    `orderformid`=<value>,
    `ordersn`=<value>,
    `clientname`=<value>,
    `address`=<value>,
    `telphone`=<value>,
    `ordertxt`=<value>,
    `beizhu`=<value>,
    `totalmoney`=<value>,
    `paidmoney`=<value>,
    `shouldmoney`=<value>,
    `goodsmoney`=<value>,
    `custtime`=<value>,
    `custdate`=<value>,
    `ap`=<value>,
    `lastdatetime`=<value>,
    `sendname`=<value>,
    `company`=<value>,
    `telname`=<value>,
    `rectime`=<value>,
    `recdate`=<value>,
    `state`=<value>,
    `hurrytime`=<value>,
    `hurrynumber`=<value>,
    `handlestate`=<value>,
    `distributionstate`=<value>,
    `invoiceheader`=<value>,
    `invoicebody`=<value>,
    `invoicetype`=<value>,
    `gmf_nsrsbh`=<value>,
    `gmf_dzdh`=<value>,
    `gmf_yhzh`=<value>,
    `shippingname`=<value>,
    `shippingmoney`=<value>,
    `longitude`=<value>,
    `latitude`=<value>,
    `sendlongitude`=<value>,
    `sendlatitude`=<value>,
    `origin`=<value>,
    `app_tk`=<value>,
    `sendtype`=<value>,
    `domain`=<value>,
    `successtime`=<value>,
    `isjiezhang`=<value>,
    `jiezhangmoney`=<value>,
    `needjiezhang`=<value>
  where xxx = xxx;

/*------- SELECT SQL---------*/
select 
    `orderformid`,
    `ordersn`,
    `clientname`,
    `address`,
    `telphone`,
    `ordertxt`,
    `beizhu`,
    `totalmoney`,
    `paidmoney`,
    `shouldmoney`,
    `goodsmoney`,
    `custtime`,
    `custdate`,
    `ap`,
    `lastdatetime`,
    `sendname`,
    `company`,
    `telname`,
    `rectime`,
    `recdate`,
    `state`,
    `hurrytime`,
    `hurrynumber`,
    `handlestate`,
    `distributionstate`,
    `invoiceheader`,
    `invoicebody`,
    `invoicetype`,
    `gmf_nsrsbh`,
    `gmf_dzdh`,
    `gmf_yhzh`,
    `shippingname`,
    `shippingmoney`,
    `longitude`,
    `latitude`,
    `sendlongitude`,
    `sendlatitude`,
    `origin`,
    `app_tk`,
    `sendtype`,
    `domain`,
    `successtime`,
    `isjiezhang`,
    `jiezhangmoney`,
    `needjiezhang`
  from `rms_orderform_05`
  where xxx = xxx;

/*------- CREATE SQL---------*/
CREATE TABLE `rms_orderform_05` (
  `orderformid` int(19) NOT NULL COMMENT '订单号',
  `ordersn` varchar(50) NOT NULL COMMENT '订单编号',
  `clientname` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `address` varchar(400) CHARACTER SET utf8 NOT NULL,
  `telphone` varchar(20) CHARACTER SET utf8 NOT NULL,
  `ordertxt` varchar(400) CHARACTER SET utf8 NOT NULL,
  `beizhu` varchar(400) CHARACTER SET utf8 NOT NULL,
  `totalmoney` decimal(10,2) NOT NULL,
  `paidmoney` decimal(10,2) DEFAULT NULL,
  `shouldmoney` decimal(10,2) DEFAULT NULL,
  `goodsmoney` decimal(10,2) DEFAULT NULL,
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
  `invoicetype` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `gmf_nsrsbh` char(20) NOT NULL DEFAULT '',
  `gmf_dzdh` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT ' ',
  `gmf_yhzh` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT ' ',
  `shippingname` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT '送餐方式',
  `shippingmoney` decimal(8,2) NOT NULL COMMENT '送餐费',
  `longitude` varchar(32) CHARACTER SET utf8 DEFAULT NULL COMMENT '订单经度坐标',
  `latitude` varchar(32) CHARACTER SET utf8 DEFAULT NULL COMMENT '订单维度坐标',
  `sendlongitude` varchar(32) CHARACTER SET utf8 DEFAULT NULL COMMENT '配送员经度坐标',
  `sendlatitude` varchar(32) CHARACTER SET utf8 DEFAULT NULL COMMENT '配送员维度坐标',
  `origin` varchar(32) CHARACTER SET utf8 DEFAULT NULL COMMENT '来源',
  `app_tk` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `sendtype` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `domain` char(20) NOT NULL,
  `successtime` char(20) DEFAULT NULL,
  `isjiezhang` int(1) NOT NULL DEFAULT '0',
  `jiezhangmoney` decimal(18,2) NOT NULL DEFAULT '0.00',
  `needjiezhang` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ordersn`),
  KEY `ordersn` (`ordersn`),
  KEY `address` (`address`(255)),
  KEY `telphone` (`telphone`),
  KEY `IDX_DOMAIN_CUSTDATE` (`domain`,`custdate`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC




 $sql = "insert into rms_orderform_05 (orderformid,ordersn,clientname,address,telphone,ordertxt,beizhu,totalmoney,paidmoney,shouldmoney,goodsmoney,
                    custtime,custdate,ap,lastdatetime,sendname,company,telname,rectime,recdate,state,hurrytime,hurrynumber,handlestate,distributionstate,invoiceheader,invoicebody,invoicetype,gmf_nsrsbh,gmf_dzdh,gmf_yhzh,shippingname,shippingmoney,longitude,latitude,sendlongitude,sendlatitude,origin,app_tk,sendtype,domain,successtime,isjiezhang,jiezhangmoney,needjiezhang)
 values (<orderformid>,<ordersn>,<clientname>,<address>,<telphone>,<ordertxt>,<beizhu>,<totalmoney>,<paidmoney>,<shouldmoney>,<goodsmoney>,<custtime>,<custdate>,<ap>,<lastdatetime>,<sendname>,<company>,<telname>,<rectime>,<recdate>,<state>,<hurrytime>,<hurrynumber>,<handlestate>,<distributionstate>,<invoiceheader>,<invoicebody>,<invoicetype>,<gmf_nsrsbh>,<gmf_dzdh>,<gmf_yhzh>,<shippingname>,<shippingmoney>,<longitude>,<latitude>,<sendlongitude>,<sendlatitude>,<origin>,<app_tk>,<sendtype>,<domain>,<successtime>,<isjiezhang>,<jiezhangmoney>,<needjiezhang>);
"