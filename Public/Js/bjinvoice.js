/**
 * Created by zhangwh1234 on 2016-04-28.
 * 北京发票的操作代码
 */


(function(window, document, undefined) {

    var InvoiceOper = {
        kpdriver:null,
        isOpen:false,
        isSetup:true,

        openCard:function(){
            if (this.isOpen){
                return true;
            }
            if (!this.isSetup){
                return false;
            }
            try{
                CollectGarbage();

                // V2.0
                this.kpdriver.CertPassWord='88888888';

                this.kpdriver.OpenCard();
                window.console.info(this.kpdriver.RetCode);
                if(this.kpdriver.RetCode=="1011"){
                    this.isOpen = true;
                    return true;
                }else if (this.kpdriver.RetCode=="1007"){
                    alert("金税卡已经被占用，请退出所有可能使用金税卡的程序后重试");
                }else if (this.kpdriver.RetCode=="1001"){
                    alert("开票信息初始化失败，请重启浏览器或重装开票系统后重试");
                }else if (this.kpdriver.RetCode=="1005"){
                    this.closeCard();
                    this.checkSetup();
                }else {
                    alert("金税卡开启失败，错误代码："+this.kpdriver.RetCode);
                }
                return false;
            }catch(e) {
                alert("开票组件出错，错误信息为open："+e.description);
                closeCard();
                return false;
            }
        },

        checkSetup:function(){
            try{
                if (this.kpdriver==undefined || this.kpdriver==null){
                    this.kpdriver = new ActiveXObject("TaxCardX.GoldTax");
                }
            }catch(e) {
                if (e.description=="Automation 服务器不能创建对象"){
                    alert("您没有安装开票组件或组件被禁用，无法使用此功能。");
                } else {
                    alert("开票组件出错，错误信息为setup："+e.description);
                }
                this.isSetup = false;
                this.closeCard();
                return false;
            }
            return true;
        },

        closeCard:function(){
            if (this.kpdriver!=undefined && this.kpdriver!=null){
                try{
                    this.kpdriver.CloseCard();
                }catch(e){}
                finally{
                    this.kpdriver = null;
                    setTimeout(CollectGarbage, 100);
                }
            }
            this.isOpen = false;
        },

        getInfo:function(infoKind){
            this.kpdriver.InfoKind = infoKind;
            this.kpdriver.GetInfo();
            var result = {};
            result.infoTypeCode = this.kpdriver.InfoTypeCode;
            result.infoNumber = this.kpdriver.InfoNumber;
            result.invStock = this.kpdriver.InvStock;
            result.taxClock = this.kpdriver.TaxClock;
            return result;
        },

        invoice:function(invoiceInfo){
            try{
                var main=invoiceInfo.main;
                var cCustType=invoiceInfo.ccusttype;
                this.kpdriver.InvInfoInit();
                this.kpdriver.InfoKind = main.invtype;
                if(main.invtype == 0 || main.invtype == 2){
                    //购方名称
                    this.kpdriver.InfoClientName = main.clientname;
                    //购方税号
                    if (main.clienttaxcode)
                    {
                        this.kpdriver.InfoClientTaxCode = main.clienttaxcode;
                    }
                    //购方银行账号
                    if (main.clientbankaccount!=null){
                        this.kpdriver.InfoClientBankAccount = main.clientbankaccount;
                    }
                    //购方地址电话
                    if (main.clientaddressphone!=null){
                        this.kpdriver.InfoClientAddressPhone = main.clientaddressphone;
                    }
                    //销方银行账号
                    if (main.sellerbankaccount!=null)
                    {
                        this.kpdriver.InfoSellerBankAccount = main.sellerbankaccount;
                    }
                    //销方地址电话
                    if (main.selleraddressphone!=null){
                        this.kpdriver.InfoSellerAddressPhone = main.selleraddressphone;
                    }
                    //发票备注
                    if (main.notes!=null){
                        this.kpdriver.InfoNotes = main.notes;
                    }
                    //开票人
                    if (main.invoicer!=null){
                        this.kpdriver.InfoInvoicer = main.invoicer;
                    }
                    //复核人
                    if (main.checker!=null){
                        this.kpdriver.InfoChecker = main.checker;
                    }
                    //收款人
                    if (main.cashier!=null){
                        this.kpdriver.InfoCashier = main.cashier;
                    }
                    //是否开具销货清单，如不为空，那么开具销货清单
                    if (main.infolistname!=null){
                        this.kpdriver.InfoListName = main.infolistname;
                    }


                    this.kpdriver.ClearInvList();
                    var list = invoiceInfo.sub;
                    if(list==null||list.length==0)
                    {
                        return;
                    }

                    for (var i=0;i<list.length;i++)
                    {
                        this.kpdriver.InvListInit();
                        if (list[i]['listgoodsname']!=null)
                        {
                            this.kpdriver.ListGoodsName = list[i]['listgoodsname'];
                        }
                        alert('ee1');
                        if (list[i]['listtaxitem']!=null){
                            this.kpdriver.ListTaxItem = list[i]['listtaxitem'];
                        }
                        alert('ee2');
                        if (list[i]['liststandard']!=null){
                            this.kpdriver.ListStandard = list[i]['liststandard'];
                        }
                        if (list[i]['listunit']!=null && list[i]['listunit']!='')
                        {
                            this.kpdriver.ListUnit = list[i]['listunit'];
                            this.kpdriver.ListNumber=list[i]['listnumber'];
                            this.kpdriver.ListAmount=list[i]['amount'];
                            if(list[i]['listprice']!=null){
                                this.kpdriver.ListPrice = list[i]['listprice'];
                            }
                        }
                        else
                        {
                            this.kpdriver.ListAmount=list[i]['amount'];

                        }
                        alert('ee3');
                        if(list[i]['tax']!=null){
                            this.kpdriver.ListTaxAmount=list[i]['tax'];
                        }
                        alert('ee4');
                        this.kpdriver.ListPriceKind = 0;
                        if(list[i]['taxrate']!=null){
                            var rate = parseFloat(list[i]['taxrate']);
                            if(rate<=1)
                            {
                                this.kpdriver.InfoTaxRate=(rate*100).toFixed(0);
                            }
                            else
                            {
                                this.kpdriver.InfoTaxRate=rate.toFixed(0);
                            }
                        }
                        alert('ee5');
                        this.kpdriver.AddInvList();
                        alert('ee6');
                    }

                    // V2.0
                    this.kpdriver.CheckEWM = 0;
                    alert('error');
                    this.kpdriver.Invoice();

                    alert('发票代码：'+this.kpdriver.InfoTypeCode+";发票号码："+this.kpdriver.InfoNumber+";清单标志："+this.kpdriver.GoodsListFlag);
                    var result = {};
                    /*这里必须将ActiveX对象里的值转成字符串才能正确的在参数中传递*/
                    result['infoAmount'] = this.kpdriver.InfoAmount+'';
                    result['infoTaxAmount'] = this.kpdriver.InfoTaxAmount+'';
                    result['infoDate'] = new Date(this.kpdriver.InfoDate+'').Format('yyyy-MM-dd hh:mm:ss');
                    result['infMonth'] = this.kpdriver.InfMonth+'';
                    result['infoTypeCode'] = this.kpdriver.InfoTypeCode+'';
                    result['infoNumber'] = this.kpdriver.InfoNumber+'';
                    result['goodsListFlag'] = this.kpdriver.GoodsListFlag+'';
                    result['retCode'] = this.kpdriver.RetCode+'';
                    result['machineNo'] = this.kpdriver.MachineNo+'';
                    result['taxrate'] = this.kpdriver.InfoTaxRate+'';
                    return result;
                }

            }catch(e){
                alert('开票失败,错误信息invoice:'+e.description);
            }
        },

        printInv:function(infoKind,infoTypeCode,infoNumber,goodsListFlag,infoShowPrtDlg){
            try{
                this.kpdriver.InfoKind = infoKind;
                this.kpdriver.InfoTypeCode = infoTypeCode;
                this.kpdriver.InfoNumber = infoNumber;
                this.kpdriver.GoodsListFlag = goodsListFlag;
                this.kpdriver.InfoShowPrtDlg = infoShowPrtDlg;

                this.kpdriver.PrintInv();
                return this.kpdriver.RetCode;
            }catch(e){
                alert('打印失败');
            }
        },

        cancelInv:function(infoKind,infoTypeCode,infoNumber){
            try{
                this.kpdriver.InfoKind = infoKind;
                this.kpdriver.InfoTypeCode = infoTypeCode;
                this.kpdriver.InfoNumber = infoNumber;
                this.kpdriver.CancelInv();
                return this.kpdriver.RetCode;
            }catch(e){
                alert('作废失败');
            }
        },

        enHand:function(handMade){
            this.kpdriver.HandMade = handMade;
            this.kpdriver.EnHand();
        },

        qryInv:function(infoBillNumber){
            try{
                this.kpdriver.InfoBillNumber=infoBillNumber;
                this.kpdriver.QryInv();
                var invoice = {};
                invoice['retCode'] = this.kpdriver.RetCode;
                if (invoice['retCode']==7001){
                    return invoice;
                }
                invoice['infoKind'] = this.kpdriver.InfoKind+'' ;
                invoice['infoTypeCode'] = this.kpdriver.InfoTypeCode+'';
                invoice['infoNumber'] = this.kpdriver.InfoNumber+'';
                invoice['infoBillNumber'] = this.kpdriver.InfoBillNumber+'';
                invoice['infoAmount'] = this.kpdriver.InfoAmount+'';
                invoice['infoTaxAmount'] = this.kpdriver.InfoTaxAmount+'';
                invoice['infoDate'] = new Date(this.kpdriver.InfoDate+'').Format('yyyy-MM-dd hh:mm:ss');
                invoice['infoPrintFlag'] = this.kpdriver.InfoPrintFlag+'';
                return invoice;
            }catch(e){
                alert('查询失败');
            }
        },

        qryInv:function(infoKind,infoTypeCode,infoNumber){
            try{
                /*这里一定要清空InfoBillNumber，否则调用QryInv()后，结果不正确*/
                this.kpdriver.InfoBillNumber='';
                this.kpdriver.InfoKind=infoKind;
                this.kpdriver.InfoTypeCode=infoTypeCode;
                this.kpdriver.InfoNumber=infoNumber;
                this.kpdriver.QryInv();
                var invoice = {};
                invoice['retCode'] = this.kpdriver.RetCode;
                if (invoice['retCode']==7001){
                    return invoice;
                }
                invoice['infoKind'] = this.kpdriver.InfoKind+'' ;
                invoice['infoTypeCode'] = this.kpdriver.InfoTypeCode+'';
                invoice['infoNumber'] = this.kpdriver.InfoNumber+'';
                invoice['infoBillNumber'] = this.kpdriver.InfoBillNumber+'';
                invoice['infoAmount'] = this.kpdriver.InfoAmount+'';
                invoice['infoTaxAmount'] = this.kpdriver.InfoTaxAmount+'';
                invoice['infoDate'] = new Date(this.kpdriver.InfoDate+'').Format('yyyy-MM-dd hh:mm:ss');
                invoice['infoPrintFlag'] = this.kpdriver.InfoPrintFlag+'';
                return invoice;
            }catch(e){
                alert('查询失败');
            }
        },

        isRepReached:function(){
            if (this.kpdriver.IsRepReached==1){
                return true;
            }
            return false;
        },

        isLockReached:function(){
            if (this.kpdriver.IsLockReached==1){
                return true;
            }
            return false;
        },

        getMachineNo:function(){
            return this.kpdriver.MachineNo;
        },

        //开具发票接口,是public的接口
        openInvoice:function(data){
            //检查金税设备
            this.checkSetup();
            //开启金税盘
            this.openCard();

            //获取开发票的票号等信息
            var invoiceInfo = this.getInfo(2);
            invoiceInfo.main = {};
            invoiceInfo.main.invtype = 2;  //增值税普通发票
            invoiceInfo.main.clientname = data.header;  //购货方名称,发票抬头
            invoiceInfo.sub = new Array();
            var list = new Array();
            var sub = {};
            sub['listgoodsname'] = data.body;  //发票内容
            sub['listnumber'] = 1;
            sub['listprice'] = data.ordermoney;
            sub['listunit'] = '份';
            sub['amount'] = data.ordermoney;
            sub['tax'] = 17;
            list.push(sub);
            invoiceInfo.sub = list;
            window.console.dir(invoiceInfo);
            //开启发票
            var invoiceResult = this.invoice(invoiceInfo);

            //打印发票
            //this.printInv('2','1100111650','08257515',0,1);
        }


    }

    // expose to the global object
    window.InvoiceOper = InvoiceOper;

}) (window, document);

