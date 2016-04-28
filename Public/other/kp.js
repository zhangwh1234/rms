var KP={
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
				if(this.kpdriver.RetCode=="1011"){
				  this.isOpen = true;
				  return true;
			    }else if (this.kpdriver.RetCode=="1007"){
			      alert("金税卡已经被占用，请退出所有可能使用金税卡的程序后重试");
			    }else if (this.kpdriver.RetCode=="1001"){
			      alert("开票信息初始化失败，请重启浏览器或重装开票系统后重试");
			    }else if (this.kpdriver.RetCode=="1005"){
					closeCard();
					checkSetup();
			    }else {
			      alert("金税卡开启失败，错误代码："+this.kpdriver.RetCode);
			    }
			    return false;
			}catch(e) {
				alert("开票组件出错，错误信息为："+e.description);
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
				  alert("开票组件出错，错误信息为："+e.description);
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
				this.kpdriver.InfoKind=main.invtype;
				if(main.invtype=='0' || main.invtype=='2'){
					//购方名称
					this.kpdriver.InfoClientName = main.clientname;
					/*if(cCustType==1&&main.buyclassname!=null&&main.buyclassname!='')
					{
						this.kpdriver.InfoClientName=main.buyclassname;
					}
					else
					{
						if (main.clientname!=null)
						{
							this.kpdriver.InfoClientName = main.clientname;
						}
					}*/
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
	/*				
	alert('main.sellername:'+main.sellername);	
					if(main.sellername!=null)
					{
						this.kpdriver.InfoSellerName=main.sellername;
					}
	alert('main.sellertaxcode:'+main.sellertaxcode);					
					if(main.sellertaxcode!=null)
					{
						this.kpdriver.InfoSellerTaxCode==main.sellertaxcode;
					}
	*/	
					//销方银行账号
					if (main.sellerbankaccount!=null)
					{
						this.kpdriver.InfoSellerBankAccount = main.sellerbankaccount;
					}
					//销方地址电话
					if (main.selleraddressphone!=null){
						this.kpdriver.InfoSellerAddressPhone = main.selleraddressphone;
					}
					/*税率
					if (invoiceInfo['infotaxrate']!=null){
						this.kpdriver.InfoTaxRate = invoiceInfo['infotaxrate'];
					}
					*/
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
					
					/*不传递销售单据号
					if (main.infobillnumber!=null){
						this.kpdriver.InfoBillNumber = main.infobillnumber;
					}
					*/
	/*
	alert('this.kpdriver.InfoClientName='+this.kpdriver.InfoClientName+'\nthis.kpdriver.InfoClientTaxCode='+this.kpdriver.InfoClientTaxCode+'\nthis.kpdriver.InfoClientBankAccount='+this.kpdriver.InfoClientBankAccount
			+'\nthis.kpdriver.InfoClientAddressPhone='+this.kpdriver.InfoClientAddressPhone+'\nthis.kpdriver.InfoSellerBankAccount='+this.kpdriver.InfoSellerBankAccount+'\nthis.kpdriver.InfoSellerAddressPhone='+this.kpdriver.InfoSellerAddressPhone
			+'\nthis.kpdriver.InfoTaxRate='+this.kpdriver.InfoTaxRate+'\nthis.kpdriver.InfoNotes='+this.kpdriver.InfoNotes+'\nthis.kpdriver.InfoInvoicer='+this.kpdriver.InfoInvoicer
			+'\nthis.kpdriver.InfoChecker='+this.kpdriver.InfoChecker+'\nthis.kpdriver.InfoCashier='+this.kpdriver.InfoCashier+'\nthis.kpdriver.InfoListName='+this.kpdriver.InfoListName);
	*/
					
					this.kpdriver.ClearInvList();
					var list = invoiceInfo.sub;
					if(list==null||list.length==0)
					{
						return;
					}
			/**
			 * 
			
					for(var j=0;j<list.length;j++)
					{
						if(j==0)
						{
							var row=list[j];
							var rate=parseFloat(row['taxrate']);
								
							if(rate<=1)
							{
								this.kpdriver.InfoTaxRate=(rate*100).toFixed(0);
							}
							else
							{
								this.kpdriver.InfoTaxRate=rate.toFixed(0);
							}
								break;
						}
					}
			 */
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
					 
						
						/*
						if (list[i]['listnumber']!=null){
							this.kpdriver.ListNumber = list[i]['listnumber'];
						}
						if (list[i]['listprice']!=null){
							this.kpdriver.ListPrice = list[i]['listprice'];
						}
						if (list[i]['listamount']!=null){
							this.kpdriver.ListAmount = list[i]['listamount'];
						}
						if (list[i]['listpricekind']!=null){
							this.kpdriver.ListPriceKind = list[i]['listpricekind'];
						}
						if (list[i]['listtaxamount']!=null){
							this.kpdriver.ListTaxAmount = list[i]['listtaxamount'];
						}
				*/
	/*alert('this.kpdriver.InfoClientName'+this.kpdriver.InfoClientName+'\nthis.kpdriver.InfoClientTaxCode'+this.kpdriver.InfoClientTaxCode+
			'\nthis.kpdriver.InfoClientBankAccount'+this.kpdriver.InfoClientBankAccount+'\nthis.kpdriver.InfoClientAddressPhone'+this.kpdriver.InfoClientAddressPhone+
			'\nthis.kpdriver.InfoSellerBankAccount'+this.kpdriver.InfoSellerBankAccount+'\nthis.kpdriver.InfoSellerAddressPhone'+this.kpdriver.InfoSellerAddressPhone+
			'\nthis.kpdriver.InfoNotes'+this.kpdriver.InfoNotes+'\nthis.kpdriver.InfoInvoicer'+this.kpdriver.InfoInvoicer+'\nthis.kpdriver.InfoChecker'+this.kpdriver.InfoChecker+
			'\nthis.kpdriver.InfoCashier'+this.kpdriver.InfoCashier+'\nthis.kpdriver.InfoListName'+this.kpdriver.InfoListName);	
		 
		 
	alert('this.kpdriver.InfoTaxRate='+this.kpdriver.InfoTaxRate+'\nthis.kpdriver.ListGoodsName='+this.kpdriver.ListGoodsName+'\nthis.kpdriver.ListTaxItem='+this.kpdriver.ListTaxItem+'\nthis.kpdriver.ListStandard='+this.kpdriver.ListStandard
			+'\nthis.kpdriver.ListUnit='+this.kpdriver.ListUnit+'\nthis.kpdriver.ListNumber='+this.kpdriver.ListNumber+'\nthis.kpdriver.ListPrice='+this.kpdriver.ListPrice
			+'\nthis.kpdriver.ListAmount='+this.kpdriver.ListAmount+'\nthis.kpdriver.ListPriceKind='+this.kpdriver.ListPriceKind+'\nthis.kpdriver.ListTaxAmount='+this.kpdriver.ListTaxAmount);*/
	
						this.kpdriver.AddInvList();
//						this.kpdriver.ListStandard ='';
//						this.kpdriver.ListUnit ='';
//						this.kpdriver.ListPrice=0;
//						this.kpdriver.ListNumber=0;
//						this.kpdriver.ListAmount=0;
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
				}else if(main.invtype=='11'){
					//正负标志
					if(main.bpmmark=='1'){
						this.InfoClientAddressPhone = '1';
					}
					
					//实际受票方
					if(cCustType==1&&main.buyclassname!=null&&main.buyclassname!=''){
						this.kpdriver.InfoClientName=main.buyclassname;
					}else{
						if (main.clientname!=null){
							this.kpdriver.InfoClientName = main.clientname;
						}
					}
					
					//实际受票方纳税人识别号
					if (main.clienttaxcode !=null)
					{
						this.kpdriver.InfoClientTaxCode = main.clienttaxcode;
					}
					
					//收货人
					if(main.consignername!=null){
						this.kpdriver.ConsignerName = main.consignername;
					}
					
					//收货人纳税人识别号
					if(main.consignertaxcode!=null){
						this.kpdriver.ConsignerTaxCode = main.consignertaxcode;
					}
					
					//发货人
					if (main.shippername!=null){
						this.kpdriver.ShipperName = main.shippername;
					}
					
					//发货人纳税人识别号
					if (main.shippertaxcode!=null){
						this.kpdriver.ShipperTaxCode = main.shippertaxcode;
					}
					
					//起运地、经由、到达地
					if(main.originviaarrivalplace!=null){
						this.kpdriver.OriginViaArrivalPlace = main.originviaarrivalplace;
					}
					
					//运输货物信息
					if(main.infolistname!=null){
						this.kpdriver.InfoListName = main.infolistname;
					}
					
					//税率
					var list = invoiceInfo.sub;
					if(list[0]['taxrate']!=null){
						var rate = parseFloat(list[0]['taxrate']);
						if(rate<=1)
						{
							this.kpdriver.InfoTaxRate=(rate*100).toFixed(0);
						}
						else
						{
							this.kpdriver.InfoTaxRate=rate.toFixed(0);
						}
					}
					
					//车种车号
					if(main.vehiclekindno!=null){
						 this.kpdriver.VehicleKindNo = main.vehiclekindno;
					}
					
					//车船吨位
					if(main.vehicletonnage!=null){
						this.kpdriver.VehicleTonnage = main.vehicletonnage;
					}
					
					//发票备注
					if (main.notes!=null && main.notes!='' ){
						this.kpdriver.InfoNotes = main.notes;
					}
					
					//收款人
					if (main.cashier!=null){
						this.kpdriver.InfoCashier = main.cashier;
					}
					
					//复核人
					if (main.checker!=null){
						this.kpdriver.InfoChecker = main.checker;
					}
					
					//开票人
					if (invoiceInfo.invoicer!=null){
						this.kpdriver.InfoInvoicer = invoiceInfo.invoicer;
					}
					
					
					//------费用项目---------
					this.kpdriver.ClearInvList();
					
					
					if(list==null||list.length==0){
						return;
					}
					for ( var i = 0; i < list.length; i++) {
						this.kpdriver.InvListInit();
						
						//费用项目
						if (list[i]['listgoodsname']!=null){
							this.kpdriver.ListGoodsName = list[i]['listgoodsname'];
						}
						
						//金额
						this.kpdriver.ListAmount=list[i]['amount'];
						
						//是否含税
						//this.kpdriver.ListPriceKind = 0;
						//税额
						if(list[i]['tax']!=null){
							this.kpdriver.ListTaxAmount=list[i]['tax'];
						}
						this.kpdriver.AddInvList();
						/**
						 * 
						 
						alert('this.kpdriver.InfoKind='+this.kpdriver.InfoKind
								+'\nthis.InfoClientAddressPhone='+this.InfoClientAddressPhone
								+'\nthis.kpdriver.InfoClientName='+this.kpdriver.InfoClientName
								+'\nthis.kpdriver.InfoClientTaxCode='+this.kpdriver.InfoClientTaxCode
								+'\nthis.kpdriver.ConsignerName='+this.kpdriver.ConsignerName
								+'\nthis.kpdriver.ConsignerTaxCode='+this.kpdriver.ConsignerTaxCode
								+'\nthis.kpdriver.ShipperName='+this.kpdriver.ShipperName
								+'\nthis.kpdriver.ShipperTaxCode='+this.kpdriver.ShipperTaxCode
								+'\nthis.kpdriver.OriginViaArrivalPlace='+this.kpdriver.OriginViaArrivalPlace
								+'\nthis.kpdriver.InfoListName='+this.kpdriver.InfoListName
								+'\nthis.kpdriver.InfoTaxRate='+this.kpdriver.InfoTaxRate
								+'\nthis.kpdriver.VehicleKindNo='+this.kpdriver.VehicleKindNo
								+'\nthis.kpdriver.VehicleTonnage='+this.kpdriver.VehicleTonnage
								+'\nthis.kpdriver.InfoNotes='+this.kpdriver.InfoNotes
								+'\nthis.kpdriver.InfoCashier='+this.kpdriver.InfoCashier
								+'\nthis.kpdriver.InfoChecker='+this.kpdriver.InfoChecker
								+'\nthis.kpdriver.ListGoodsName='+this.kpdriver.ListGoodsName
								+'\nthis.kpdriver.ListAmount='+this.kpdriver.ListAmount
								+'\nthis.kpdriver.ListTaxAmount='+this.kpdriver.ListTaxAmount
								+'\nthis.kpdriver.InfoInvoicer='+this.kpdriver.InfoInvoicer);
						*/
					}
					this.kpdriver.Invoice();
					
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
				}else if(main.invtype=='12'){
					//新/旧发票
					
					
					this.kpdriver.InfoClientAddressPhone = main.infoclientaddressphone;
					var list = invoiceInfo.sub;
					if(list==null){
						return;
					}
					
					//纳税人识别号
					if (main.clienttaxcode !=null)
					{
						this.kpdriver.InfoClientTaxCode = main.clienttaxcode;
					}
					//购货单位
					if(cCustType==1&&main.buyclassname!=null&&main.buyclassname!=''){
						this.kpdriver.InfoClientName=main.buyclassname;
					}else{
						if (main.clientname!=null){
							this.kpdriver.InfoClientName = main.clientname;
						}
					}
					//身份证号码/组织机构代码
					if(main.idcard != null){
						this.kpdriver.IDCard = main.idcard;
					}
					
					//车辆类型
					if(list[0]['vehiclekind']!=null){
						this.kpdriver.VehicleKind = list[0]['vehiclekind'];
					}
					
					//厂牌型号
					if(list[0]['brandmodel']!=null){
						this.kpdriver.BrandModel = list[0]['brandmodel'];
					}
					
					//产地
					if(list[0]['originplace']!=null){
						this.kpdriver.OriginPlace = list[0]['originplace'];
					}
					
					//合格证号
					if(main.qualitycertificate != null){
						this.kpdriver.QualityCertificate = main.qualitycertificate;
					}
					
					//进口证书号
					if(main.impcertificateno != null){
						this.kpdriver.ImpCertificateNo = main.impcertificateno;
					}
					
					//商检单号
					if(main.comminspectionno != null){
						this.kpdriver.CommInspectionNo = main.comminspectionno;
					}
					
					//发动机号
					if(main.engineno != null){
						this.kpdriver.EngineNo = main.engineno;
					}
					
					//车辆识别代码、车辆号码
					if(main.vehicleno != null){
						this.kpdriver.VehicleNo = main.vehicleno;
					}
					
					//生成厂家名称
					if(list[0]['manufacturername']!=null){
						this.kpdriver.ManufacturerName = list[0]['manufacturername'];
					}
					
					//价税合计
					if(list[0]['afterdistaxamount']!=null){
						this.kpdriver.AmountTaxTotal = list[0]['afterdistaxamount'];
					}
					
					//销方单位电话
					if(main.sellerphone!=null){
						this.kpdriver.SellerPhone = main.sellerphone;
					}
					
					//销方单位账号
					if(main.selleraccount!=null){
						this.kpdriver.SellerAccount = main.selleraccount;
					}
					
					//地址
					if(main.selleraddress!=null){
						this.kpdriver.SellerAddress = main.selleraddress;
					}
					
					//开户银行
					if(main.sellerbank!=null){
						this.kpdriver.SellerBank = main.sellerbank;
					}
					
					//税率
					if(list[0]['taxrate']!=null){
						var rate = parseFloat(list[0]['taxrate']);
						if(rate<=1)
						{
							this.kpdriver.InfoTaxRate=(rate*100).toFixed(0);
						}
						else
						{
							this.kpdriver.InfoTaxRate=rate.toFixed(0);
						}
					}
					
					//吨位
					if(list[0]['tonnage']!=null){
						this.kpdriver.Tonnage = list[0]['tonnage'];
					}
					
					//限乘人数
					if(list[0]['peopleno']!=null){
						this.kpdriver.PeopleNo = list[0]['peopleno'];
					}
					
					//备注
					//if (main.notes!=null && main.notes!=''){
						//this.kpdriver.InfoNotes = main.notes;
					//}
					//开票人
					if (invoiceInfo.invoicer!=null){
						this.kpdriver.InfoInvoicer = invoiceInfo.invoicer;
					}
					/**
					 * 
					 
					alert('this.kpdriver.InfoKind='+this.kpdriver.InfoKind
							+'\nthis.kpdriver.InfoClientAddressPhone='+this.kpdriver.InfoClientAddressPhone
							+'\nthis.kpdriver.InfoClientTaxCode='+this.kpdriver.InfoClientTaxCode
							+'\nthis.kpdriver.InfoClientName='+this.kpdriver.InfoClientName
							+'\nthis.kpdriver.IDCard='+this.kpdriver.IDCard
							+'\nthis.kpdriver.VehicleKind='+this.kpdriver.VehicleKind
							+'\nthis.kpdriver.BrandModel='+this.kpdriver.BrandModel
							+'\nthis.kpdriver.OriginPlace='+this.kpdriver.OriginPlace
							+'\nthis.kpdriver.QualityCertificate='+this.kpdriver.QualityCertificate
							+'\nthis.kpdriver.ImpCertificateNo='+this.kpdriver.ImpCertificateNo
							+'\nthis.CommInspectionNo='+this.kpdriver.CommInspectionNo
							+'\nthis.kpdriver.EngineNo='+this.kpdriver.EngineNo
							+'\nthis.kpdriver.VehicleNo='+this.kpdriver.VehicleNo
							+'\nthis.kpdriver.ManufacturerName='+this.kpdriver.ManufacturerName
							+'\nthis.kpdriver.AmountTaxTotal='+this.kpdriver.AmountTaxTotal
							+'\nthis.kpdriver.SellerPhone='+this.kpdriver.SellerPhone
							+'\nthis.kpdriver.SellerAccount='+this.kpdriver.SellerAccount
							+'\nthis.kpdriver.SellerAddress='+this.kpdriver.SellerAddress
							+'\nthis.kpdriver.SellerBank='+this.kpdriver.SellerBank
							+'\nthis.kpdriver.InfoTaxRate='+this.kpdriver.InfoTaxRate
							+'\nthis.kpdriver.Tonnage='+this.kpdriver.Tonnage
							+'\nthis.kpdriver.PeopleNo='+this.kpdriver.PeopleNo
							+'\nthis.kpdriver.InfoNotes='+this.kpdriver.InfoNotes
							+'\nthis.kpdriver.InfoInvoicer='+this.kpdriver.InfoInvoicer);
					*/
					
					this.kpdriver.Invoice();
					var result = {};
					/*这里必须将ActiveX对象里的值转成字符串才能正确的在参数中传递*/
					
					
					
					result['retCode'] = this.kpdriver.RetCode+'';
					
					
					result['infoTypeCode'] = this.kpdriver.InfoTypeCode+'';
					result['infoNumber'] = this.kpdriver.InfoNumber+'';
					result['infoAmount'] = this.kpdriver.InfoAmount+'';
					result['infoTaxAmount'] = this.kpdriver.InfoTaxAmount+'';
					result['infoDate'] = new Date(this.kpdriver.InfoDate+'').Format('yyyy-MM-dd hh:mm:ss');
					result['infMonth'] = this.kpdriver.InfMonth+'';
					
					result['goodsListFlag'] = this.kpdriver.GoodsListFlag+'';
					
					result['machineNo'] = this.kpdriver.MachineNo+'';
					result['taxrate'] = this.kpdriver.InfoTaxRate+'';
				
					return result;
				}

			}catch(e){
				alert('开票失败,错误信息:'+e.description);
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
		}
		
		
		
};

Date.prototype.Format = function (fmt) { 
    var o = {
        "M+": this.getMonth() + 1, 
        "d+": this.getDate(), 
        "h+": this.getHours(), 
        "m+": this.getMinutes(),
        "s+": this.getSeconds(), 
        "q+": Math.floor((this.getMonth() + 3) / 3), 
        "S": this.getMilliseconds() 
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}

function closeCard(){
	top.KP.closeCard();
}
