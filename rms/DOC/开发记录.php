2012.05.20
  1、有些导航菜单(Tab)有主页是流程图，也有的直接指定模块菜单(Module)。
  2、导航菜单分为 客户 产品 销售 配送。
  3、目前分为 客户 产品  接单 分单 派单 五个主模块。先完成这5个模块。
  4、导航菜单(Tab)的表：rms_tab,试行。
  5、模块菜单(Module)的表:rms_module。
2012-07-02
  1、定制导航为 客户 产品 接单 分单 派单
  
2013-07-06 添加来电记录，要开始在合肥振华快餐使用了
2013-07-07 修复订单金额大于10000，溢出的问题。
2014-1-3   添加两个模块，打印派单和发货单
2014-03-04 增加企业组织机构模块，分为三级。表名：rms_organization,模块名称:organization
2014-04-20 从网站输入订单：/usr/local/php/bin/php /home/ftp/1520/lihuaerp-20140316-AYr/weborderdown/index.php
            自动备份订单：http://nj.lihuaerp.cn/index.php?s=BackupDatabase
2014-05-25 添加装修单模块 zhuangxiang
2014-05-29 产品增加简称。
2014-05-31 开始完成订单预订系统，添加 bookorder:预订表，bookdate:预订日期,bookproducts:预订产品;bookaction:预订日志。
           添加餐售情况 ProductsMonit
2014-07-02 订单模块列表加入快捷字段:listLinkField,
2014-07-07 在订单模块中，增加今日菜单快捷键ctrl+1,消息显示自身；
2014-07-09 添加来电历史记录表 telhistory
2014-07-10 添加打印表,是为了打印取得的ID:orderprinter
2014-07-20 修正订单分配和订单配送的光标。工作还没完成。
2014-07-21 接线系统添加F9，F10快捷键
2014-09-08 统计报表，加入了立即执行统计的代码
2014-09-13 改标题订餐管理系统改为送餐管理系统。上下午分割时间是15:30分。
2014-09-21 订餐单中，加入了百度地图的提示
2014-09-27 修复了打印只能在极速模式，不能在IE模式运行的问题，是LodopFuncs.JS没更新的缘故。
2014-09-29 开始想刷新，如何能更快一点的方案。
2014-10-03 开始重新设计刷新表，用调度名称和订单缓存的方案，建立两个表，缓存订单表:bufferordershow和客户缓存表:bufferclient,开始设计和测试。
2014-10-04 今天考虑一下，实现这样的刷新表，技术上有点复杂的，需要消息的来回确认，所以暂时不弄了。
2014-11-12 修改了订单打印，支持80宽度，加上了条码。修正装箱添加不能显示产品的问题。 
2014-11-25 重新完善订单统计的内容，1、添加大客户统计 ，2、完善客户数
2014-11-27 订单分配和订单配送的选择行的颜色，改为蓝色，好像不刺眼了。订单地址查询等列表，加入lastdatetime排序。
2014-12-03 增加接线中订单复制的功能。
2014-12-04 完善了接线、分配、配送功能中的查询返回的细节。
2014-12-05 修改分配、配送中的处理过程，改ajax返回处理为立即处理，后台修改。
           刷新分为快速刷新和慢速刷新。
2014-12-14 修改orderaction表，已复合状态发送给网站，app推送等的需要。
2014-12-21 修改orderform的附表：orderproducts,orderaction,orderstate，加入ordersn字段。
2014-12-24 分配，配送等，需要已ordersn字段为主。
2014-12-28 给各个模块加入ctrl快捷键。
2014-12-29 订餐模块加入退废餐查询，加入接线员订单量在PAGE中显示。
2015-01-01 实现了数据库分层架构。连接层->Model->Db
2015-01-11 分公司的快键键分布图：0、处理退餐、改单、催送   2、订单备注  3、转给其他分公司  4、单发消息  5、地址查询  6、送餐员查询  7、返回订单   8、打印订单  9、转给其他分公司
2015-01-20 重新添加TAB，1、合作平台  2、发票系统  3、营收结账
2015-01-21 定义TAB:发票系统:Invoice;营收结账：Revenue;合作平台：Cooperation; 
2015-02-06 修改订单保存，去掉可以选择分公司，避免出错。
2015-02-26 需要增加地区公司的属性模块，保存一下地区公司的属性信息，比如是否开启订单选择分公司的模块。  
           发现Tab的定义有错误，应该是nav的定义：导航，然后module是menu的定义；修改。
           1、修改rms_tab表，改为rms_nav,字段改为：tabid:navid,tab_label:navname，增加title提示帮助,方便编程
           2、修改rms_module，改为rms_menu，增加title中文提示，方便编程。
           3、修改rms_tab_module_rel 为rms_nav_menu  
2015-03-18 外部平台加入百度外卖：baiduwaimai;menu_id:30    
2015-03-23 网上抄的，修改jeasyui的js代码，提供datagrid的速度。  http://love398146779.iteye.com/blog/2067515
2015-03-26 修改网订下载，在地址中增加用户名称。
		   新建rms_otherplatform数据库，管理合作平台的数据，新建百度的商户信息和百度的菜单信息。
2016-01-03 修改中了组织编辑程序没有做好的部分。
2016-02-15 广州提出几个问题，进行修正。1、订单分配和订单配送的快捷键。2、订餐单的排序。3、订单分配综合查询优惠。
2016-02-24 订单部分，添加应收：shouldmoney,已收金额:paidmoney。还要添加来源origin字段。增加latitude维度和longitude经度。
2016-02-29 订单，增加两个从表，一个是活动表：rms_orderactivity,一个是支付表rms_orderpayment.
           表字段：orderactivityid,ordersn,orderformid,activityid,name,money,note,date
                  orderpaymentid,ordersn,orderformid,paymentid,name,money,note,date
2016-03-13 在订单表orderform中添加商品总额goodsmoney字段。
2016-03-16 修改送餐员表，改类型为微信号。改短信表，增加weixin字段。修改短信发送模块，支持微信。
2016-03-22 修改打印表，分公司打印，会出唯一打印序号，以便配合营收结账的需要。修改rms_orderprinter
2016-03-28 重新完善了接线系统，修正了一些错误，比如来电条等。
2016-04-04 完善接线中的快捷菜单。
2016-04-08 修正订单保存防止重复，送餐员管理，代码不能1位。删除orderformhandle模块。
2016-04-16 重写发票模块.增加两个表1:invoice发票,2:invoicecontent发票内容,3:发票开具日志invoiceaction
           1:invoice: invoiceid,header,ordersn,orderformtxt,ordertime,opentime,openname,state
           2:invoicecontentid,name,number,money
           3:invoiceationid,content,datetime,actionname
2016-04-18 把打印的确认状态从general.js改到orderhandle的listview中,加快速度.
2016-04-21 开始编写客户通知程序,通知客户订单确认,配送,完成等消息.表明:rms_client_notity
           1:clientnotifyid,ordersn,telphone,app_tk,contenttype,origin,domain 其中contentype包括(comfirm,sendname,complete)
2016-04-25 修改了装箱,在装箱产品中,添加分公司名称,产品等数据.
2016-04-28 开始使用github系统。


