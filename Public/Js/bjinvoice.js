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
                //this.kpdriver.CertPassWord='124.205.14.131:8001';

                //加入代码，20170506，发票自动上传设置为不上传，改为人工上传
                this.kpdriver.UploadInvoiceAuto = 0;

                this.kpdriver.OpenCard();
                if(this.kpdriver.RetCode=="1011"){
                    this.isOpen = true;
                    return true;
                }else if (this.kpdriver.RetCode=="1007"){
                    alert("金税卡已经被占用，请退出所有可能使用金税卡的程序后重试");
                    return false;
                }else if (this.kpdriver.RetCode=="1001"){
                    alert("开票信息初始化失败，请重启浏览器或重装开票系统后重试");
                    return false;
                }else if (this.kpdriver.RetCode=="1005"){
                    this.closeCard();
                    alert('应该是浏览器没有用管理员权限打开!错误号:1005');
                    return false;
                }else {
                    alert("金税卡开启失败，错误代码："+this.kpdriver.RetCode);
                    this.closeCard();
                    return false;
                }
                return false;
            }catch(e) {
                alert("开票组件出错，错误信息为open："+e.description);
                this.closeCard();
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
                    alert('检查一下浏览器是否选择了兼容模式,不要用闪电模式!');
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
            var rect =  this.kpdriver.GetInfo();
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

                        if (list[i]['listtaxitem']!=null){
                            this.kpdriver.ListTaxItem = list[i]['listtaxitem'];
                        }

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

                        if(list[i]['tax']!=null){
                            this.kpdriver.ListTaxAmount=list[i]['tax'];
                        }

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

                        //含税标志
                        this.kpdriver.ListPriceKind = 1;

                        //分类税代码
                        var retStr = this.kpdriver.BatchUpload(this.setCategoryTax());

                        if(retStr.indexOf('0000') == -1){
                            alert('税收分类编码校验失败！');
                            return false;
                        }

                        this.kpdriver.AddInvList();

                    }

                    // V2.0
                    this.kpdriver.CheckEWM = 0;
                    this.kpdriver.Invoice();

                    //alert('发票代码：'+this.kpdriver.InfoTypeCode+";发票号码："+this.kpdriver.InfoNumber+";清单标志："+this.kpdriver.GoodsListFlag);
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
                return false;
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
            var openState = this.openCard();
            if(!openState) return false;

            //获取开发票的票号等信息
            var invoiceInfo = this.getInfo(2);
            invoiceInfo.main = {};
            invoiceInfo.main.invtype = 2;  //增值税普通发票
            invoiceInfo.main.clientname = data.header;  //购货方名称,发票抬头
            invoiceInfo.main.clienttaxcode = data.gmf_nsrsbh;  //购买方税号
            invoiceInfo.main.clientbankaccount = data.gmf_yhzh;  //购买方银行账户
            invoiceInfo.main.clientaddressphone = data.gmf_dzdh;  //购买方地址电话
            invoiceInfo.main.invoicer = data.name;
            invoiceInfo.main.cashier = data.cashier;
            invoiceInfo.main.checker = data.checker;
            invoiceInfo.main.selleraddressphone = '北京市朝阳区大郊亭中街2号院4号楼4-7Ａ87779899';
            invoiceInfo.main.sellerbankaccount = '交行北京分行松榆里支行 110060900018001052402';

            invoiceInfo.sub = new Array();
            var list = new Array();
            var sub = {};
            sub['listgoodsname'] = data.body;  //发票内容
            sub['listnumber'] = 1;
            sub['listprice'] = data.ordermoney;
            sub['listunit'] = ' ';
            //sub['amount'] = data.ordermoney;
            //sub['tax'] = 3;
            sub['taxrate'] = 6;
            list.push(sub);
            invoiceInfo.sub = list;
            //开启发票
            var invoiceResult = this.invoice(invoiceInfo);
            if(this.kpdriver.RetCode == '4011'){
                alert('发票代码：'+this.kpdriver.InfoTypeCode+";\r发票号码："+
                this.kpdriver.InfoNumber + ";\r发票抬头: "+data.header + "\r发票导入成功");
            }else{
                this.returnError(this.kpdriver.RetCode);
                this.closeCard();
                return false;
            }

            //打印发票
            var retCode = this.printInv('2',invoiceResult.infoTypeCode,invoiceResult.infoNumber,0,1);
            this.returnError(retCode);
            //this.closeCard();
            // return false;
            return true;
            //打印发票
            //this.printInv('2','1100153320','41257002',0,1);
            alert('print');
            //this.closeCard();
            return true;
        },

        //设置分类税编码
        setCategoryTax : function(){
            //定义发票分类编码头
            var taxHeader = '';
            //定义数据
            var taxData   = '<?xml version="1.0" encoding="GBK"?><FPXT><INPUT><GoodsNo><GoodsNoVer>1.0</GoodsNoVer><GoodsTaxNo>3070401</GoodsTaxNo><TaxPre>0</TaxPre><TaxPreCon></TaxPreCon><ZeroTax></ZeroTax><CropGoodsNo></CropGoodsNo><TaxDeduction></TaxDeduction></GoodsNo></INPUT></FPXT>';
            //base64加密
            taxData =  Base64.encode(taxData);
            //组成数据
            taxHeader = '<?xml version="1.0" encoding="GBK"?>';
            taxHeader = taxHeader +  '<FPXT_COM_INPUT>';
            taxHeader = taxHeader +  '<ID>1100</ID>';
            taxHeader = taxHeader +  '<DATA>'+ taxData +'</DATA>';
            taxHeader = taxHeader +  '</FPXT_COM_INPUT>';
            return taxHeader;
        },


        //返回错误信息
        returnError : function(retCode){
            switch (retCode){
                case 1:
                    alert(" 尚未开启金税盘");
                    break;
                case 1001:
                    alert(" 发票信息初始化失败");
                    break;
                case 1003:
                    alert(" 金税盘初始化失败");
                    break;
                case 1007:
                    alert(" 金税盘不能独占打开");
                    break;
                case 1010:
                    alert(" 注册失败");
                    break;
                case 1011:
                    alert(" 金税盘开启成功");
                    break;
                case 3011:
                    alert(" 金税盘状态信息读取成功");
                    break;
                case 4001:
                    alert('4001:发票数据内容不全,不能开票!')
                    break;
                case 4003:
                    alert(" 发票数据写盘失败");
                    break;
                case 4005:
                    alert(" 发票数据写库失败");
                    break;
                case 4011:
                    alert(" 开票成功");
                    break;
                case 4012:
                    alert(" 开票失败");
                    break;
                case 4016:
                    alert(" 发票数据校验通过");
                    break;
                case 5001:
                    alert(" 未找到发票或清单");
                    break;
                case 5011:
                    alert(" 打印成功");
                    break;
                case 5012:
                    alert(" 未打印");
                    break;
                case 5013:
                    alert(" 打印失败");
                    break;
                case 6011:
                    alert(" 发票作废成功");
                    break;
                case 6013:
                    alert(" 发票作废失败");
                    break;
                case 7001:
                    alert( " 未找到发票");
                    break;
                case 7011:
                    alert(" 发票查询成功");
                    break;
                case 8000:
                    alert(" 发票上传成功");
                    break;
                case 8001:
                    alert( " 发票上传失败");
                    break;
                case 8050:
                    alert(" 发票报送状态更新成功");
                    break;
                case 8051:
                    alert(" 发票报送状态更新失败");
                    break;
                case 8052:
                    alert( " 没有待更新包送状态的发票,请先执行发票上传");
                    break;
                case 9000:
                    alert(" 调用成功");
                    break;
                case 9001:
                    alert(" 调用失败");
                    break;
                default:
                    alert(retCode);
            }
        }

    }

    // expose to the global object
    window.InvoiceOper = InvoiceOper;

}) (window, document);


/**
 * UTF16和UTF8转换对照表
 * U+00000000 – U+0000007F   0xxxxxxx
 * U+00000080 – U+000007FF   110xxxxx 10xxxxxx
 * U+00000800 – U+0000FFFF   1110xxxx 10xxxxxx 10xxxxxx
 * U+00010000 – U+001FFFFF   11110xxx 10xxxxxx 10xxxxxx 10xxxxxx
 * U+00200000 – U+03FFFFFF   111110xx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
 * U+04000000 – U+7FFFFFFF   1111110x 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
 */
var Base64 = {
    // 转码表
    table : [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
        'I', 'J', 'K', 'L', 'M', 'N', 'O' ,'P',
        'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
        'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f',
        'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n',
        'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
        'w', 'x', 'y', 'z', '0', '1', '2', '3',
        '4', '5', '6', '7', '8', '9', '+', '/'
    ],
    UTF16ToUTF8 : function(str) {
        var res = [], len = str.length;
        for (var i = 0; i < len; i++) {
            var code = str.charCodeAt(i);
            if (code > 0x0000 && code <= 0x007F) {
                // 单字节，这里并不考虑0x0000，因为它是空字节
                // U+00000000 – U+0000007F  0xxxxxxx
                res.push(str.charAt(i));
            } else if (code >= 0x0080 && code <= 0x07FF) {
                // 双字节
                // U+00000080 – U+000007FF  110xxxxx 10xxxxxx
                // 110xxxxx
                var byte1 = 0xC0 | ((code >> 6) & 0x1F);
                // 10xxxxxx
                var byte2 = 0x80 | (code & 0x3F);
                res.push(
                    String.fromCharCode(byte1),
                    String.fromCharCode(byte2)
                );
            } else if (code >= 0x0800 && code <= 0xFFFF) {
                // 三字节
                // U+00000800 – U+0000FFFF  1110xxxx 10xxxxxx 10xxxxxx
                // 1110xxxx
                var byte1 = 0xE0 | ((code >> 12) & 0x0F);
                // 10xxxxxx
                var byte2 = 0x80 | ((code >> 6) & 0x3F);
                // 10xxxxxx
                var byte3 = 0x80 | (code & 0x3F);
                res.push(
                    String.fromCharCode(byte1),
                    String.fromCharCode(byte2),
                    String.fromCharCode(byte3)
                );
            } else if (code >= 0x00010000 && code <= 0x001FFFFF) {
                // 四字节
                // U+00010000 – U+001FFFFF  11110xxx 10xxxxxx 10xxxxxx 10xxxxxx
            } else if (code >= 0x00200000 && code <= 0x03FFFFFF) {
                // 五字节
                // U+00200000 – U+03FFFFFF  111110xx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
            } else /** if (code >= 0x04000000 && code <= 0x7FFFFFFF)*/ {
                // 六字节
                // U+04000000 – U+7FFFFFFF  1111110x 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
            }
        }

        return res.join('');
    },
    UTF8ToUTF16 : function(str) {
        var res = [], len = str.length;
        var i = 0;
        for (var i = 0; i < len; i++) {
            var code = str.charCodeAt(i);
            // 对第一个字节进行判断
            if (((code >> 7) & 0xFF) == 0x0) {
                // 单字节
                // 0xxxxxxx
                res.push(str.charAt(i));
            } else if (((code >> 5) & 0xFF) == 0x6) {
                // 双字节
                // 110xxxxx 10xxxxxx
                var code2 = str.charCodeAt(++i);
                var byte1 = (code & 0x1F) << 6;
                var byte2 = code2 & 0x3F;
                var utf16 = byte1 | byte2;
                res.push(Sting.fromCharCode(utf16));
            } else if (((code >> 4) & 0xFF) == 0xE) {
                // 三字节
                // 1110xxxx 10xxxxxx 10xxxxxx
                var code2 = str.charCodeAt(++i);
                var code3 = str.charCodeAt(++i);
                var byte1 = (code << 4) | ((code2 >> 2) & 0x0F);
                var byte2 = ((code2 & 0x03) << 6) | (code3 & 0x3F);
                utf16 = ((byte1 & 0x00FF) << 8) | byte2
                res.push(String.fromCharCode(utf16));
            } else if (((code >> 3) & 0xFF) == 0x1E) {
                // 四字节
                // 11110xxx 10xxxxxx 10xxxxxx 10xxxxxx
            } else if (((code >> 2) & 0xFF) == 0x3E) {
                // 五字节
                // 111110xx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
            } else /** if (((code >> 1) & 0xFF) == 0x7E)*/ {
                // 六字节
                // 1111110x 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
            }
        }

        return res.join('');
    },
    encode : function(str) {
        if (!str) {
            return '';
        }
        var utf8    = this.UTF16ToUTF8(str); // 转成UTF8
        var i = 0; // 遍历索引
        var len = utf8.length;
        var res = [];
        while (i < len) {
            var c1 = utf8.charCodeAt(i++) & 0xFF;
            res.push(this.table[c1 >> 2]);
            // 需要补2个=
            if (i == len) {
                res.push(this.table[(c1 & 0x3) << 4]);
                res.push('==');
                break;
            }
            var c2 = utf8.charCodeAt(i++);
            // 需要补1个=
            if (i == len) {
                res.push(this.table[((c1 & 0x3) << 4) | ((c2 >> 4) & 0x0F)]);
                res.push(this.table[(c2 & 0x0F) << 2]);
                res.push('=');
                break;
            }
            var c3 = utf8.charCodeAt(i++);
            res.push(this.table[((c1 & 0x3) << 4) | ((c2 >> 4) & 0x0F)]);
            res.push(this.table[((c2 & 0x0F) << 2) | ((c3 & 0xC0) >> 6)]);
            res.push(this.table[c3 & 0x3F]);
        }

        return res.join('');
    },
    decode : function(str) {
        if (!str) {
            return '';
        }

        var len = str.length;
        var i   = 0;
        var res = [];

        while (i < len) {
            code1 = this.table.indexOf(str.charAt(i++));
            code2 = this.table.indexOf(str.charAt(i++));
            code3 = this.table.indexOf(str.charAt(i++));
            code4 = this.table.indexOf(str.charAt(i++));

            c1 = (code1 << 2) | (code2 >> 4);
            c2 = ((code2 & 0xF) << 4) | (code3 >> 2);
            c3 = ((code3 & 0x3) << 6) | code4;

            res.push(String.fromCharCode(c1));

            if (code3 != 64) {
                res.push(String.fromCharCode(c2));
            }
            if (code4 != 64) {
                res.push(String.fromCharCode(c3));
            }

        }

        return this.UTF8ToUTF16(res.join(''));
    }
};

