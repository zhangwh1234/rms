var downLoadTime=0;
var jsMsg="税务系统繁忙，请等1小时后再试";
var INV_TIMEOUT=5000;

var oTable;
var invoiceList=[];
var numNow=1;
//申请成功失败标示，false为失败
var flag=false;
var platForm;

// 验证码uuid
var vuuid;
// var vuuid=window.Base64.encode(uid);
var vUrl = "";
function getUUID() {
	var s = [];
	var hexDigits = "0123456789abcdef";
	for (var i = 0; i < 36; i++) {
	s[i] = hexDigits.substr(Math.floor(Math.random() * 0x10), 1);
	}
	s[14] = "4"; // bits 12-15 of the time_hi_and_version field to 0010
	s[19] = hexDigits.substr((s[19] & 0x3) | 0x8, 1); // bits 6-7 of the
														// clock_seq_hi_and_reserved
														// to 01
	s[8] = s[13] = s[18] = s[23] = "-";

	var uuid = s.join("");
	return uuid;
}

function isEmpty(s){
	alert(s);
	var flag = false;
	if(s==undefined || s==null || s==''){
		flag =true;
	}
	alert(flag);
	return flag;
}
function transferOrderInvoice(){
	var userId=$("#aliUserID").val();
	
	var storeCodeId=$("#storeCode").val();
	var orderImeiNum= $("#zixingId").val();
	var bizDate=$("#bizDate").val();
	var transactionId=$("#transactionID").val();
	
	window.location.href=contextPath + '/inv/apply/getAliInvoiceUrl.do?userId='+userId
	+"&storeCode="+storeCodeId+"&bizDate="+bizDate+"&orderImeiNum="+orderImeiNum+"&transactionId="+transactionId+"&apllyState=1";
}
var isClient = true; //默认是电脑端登录
$(function(){	
	vUrl = contextPath+"/inv/apply/vCode.do";
	
	platForm = $("#platForm").val();
	// 清空数据
	$("#vCodeId").val("");
	// 初始化验证码,初始化时将生成一个UUID，后面将此UUID作为唯一KEY来获取验证码
	vuuid=getUUID();
	var u = vUrl + "?uuidKey=" + vuuid; 
	$("#vCode").attr("src",u);


	var clientId = $(window).width()
	if(clientId>800)  //电脑端登录
	{
		isClient = true;
	}
	else  //手机端
	{
		isClient = false;
	}
	
	if(isClient)
	{
		$("#ie10Id").show();
	}
	else
	{
		$("#ie10Id").hide(); 
	}
	
	/** add by li_minghui 2016-9-18 支付宝对接相关功能 start **/
	$("#addAliPay").click(function(){
		if("alipay"==platForm){
			var userId=$("#aliUserID").val();
			var invoiceCode = $("#invoiceCode1").text();
			var invoiceNum = $("#invoiceNum1").text();
	    	$.ajax({
	    		url: contextPath+'/inv/apply/jugdeInvoiceStatus.do',
	    		dataType:'json',
	    		type : 'post',
	    		data:{
	    			'invoiceCode':invoiceCode,
	    			'invoiceNum':invoiceNum
	    		}
		    	,error:function(){
					jQuery('body').hideLoading();
					if(isClient)
					{
					   $.ocAlert("error",jsMsg.insError);
					}
					else
					{
						alert(jsMsg.insError);
					}
				},
	    		success:function(data){
	    			jQuery('body').hideLoading();
	    			if(data.resultJson.errorCode=='0000'){
	    				console.info("发票invoiceCode="+invoiceCode+",invoiceNum="+invoiceNum+"已同步到发票管家");
	    				if(isClient)
						{
						   $.ocAlert("success",data.resultJson.msg);
						}
						else
						{
							alert(data.resultJson.msg);
						}
	    			}else{
	    				console.info("发票invoiceCode="+invoiceCode+",invoiceNum="+invoiceNum+"未同步到发票管家");
	    				var storeCodeId=$("#storeCode").val();
	    				var orderImeiNum= $("#zixingId").val();
	    				var bizDate=$("#bizDate").val();
	    				var transactionId=$("#transactionID").val();
	    				console.info("同步到发票管家开始跳转invoiceCode="+invoiceCode+",invoiceNum="+invoiceNum);
	    				window.location.href=contextPath + '/inv/apply/getAliInvoiceUrl.do?userId='+userId+'&invoiceCode='+invoiceCode+'&invoiceNum='+invoiceNum
	    				+"&storeCode="+storeCodeId+"&bizDate="+bizDate+"&orderImeiNum="+orderImeiNum+"&transactionId="+transactionId+"&apllyState=1";
	    			}	
	    		}
				
	    	});
		}else{			
			var msg = "请使用支付宝客户端扫描开票码添加发票";
			if(isClient)
			{
				$.ocAlert("info",msg);
			}
			else
			{
				alert(msg);
			}
		}
	});
	


	
	/** add by li_minghui 2016-9-18 支付宝对接相关功能 end **/
	// 申请按钮点击事件
	$("#apply").click(function(){
		
		//发票提取码过滤前后空格及校验非法字符
		$("#orderImeiNum").val($.trim($("#orderImeiNum").val()));  
		var orderImeiNumVal=$("#orderImeiNum").val();
		var pattern = new RegExp("[~'!<>\\]\\[#$%^&*()-+=:]");
		if(pattern.test(orderImeiNumVal)){
			if(isClient)
			{
				$.ocAlert("error",jsMsg.orderImeiNumError);
			}
			else
			{
				alert(jsMsg.orderImeiNumError);
			}
			return;
		}	
		
		if(orderImeiNumVal.length<16)
		{
			if(isClient)
			{
				$.ocAlert("error",jsMsg.imeiLengthError);
			}
			else
			{
				alert(jsMsg.imeiLengthError);
			}
			return;
		}
		
		
		 var orderImeiNum= $("#orderImeiNum").val()
		var vCode=$("#vCodeId").val();			
		if(vCode==''){
			if(isClient)
			{
				$.ocAlert("error",jsMsg.vCodeEmpty);
			}
			else
			{
				alert(jsMsg.vCodeEmpty);
			}
			
			changeCode();
			return;
		}
		var base64 = new Base64();  
		var vCodeFromCookie=getCookie(vuuid);
		if(vCodeFromCookie==null){
			if(isClient)
			{
				$.ocAlert("error",jsMsg.vCodeExpire);
			}
			else
			{
				alert(jsMsg.vCodeExpire);
			}
			
			
			$("#vCodeId").val("");
			changeCode();
			return;
		}
		var str = base64.decode(vCodeFromCookie);
		if(vCode==str){
			
			if($("#orderImeiNum").val()==''){
				if(isClient)
				{
					$.ocAlert("error",jsMsg.serOrderIdEmpty);
				}
				else
				{
					alert(jsMsg.serOrderIdEmpty);
				}
				return ;
			}				
			// 发送请求			
			applyOrderDetails(orderImeiNum);
							
		}else{				
			clearCookie(vuuid);
			if(isClient)
			{
				$.ocAlert("error",jsMsg.vcodeError);
			}
			else
			{
				alert(jsMsg.vcodeError);
			}
			$("#vCodeId").val("");
			changeCode();
		}
	
	});
	
	
			
// 航信的发送email
$("#preEmail").click(function(){	
	$("#hxEmail").toggle();		
});
// 回退事件
$("#back01").click(function(){
	// 返回上一级
	history.back(-1)

});
$("#back02").click(function(){
	location.reload();	
});
$("#back03").click(function(){
	
   location.reload();

});

// 点击取消发送邮箱事件
$("#closeEmail").click(function(){
	$("#send-js").hide();
	$("#emailText").val("");
});

// 申请发票按钮点击事件
$("#applyInv").click(function(){
	//如果合计可开票金额为0元，则不让用户开票
	if($("#total").text()=="0元"||$("#total").text()=="0.00元")
    {
		if(isClient)
		{
			$.ocAlert("error",jsMsg.msgTotole);
			return false;
		}
		else
		{
			alert(jsMsg.msgTotole);
			return false;
		}
    }
	
	var option = {
			onOk : function() {
				addIns(invoiceTitle,mobile);
			},
			onCancel : function() {
				return false;
			}
		}
	
	var invoiceTitle=$("#invTitle").val();
	if(invoiceTitle==null||invoiceTitle==''){
		if(isClient)
		{
		   $.ocAlert("error",jsMsg.invoiceTitleEmpty);
		}
		else
		{
			alert(jsMsg.invoiceTitleEmpty);
		}
		return;
	}
	
	
	//购方名称过滤前后空格及校验非法字符
	$("#invTitle").val($.trim($("#invTitle").val()));  
	var invTitleVal=$("#invTitle").val();
	//var pattern = new RegExp("[~'!<>\\]\\[#$%^&*-+=:]");
	
	/**wang_mengyan modify 2016-07-09 购方名称校验非法字符 start*/
	var pattern=/[^\a-\z\A-\Z0-9\u4E00-\u9FA5\(\)\-\_\（\）\、]/g; 
	var patternEx=/[^\a-\z\A-\Z0-9\u4E00-\u9FA5\(\)\-\_\（\）\、\s\·]/g; 
	/**wang_mengyan modify 2016-07-09 购方名称校验非法字符 end*/
	
	if(pattern.test(invTitleVal)){
		if(isClient)
		{
		   $.ocAlert("error",jsMsg.invTitleError);
		}
		else
		{
			alert(jsMsg.invTitleError);
		}
		return;
	}		
	

	/**wang_mengyan modify 2016-07-27 纳税人识别号、地址、电话、开户行及账号校验非法字符 start*/
	var  invTaxNoVal=$("#invTaxNo").val();
	
	if(pattern.test(invTaxNoVal)){
		if(isClient)
		{
		   $.ocAlert("error",jsMsg.invTaxNoError);
		}
		else
		{
			alert(jsMsg.invTaxNoError);
		}
		return;
	}
	
	
	if(invTaxNoVal!="")
	{
		if(!(invTaxNoVal.length==15||invTaxNoVal.length==18||invTaxNoVal.length==20)){
			if(isClient)
			{
			   $.ocAlert("error",jsMsg.invTaxNoLenErr);
			}
			else
			{
				alert(jsMsg.invTaxNoLenErr);
			}
			return;
		}
	}
	
	
	var invAddrPhoneVal=$("#invAddrPhone").val();
	
	if(patternEx.test(invAddrPhoneVal)){
		if(isClient)
		{
		   $.ocAlert("error",jsMsg.invAddrPhoneError);
		}
		else
		{
			alert(jsMsg.invAddrPhoneError);
		}
		return;
	}

	var invBankVal=$("#invBank").val();
	
	if(patternEx.test(invBankVal)){
		if(isClient)
		{
		   $.ocAlert("error",jsMsg.invBankError);
		}
		else
		{
			alert(jsMsg.invBankError);
		}
		return;
	}
	
	/**wang_mengyan modify 2016-07-27 纳税人识别号、地址、电话、开户行及账号校验非法字符 end*/
	
	
	
	
	
	/**wang_mengyan modify 2016-07-19 修改邮箱验证规则 start*/
	var reg=/^\w+([-_.]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,6})+$/;
	
	/**wang_mengyan modify 2016-07-19 修改邮箱验证规则  end*/
	
	
	var myreg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1})|(17[0-9]{1}))+\d{8})$/; 
	
	var mobile=$("#mobile").val();
	if(mobile!="")
	{
		if(!myreg.test(mobile)) 
		{ 
			if(isClient)
			{
			   $.ocAlert("error",jsMsg.mobileError);
			}
			else
			{
				alert(jsMsg.mobileError);
			}
			return false; 
		} 
	}
	
		//航行邮箱验证
		if($("#hxEmail").val()!="")
		{
	      if(!reg.test($("#hxEmail").val())){
	    	//如果输入框是显示状态
	    		if(!$("#hxEmail").val()==''){    		
	    			if(isClient)
	    			{
	    			   $.ocAlert("error",jsMsg.mailError);
	    			}
	    			else
	    			{
	    				alert(jsMsg.mailError);
	    			}
	    			$(this).val('');
	    			return;
	    		}
	    	}
		}
      
   //航信邮箱判空
//    if($("#hxEmail").val()==''){   		
//    	if(isClient)
//		{
//		   $.ocAlert("error",jsMsg.mailEmpty);
//		}
//		else
//		{
//			alert(jsMsg.mailEmpty);
//		}
//    	return;
//    }
    
    var a =$("#invTitle").val();
//    var div ="<div id='divInvoiceInfo'><span style='font-size:18px;text-align:left;'>购方名称：&nbsp;&nbsp;<label style='color:red;font-size:26px;' >"+a+"</label></span><br/>" +
//	"<span style='font-size:24px;text-align:center;padding-top:9px;'>" +
//			"请确认以上信息是否正确!</span></div>";

	var b = "邮箱: "+$("#hxEmail").val();
	var mail=$("#hxEmail").val();
	/**lee_solar modify 2016-07-08 前台顾客开票流程优化 start*/
	var div="";
/*	if(mail=="")
	{
		 div="<div  id='divInvoiceInfo'><span style='font-size:18px;text-align:left;'>购方名称：&nbsp;&nbsp;<label style='color:red;font-size:26px;' >"+a+"</label></span><br/>" +
			 "<span style='font-size:24px;text-align:center;padding-top:9px;'>请确认以上信息是否正确!</span></div>";
	}
	else
	{*/
		
		 div="<div  id='divInvoiceInfo'><span style='font-size:18px;text-align:left;'>购方名称：&nbsp;&nbsp;<label style='color:red;font-size:26px;' >"+a+"</label></span><br/>" +
		 "<span style='font-size:18px;text-align:left;'>纳税人识别号：&nbsp;&nbsp;<label style='color:red;font-size:26px;' >"+$("#invTaxNo").val()+"</label></span><br/>" +
		 "<span style='font-size:18px;text-align:left;'>地址、电话：&nbsp;&nbsp;<label style='color:red;font-size:26px;' >"+$("#invAddrPhone").val()+"</label></span><br/>" +
		 "<span style='font-size:18px;text-align:left;'>开户行及账号：&nbsp;&nbsp;<label style='color:red;font-size:26px;' >"+$("#invBank").val()+"</label></span><br/>" +
			"<span style='font-size:18px;text-align:left;'>邮箱地址：&nbsp;&nbsp;&nbsp;<label style='color:red;font-size:26px;' >"+
			mail+"</label></span>" +
		"<span style='font-size:24px;text-align:center;padding-top:9px;'>请确认以上信息是否正确，邮箱请填写真实的地址，否则将无法收到电子发票邮件!</span></div>";
	/*}*/
	 
	/**lee_solar modify 2016-07-08 前台顾客开票流程优化 end*/
	if(isClient)	{
	   $.ocAlert("confirm",div,option);
	}else{
		var c="";
		/**lee_solar modify 2016-07-08 前台顾客开票流程优化 start*/
		var msg="请确认：" +
				"购方名称："+$("#invTitle").val()+"\r\n"+	
				"纳税人识别号："+$("#invTaxNo").val()+"\r\n"+
				"地址、电话："+$("#invAddrPhone").val()+"\r\n"+
				"开户行及账号："+$("#invBank").val()+"\r\n"+
				"邮箱地址："+$("#hxEmail").val()+"\r\n"+
				"以上信息是否正确，邮箱请填写真实的地址，否则将无法收到电子发票邮件!";
		 c = confirm(msg);
		/*	if(mail=="")
			{
				 c = confirm("请确认 购方名称:"+a+" 信息是否正确?");
			}
			else
			{
				 c = confirm("请确认 购方名称:"+a+",邮箱地址:"+mail+" 信息是否正确?");
			}*/
			/**lee_solar modify 2016-07-08 前台顾客开票流程优化 end*/
		if(c==false){
			return false;
		}else{
			addIns(invoiceTitle,mobile);
		}
	}
  
});

function addIns(invoiceTitle,mobile){
	var orderImeiNum=$("#orderImeiNum").val();
	// 带串号和抬头的申请
	//jQuery('body').showLoading({});
	apply(orderImeiNum,invoiceTitle,mobile);
}


// 下载PDF
$("#downloadPDF").click(function(){
	$.fileDownload($("#invUrl").val(),{    
		failCallback: function (responseHtml, url) {
		}
	});
});
// 发送邮箱按钮点击
$("#sendEmail").click(function(){
    $("#send-js").show();    
});
// 发送邮箱发送按钮点击
$("#send").click(function(){
	
	/**wang_mengyan modify 2016-07-19 修改邮箱验证规则 start*/
	var reg=/^\w+([-_.]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,6})+$/;
	/**wang_mengyan modify 2016-07-19 修改邮箱验证规则 end*/
	
    if($("#emailText").val()==''){
    	if(isClient)	{
		   $.ocAlert("error",jsMsg.mailEmpty);
		}else{
			alert(jsMsg.mailEmpty);
		}
    	return false;
    };
   
    if(reg.test($("#emailText").val())){
    	 jQuery('body').showLoading({});
    	$.ajax({
    		url: contextPath+'/inv/apply/sendemail.do',
    		dataType:'json',
    		type : 'post',
    		data:{
    			PDFUrl:$("#invUrl").val(),
    			Email:$("#emailText").val()
    		}
	    	,error:function(){
				jQuery('body').hideLoading();
				if(isClient)
				{
				   $.ocAlert("error",jsMsg.insError);
				}
				else
				{
					alert(jsMsg.insError);
				}
			},
    		success:function(data){
    			jQuery('body').hideLoading();
    			if(data.resultJson.errorCode=='0000'){
    				if(isClient)
					{
					   $.ocAlert("success",jsMsg.mailSuccess);
					}
					else
					{
						alert(jsMsg.mailSuccess);
					}
    				$("#send-js").hide();
    			}else{
    				if(isClient)
					{
    					$.ocAlert("error",data.resultJson.msg); 
					}
					else
					{
						alert(data.resultJson.msg);
					}
    			}	
    		}
			
    	})
    }else{
    	if(isClient)
		{
			$.ocAlert("error",jsMsg.mailError); 
		}
		else
		{
			alert(jsMsg.mailError);
		}
    	$("#emailText").val("");
    	
    } 
});


// 前一张点击事件
$("#pre").click(function() {
	$("li[name='li_salesAddr']").remove();
	$("li[name='li_salesName']").remove();
	numNow = numNow - 1;
	var invItem = invoiceList[numNow - 1];
	// $("#invNumNow").text(numNow);
	setValues(invItem);
	if (numNow == 1) {
		// 如果当前张数为1，则隐藏前一页按钮
		$("#pre").hide();
		$("#next").show();
		// $("#next").removeClass("aspect-left un").addClass("aspect-left on")
	}
});
// 下一张点击事件
$("#next").click(function() {
	$("li[name='li_salesAddr']").remove();
	$("li[name='li_salesName']").remove();
	numNow = numNow + 1;
	var invItem = invoiceList[numNow-1];
	// $("#invNumNow").text(numNow);
	setValues(invItem);
	$("#pre").show();
	$("#pre").removeClass("aspect-left un").addClass("aspect-left on");
	if (numNow == invoiceList.length) {
		// 如果当前张数为数组长度，则隐藏下一页按钮
		$("#next").hide();
	}
});

//获取JS提示错误信息
$.ajax({
	url: contextPath+'/inv/apply/getJsMsg.do',
	dataType:'json',
	type : 'post',
	data:{
	},
	async: false,
	// TODO
	success:function(data){
		if(data.resultFlag=="SUCCESS")
	    {
			//获取JS页面错误 提示消息
			var js = data.resultJson.data
			jsMsg=JSON.parse(js);
	    }
	}
	
});


//判断是否走页面，还是二维码扫描直接进第二个页面
var zixingId  = $("#zixingId").val(); //订单串号,从后台orderSerNum传入，隐藏字段值
var storeCodeId  = $("#storeCode").val();
if(zixingId != "" && zixingId !=null)
{
	  //判断订单串号是否存在，存在的话，直接返回
	  var isInsFlag=true;
	  var dataMsg = "";
	  var isInfoFlag = true;  //扫秒时，将第二个页面隐藏
	  $.ajax({ 
		    url: contextPath+'/inv/apply/cusapply.do',					
			dataType:'json',
			type : 'post',
			async: false,
			data:{
				storeCodeId:storeCodeId,
				orderImeiNum:zixingId,
				bizDate:$("#bizDate").val(),
				transactionId:$("#transactionID").val()
			},
			success:function(data){						
				if(data.resultFlag=='FAIL'){
					jQuery('body').hideLoading();
					if(isClient)
					{
						$.ocAlert("error",jsMsg.insError);
					}
					else
					{
						alert(jsMsg.insError);
					}
				}else{
					if(data.resultJson.errorCode==0000)
					{
						jQuery('body').hideLoading();
						$("#login-js").hide();
						$("#info-js").show();
						getInvList(data.resultJson.data);
						isInfoFlag=false;
					}
					else if(data.resultJson.errorCode==7777) //进入发票第二个页面
					{
						//空实现	
						//如果是支付宝扫码
						if("alipay"==platForm){
							var userId = $("#aliUserID").val();
							//如果已经授权则绑定title
							if(userId != undefined && userId != ''){
								bindAliUserTitles();
							}else{
								var flag = $("#aliUserAhtuState").val();
								//代表已经授权过
								if(flag == 'Y'){
									  bindAliUserTitle();
								  }else{	
									$("#invTitle").focus(function(){
										//如果未授权，则需要后台获取授权信息
										//重新进入页面
										window.location.href=contextPath + '/inv/apply/getAliAuthPage.do?storeCode='+storeCodeId+'&bizDate='
										+$("#bizDate").val()+'&transactionId='+$("#transactionID").val()+'&orderIemiNum='+zixingId+'&pageRes=invoicePage';
									});
								}
							}
						}else if(data.resultJson.data!=null)
						{	
							//如果非支付宝扫码
							$("#invTitle").val(data.resultJson.data.invoiceTitle);
						}
					}
					else
					{
						jQuery('body').hideLoading();
						isInsFlag = false;
						dataMsg=data.resultJson.msg;
					}
					
				}
			}

		});
	  
	  if(!isInfoFlag)  //判断当扫秒时，已存在订单，则将第二个页面隐藏掉
	  {
		// $("#apply-js").hide();
		  //if(platForm=='alipay'){	
			  //transferOrderInvoice();
		  //}
		  return;
	  }
	 
	  
	  if(!isInsFlag)
	  {
		  if(isClient)
			{
				$.ocAlert("error",dataMsg);
			}
			else
			{
				alert(dataMsg);
			}
		  return false;
	  }
	  
	  
	  openLoading();
	  $.ajax({ //二维码扫描进入时
		    url: contextPath+'/inv/apply/askOrderDetails.do',					
			dataType:'json',
			type : 'post',
			async: false,
			data:{
				storeCodeId:storeCodeId,
				orderImeiNum:zixingId
			}
			,error:function(){
				jQuery('body').hideLoading();
				if(isClient)
				{
					$.ocAlert("error",jsMsg.insError);
				}
				else
				{
					alert(jsMsg.insError);
				}
							
			},
			success:function(data){						
				if(data.resultFlag=='FAIL'){
					jQuery('body').hideLoading();
					if(isClient)
					{
						$.ocAlert("error",jsMsg.insError);
					}
					else
					{
						alert(jsMsg.insError);
					}
				}else{
					//给订单串号值 
					$("#orderImeiNum").val(zixingId);
					
					order=data.resultJson.data;
					jQuery('body').hideLoading();
					$("#login-js").hide();
					$("#apply-js").show();
					$("#businessDate1").text("交易时间："+order.responseBdate);
					var storeName1="";
					storeName1=order.storeName.toString();
					if(storeName1.length>25){
						storeName1=storeName1.substring(0,25)+"..."
						$("#storeName").attr("title",order.storeName);
						$("#storeName").text("餐厅名称："+storeName1);
					}else{				
						$("#storeName").text("餐厅名称："+order.storeName);
					}
					//$("#storeName").text("餐厅名称："+order.storeName);
					$("#totalOrderPrice").text("订单总金额：     "+parseFloat(order.netPrice/100)+"元");
					var totalAmount = 0;
					/**wang_mengyan modify 2016-07-27 加载前清空 start*/
					$("#table").html("");
					/**wang_mengyan modify 2016-07-27 加载前清空 end*/
					$.each(order.financeDeptList, function(i, obj) {
						var objPrice = parseFloat(obj.modifyTotalPrice);
						if(objPrice!=0)
						{
							totalAmount += parseFloat(obj.modifyTotalPrice);
							
							$("#table").append(
									"<li class='td'><span class='til'>"+ obj.financeName +"</span><span class='con'>"
									+ parseFloat(obj.modifyTotalPrice/100) + "元</span><span class='con'>"+ obj.taxRate*100 +"%</span> </li>"
							)									
						}
					});
					$("#total").text(parseFloat(totalAmount/100)+"元");
					if("alipay"!=platForm){						
						if(data.resultJson.invoiceTitle!=null){
							$("#invTitle").val(data.resultJson.invoiceTitle);							
						}					
					}
				}
			}

		});
	  closeLoading();
}
/**lee_solar modify 2016-07-08 前台顾客开票流程优化 start*/
$("#sp_tax").click(function(){

	if($("div[sp_tax=true]").is(":hidden")){
	$("div[sp_tax=true]").show();
	}else{
	$("div[sp_tax=true]").hide();
	}
	});
$("#sp_tax_img").click(function(){

	if($("div[sp_tax=true]").is(":hidden")){
	$("div[sp_tax=true]").show();
	}else{
	$("div[sp_tax=true]").hide();
	}
	});
	$("div[sp_tax=true]").hide();
	/**lee_solar modify 2016-07-08 前台顾客开票流程优化 end*/
	//电子订单开票
	if($("#orderRes").val()=="elecOrder"){	
		openLoading();
		$.ajax({ //二维码扫描进入时
			url: contextPath+'/elecOrderInvoice/getOrderInfo.do',					
			dataType:'json',
			type : 'post',
			async: false,
			data:{
				'storeCode':$("#storeCode").val(),
				'businessDate':$("#bizDate").val(),
				'orderId':$("#orderId").val(),
				'posType':$("#posType").val()
			},
			error:function(){
			jQuery('body').hideLoading();
			if(isClient)
			{
				$.ocAlert("error",jsMsg.insError);
			}
			else
			{
				alert(jsMsg.insError);
			}
			
		},
		success:function(data){
			jQuery('body').hideLoading();
			if(data.resultJson.errorCode=='0000'){
				order=data.resultJson.data;
				$("#orderImeiNum").val(order.orderImeiNum);
				//$("#orderImeiNum").attr("readonly","readonly");
			}else{
				if(isClient)
				{
					$.ocAlert("error",data.resultJson.msg);
				}
				else
				{
					alert(data.resultJson.msg);
				}
			}

		}
		
		});
	}
});


function isemptypdf()
{
	 var  pdfUrl=$("#invUrl").val();
	 
	if(pdfUrl==""||pdfUrl==null)
     {
		 if(isClient)
			{
				$.ocAlert("error",jsMsg.insError); 
			}
			else
			{
				alert(jsMsg.insError);
			}
		 return false;
     }
	 else
	 {
		// pdfUrl = contextPath+"/inv/apply/pdfdownload.do?pdfurl="+pdfUrl;
		 
		// window.location.href=pdfUrl;
		 window.open(pdfUrl);
	 }
	 //var pdfUrl="http://yum.shdzfp.com/pdf/g?p=K591x3AtsS504uuSOhfTzSeiCjLLq7_Xx6WqxLH9LQU";
	


	 
}

function bindAliUserTitles(){
	//已经获取支付宝的用户id
	var titles=[];

	// 启动运行查AutoCompelet，法人公司
	$.ajax({
		cache : true,
		type : "POST",
		url : contextPath + '/inv/apply/getUserTitles.do',
		data : {
			"aliUserId":$("#aliUserID").val(),
			"accessToken":$("#accessToken").val(),
			"refreshToken":$("#refreshToken").val()
		},
		dataType : "json",
		success : function(data) {
			// response的数据会返回给source，作为展示用的数据
			titles = data.resultJson.data;
			$("#invTitle").autocomplete({
				source : titles,
				autoFocus : false,
				delay : 100,
				disabled : false,
				minLength : 0,
				// 这是选中时触发的事件，可以在此执行相应的动作
				select : function(event, ui) {
					changeTitleInfo(ui.item);
					return false;
				},
				focus : function() {
					return false;
				}
			});
			changeTitleInfo(titles[0]);
		}
	});
}
function changeTitleInfo(item){
	//alert("name:"+item.title_name+" "+ "email:"+item.user_email+" "+"mobile："+item.user_mobile+" "+"adress:"+user_address+" "+"openBack:"+item.open_bank_name+" " + "bankAcc:"+item.open_bank_account);
	// ui.item.label这里获取的是当前选中的记录的label，相当于下拉框中的text
	$("#invTitle").val(item.title_name);
	// ui.item.label这里获取的是当前选中的记录的value，相当于下拉框中的value
	var email = item.user_email;
	$("#hxEmail").val(email);					
	var mobile = item.user_mobile;
	var taxNo = item.tax_register_no;
	var adress =item.user_address;	
	var bank = item.open_bank_name;
	var account = item.open_bank_account;	
	$("#invTaxNo").val(taxNo);
	var myreg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1})|(17[0-9]{1}))+\d{8})$/; 
	if(!(mobile=='' || mobile==null )){
		if(myreg.test(mobile)){
			$("#mobile").val(mobile);
			if(!(adress==null || adress=='')){
				$("#invAddrPhone").val(adress);
			}else{
				$("#invAddrPhone").val("");
			}
		}else{
			if(!(adress==null || adress=='')){
				$("#invAddrPhone").val(adress+" "+ mobile);
			}else{
				$("#invAddrPhone").val("");
			}
		}
	}else{
		$("#mobile").val("");
		if(!(adress==null || adress=='')){
			$("#invAddrPhone").val(adress);
		}else{
			$("#invAddrPhone").val("");
		}
	}
	if(!(bank=='' || account=='' || bank==null || account==null )){
		$("#invBank").val(bank+" "+ account);
	}else{
		$("#invBank").val("");
	}
	if(!($("#invTaxNo").val()=="" && $("#invAddrPhone").val()=="" && $("#invBank").val()=="")) {			
		$("div[sp_tax=true]").show();							
	}else{
		$("div[sp_tax=true]").hide();
	}
}
// ///////////////////function///////////////////////
// 获取订单详细信息或者直接是发票信息
function  applyOrderDetails(orderImeiNum){
	//开启蒙版
	/**lee_solar modify 2016-07-08 前台顾客开票流程优化 start*/
	jQuery('body').showLoading({"msg":"发票数据查询中，请耐心等待..."});
	/**lee_solar modify 2016-07-08 前台顾客开票流程优化 end*/
	// 去获取订单信息
	$.ajax({
		url: contextPath+'/inv/apply/cusapply.do',
		dataType:'json',
		type : 'post',
		data:{
			orderImeiNum:orderImeiNum
		}
		,error:function(){
			jQuery('body').hideLoading();
			 if(isClient)
				{
					$.ocAlert("error",jsMsg.insError); 
				}
				else
				{
					alert(jsMsg.insError);
				}
		}
		,success:function(data){
			// 返回成功结果
			// 参数错errorCode=0000成功
			if(data.resultFlag=='FAIL'){
				jQuery('body').hideLoading();
				 if(isClient)
					{
						$.ocAlert("error",jsMsg.insError); 
					}
					else
					{
						alert(jsMsg.insError);
					}
			}else if(data.resultJson.errorCode==0000){
				jQuery('body').hideLoading();
				$("#login-js").hide();
				$("#info-js").show();
				getInvList(data.resultJson.data);
				if(window.inte){
					window.clearInterval(window.inte);
					window.inte=undefined;
					$("#num-ggg").remove();
					$("#vCode").click(changeCode);
					$("#vCodeHref").click(changeCode);
				}
				// 表示已申请
			}
			else if(data.resultJson.errorCode==7777){  //进入发票第二个页面，输入发票抬头等信息
				//初始化发票抬头数据
				if(window.inte){
					window.clearInterval(window.inte);
					window.inte=undefined;
					$("#num-ggg").remove();
					$("#vCode").click(changeCode);
					$("#vCodeHref").click(changeCode);
				}
						
				$("#invTitle").val(data.resultJson.data.invoiceTitle);
				
				$.ajax({
				    url: contextPath+'/inv/apply/askOrderDetails.do',					
					dataType:'json',
					type : 'post',
					data:{
						orderImeiNum:orderImeiNum
					}
					,error:function(){
						jQuery('body').hideLoading();
						 if(isClient)
							{
								$.ocAlert("error",jsMsg.insError); 
							}
							else
							{
								alert(jsMsg.insError);
							}
					},
					success:function(data){						
						if(data.resultFlag=='FAIL'){
							jQuery('body').hideLoading();
							 if(isClient)
								{
									$.ocAlert("error",jsMsg.insError); 
								}
								else
								{
									alert(jsMsg.insError);
								}
							
						}else{
							order=data.resultJson.data;
							jQuery('body').hideLoading();
							$("#login-js").hide();
							$("#apply-js").show();
							$("#businessDate1").text("交易时间："+order.responseBdate);
							var storeName1="";
							storeName1=order.storeName.toString();
							if(storeName1.length>25){
								storeName1=storeName1.substring(0,25)+"..."
								$("#storeName").attr("title",order.storeName);
								$("#storeName").text("餐厅名称："+storeName1);
							}else{				
								$("#storeName").text("餐厅名称："+order.storeName);
							}
							//$("#storeName").text("餐厅名称："+order.storeName);
							$("#totalOrderPrice").text("订单总金额：     "+parseFloat(order.netPrice/100)+"元");
							var totalAmount = 0;
							/**wang_mengyan modify 2016-07-27 加载前清空 start*/
							$("#table").html("");
							/**wang_mengyan modify 2016-07-27 加载前清空 end*/
							$.each(order.financeDeptList, function(i, obj) {
								var objPrice = parseFloat(obj.modifyTotalPrice);
								if(objPrice!=0)
							    {
									totalAmount += parseFloat(obj.modifyTotalPrice);
								
									$("#table").append(
											"<li class='td'><span class='til'>"+ obj.financeName +"</span><span class='con'>"
											+ parseFloat(obj.modifyTotalPrice/100) + "元</span><span class='con'>"+ obj.taxRate*100 +"%</span> </li>"
									)									
							    }
							});
							$("#total").text(parseFloat(totalAmount/100)+"元");
							
							if(data.resultJson.invoiceTitle!=null){
								$("#invTitle").val(data.resultJson.invoiceTitle);
								
							}					
						}
					}

				});
			
			}else{
				// 除此全是错误，提示后台传来的错误信息
				jQuery('body').hideLoading();
				clearCookie(vuuid);
				 if(isClient)
					{
						$.ocAlert("error",data.resultJson.msg); 
					}
					else
					{
						alert(data.resultJson.msg);
					}
			}
			
		},					

	});

}
// 成功动作
function suc(){	
//	jQuery('body').showLoading({"msg":"税务局受理中，请耐心等待..."});
//	setTimeout('download()', INV_TIMEOUT);
	download();

}
// 失败动作
function fail(){
	$("#apply-js").hide();
	$("#state-js").hide();
	$("#login-js").show();
	//location.reload();
}
// 获取发票集合
function getInvList(data){
	numNow = 1;
	$("#invoiceTitle").text('');
	$("#salesName1").text('');
	$("#salesAddr1").text('');
	$("#invoiceCode1").text('');
	$("#invoiceNum1").text('');
	$("#amountWithoutTax").text('');
	$("#taxAmount").text('');
	$("#invUrl").val('');	
	$("#pre").hide();
	$("#next").hide();	
			invoiceList = data
			// $("#invNum").text(" 总数:" + data.resultJson.length + "张");
			var invoicetitle="";
			invoicetitle=invoiceList[0].invoiceTitle.toString();
			
			if(isClient){
				if(invoicetitle.length>25){
					invoicetitle=invoicetitle.substring(0,25)+"..."
					$("#invoiceTitle").attr("title",invoiceList[0].invoiceTitle);
					$("#invoiceTitle").text(invoicetitle);
				}else{				
					$("#invoiceTitle").text(invoiceList[0].invoiceTitle);
				}
			}else{
				if(invoicetitle.length>12){
					invoicetitle=invoicetitle.substring(0,12)+"..."
					$("#invoiceTitle").attr("title",invoiceList[0].invoiceTitle);
					$("#invoiceTitle").text(invoicetitle);
				}else{				
					$("#invoiceTitle").text(invoiceList[0].invoiceTitle);
				}
			}
			/**wang_mengyan modify 2016-07-11   销方名称显示方式优化 start*/
			//$("#salesName1").text(invoiceList[0].salesName==null?"":invoiceList[0].salesName);
			
			
			var salesName="";
			var salesNameEx="";
			salesName=invoiceList[0].salesName.toString();
			
			
			if(isClient){	
				if(salesName.length>25){
					
					var	salesNameSub=salesName.substring(0,25);
					salesNameEx=salesName.substring(25,salesName.length);					
						$("#salesName1").text(salesNameSub);			
						var partNum=0;
						
						if(salesNameEx.length%40==0){
							 partNum=salesNameEx.length/40;
						}else{
							 partNum = salesNameEx.length/40+ 1;
						}
						
						 for( var i = 0; i < partNum - 1; i++ ){
							 var nameSub=  salesNameEx.substring( i * 40, (i+1)*40);
							 $("#salesName"+(i+1)).parent().after("<li name='li_salesName'><span  style='float:right;' id='salesName"+(i+2)+"'>&nbsp;&nbsp;"+nameSub+"</span></li>");
							 
						 }
						
						
					}else{
						$("#salesName1").text(salesName);
					}
			}else{
	                if(salesName.length>12){
					var	salesNameSub=salesName.substring(0,12);
						salesNameEx=salesName.substr(12);	
						
						
						$("#salesName1").text(salesNameSub);	
						
						
						
						if(salesNameEx.length>19){
							var nameSub1=salesNameEx.substring(0,19);
							var nameSub2=salesNameEx.substr(19);
							 $("#salesName1").parent().after("<li name='li_salesName'><span  style='float:right;' id='salesName2'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+nameSub1+"</span></li>");
							 $("#salesName1").parent().next().after("<li name='li_salesName'><span  style='float:right;' id='salesName3'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+nameSub2+"</span></li>");
						}else{
							 $("#salesName1").parent().after("<li name='li_salesName'><span  style='float:right;' id='salesName2'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+salesNameEx+"</span></li>");
						}
						
						
						
						
					}else{
						$("#salesName1").text(salesName);
					}
			}
			
			
			
			/**wang_mengyan modify 2016-07-11 销方名称显示方式优化 end*/
			
			
			
			
			
			var salesAddr="";
			var salesAddrEx="";
			salesAddr=invoiceList[0].salesAddr.toString();
			
			
			if(isClient){	
				if(salesAddr.length>25){
					
					var	salesAddrSub=salesAddr.substring(0,25);
						salesAddrEx=salesAddr.substring(25,salesAddr.length);					
						$("#salesAddr1").text(salesAddrSub);			
						var partNum=0;
						
						if(salesAddrEx.length%40==0){
							 partNum=salesAddrEx.length/40;
						}else{
							 partNum = salesAddrEx.length/40+ 1;
						}
						
						 for( var i = 0; i < partNum - 1; i++ ){
							 var addrSub=  salesAddrEx.substring( i * 40, (i+1)*40);
							 $("#salesAddr"+(i+1)).parent().after("<li name='li_salesAddr'><span  style='float:right;' id='salesAddr"+(i+2)+"'>&nbsp;&nbsp;"+addrSub+"</span></li>");
							 
						 }
						
						
					}else{
						$("#salesAddr1").text(salesAddr);
					}
			}else{
	                if(salesAddr.length>12){
					var	salesAddrSub=salesAddr.substring(0,12);
						salesAddrEx=salesAddr.substring(12,salesAddr.length);	
						$("#salesAddr1").text(salesAddrSub);			
						var partNum=0;
						
						if(salesAddrEx.length%30==0){
							 partNum=salesAddrEx.length/30;
						}else{
							 partNum = salesAddrEx.length/30+ 1;
						}
						
						 for( var i = 0; i < partNum - 1; i++ ){
							 var addrSub=  salesAddrEx.substring( i * 30, (i+1)*30);
							 $("#salesAddr"+(i+1)).parent().after("<li name='li_salesAddr'><span  style='float:right;' id='salesAddr"+(i+2)+"'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+addrSub+"</span></li>");
							 
						 }
						
						
					}else{
						$("#salesAddr1").text(salesAddr);
					}
			}
			
			
			
			/*$("#salesAddr1").text(invoiceList[0].salesAddr==null?"":invoiceList[0].salesAddr);*/
			$("#invoiceCode1").text(invoiceList[0].invoiceCode==null?"":invoiceList[0].invoiceCode);
			$("#invoiceNum1").text(invoiceList[0].invoiceNum==null?"":invoiceList[0].invoiceNum);
			$("#amountWithoutTax").text(invoiceList[0].amountWithoutTax==null?"":invoiceList[0].amountWithoutTax+"元");
			$("#taxAmount").text(invoiceList[0].taxAmount==null?"":invoiceList[0].taxAmount+"元");
			$("#totalPrice").text(
					isNaN(parseFloat(invoiceList[0].amountWithoutTax)+parseFloat(invoiceList[0].taxAmount))?"暂无":(parseFloat(invoiceList[0].amountWithoutTax)+parseFloat(invoiceList[0].taxAmount)).toFixed(2)+"元"
							);
			$("#invUrl").val(invoiceList[0].invoiceDownloadUrl);
			
			//发票为空，则页面不显示发票下载等，提示用户信息
			var falg="1";
			if(invoiceList!=null)
		    {
				for(var i=0;i<invoiceList.length;i++)
					{
						var obj=invoiceList[i];
						if(obj.invoiceDownloadUrl==""||obj.invoiceDownloadUrl==null)
						{
							falg="2";
							break;
						}
					}
		    }
			
			if(falg=="2")
		    {
				$("#diverroId").hide(); 
				$(".tips").hide(); 
				$("#divMsgId").show()
		    }
			else
		    {
				$("#diverroId").show(); 
				$(".tips").show(); 
				$("#divMsgId").hide();
		    }
			
			
			
			if (invoiceList.length > 1) {
				$("#next").show();
			}	
			
			 //复制url点击事件
			$("#copyURL").zclip({
			 path:contextPath+'/resources/js/inv/ZeroClipboard.swf', //
			// 记得把ZeroClipboard.swf引入到项目中
			 copy:function(){
			 return $("#invUrl").val();
			 },
			 afterCopy:function(){/* 复制成功后的操作 */
				 if(isClient)
					{
					 $.ocAlert("success",jsMsg.linkSuccess);
					}
					else
					{
						alert(jsMsg.linkSuccess);
					}
			 
			 }
							
			 });
			 
			 //获取发票二维码信息
			// var zxingUrl = invoiceList[0].zxingUrl;
			 //var fullUrl = "http://wxqyh.51fapiao.cn/eyun/scan/"+zxingUrl+".do";
			 //发票第三个页面生成二维码
			// $("#showzxingId").html("");
			 //jQuery('#showzxingId').qrcode({width:130,height:130,correctLevel:0,text:zxingUrl});
			 /* <!-- lee_solar modify 2016-07-06 修改样式 start  --> */
			if(data[0].pdfExpireTime){
				$(".tips").html("电子发票已生成，请按需点击以下按钮获取!<br>发票PDF请于"+data[0].pdfExpireTime+"之前下载！")
			}else{
				$(".tips").html("电子发票已生成，请按需点击以下按钮获取!<br>")
			}
				/* <!-- lee_solar modify 2016-07-06 修改样式 end  --> */
}



// 给发票页面设置值
function setValues(invItem) {
	// 先清除
	
	$("#invoiceTitle").text('');
	$("#salesName1").text('');
	$("#salesAddr1").text('');
	$("#invoiceCode1").text('');
	$("#invoiceNum1").text('');
	$("#amountWithoutTax").text('');
	$("#taxAmount").text('');
	$("#totalPrice").text('')

	$("#invUrl").val('');
	
	// 再填充
	var invoicetitle="";
	invoicetitle=invItem.invoiceTitle.toString();
	if(invoicetitle.length>25){
		invoicetitle=invoicetitle.substring(0,25)+"..."
		$("#invoiceTitle").attr("title",invItem.invoiceTitle);
		$("#invoiceTitle").text(invoicetitle);
	}else{				
		$("#invoiceTitle").text(invItem.invoiceTitle);
	}
	
	
	/**wang_mengyan modify 2016-07-11   销方名称显示方式优化 start*/
	//$("#salesName1").text(invItem.salesName==null?"":invItem.salesName);
	
	
	var salesName="";
	var salesNameEx="";
	salesName=invItem.salesName.toString();
	
	
	if(isClient){	
		if(salesName.length>25){
			
			var	salesNameSub=salesName.substring(0,25);
			salesNameEx=salesName.substring(25,salesName.length);					
				$("#salesName1").text(salesNameSub);			
				var partNum=0;
				
				if(salesNameEx.length%40==0){
					 partNum=salesNameEx.length/40;
				}else{
					 partNum = salesNameEx.length/40+ 1;
				}
				
				 for( var i = 0; i < partNum - 1; i++ ){
					 var nameSub=  salesNameEx.substring( i * 40, (i+1)*40);
					 $("#salesName"+(i+1)).parent().after("<li name='li_salesName'><span  style='float:right;' id='salesName"+(i+2)+"'>&nbsp;&nbsp;"+nameSub+"</span></li>");
					 
				 }
				
				
			}else{
				$("#salesName1").text(salesName);
			}
	}else{
		if(salesName.length>12){
			var	salesNameSub=salesName.substring(0,12);
				salesNameEx=salesName.substr(12);	
				
				
				$("#salesName1").text(salesNameSub);	
				
				
				
				if(salesNameEx.length>19){
					var nameSub1=salesNameEx.substring(0,19);
					var nameSub2=salesNameEx.substr(19);
					 $("#salesName1").parent().after("<li name='li_salesName'><span  style='float:right;' id='salesName2'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+nameSub1+"</span></li>");
					 $("#salesName1").parent().next().after("<li name='li_salesName'><span  style='float:right;' id='salesName3'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+nameSub2+"</span></li>");
				}else{
					 $("#salesName1").parent().after("<li name='li_salesName'><span  style='float:right;' id='salesName2'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+salesNameEx+"</span></li>");
				}
												
			}else{
				$("#salesName1").text(salesName);
			}
	}
	
	
	
	/**wang_mengyan modify 2016-07-11 销方名称显示方式优化 end*/
	
	
	
	//$("#salesAddr1").text(invItem.salesAddr==null?"":invoiceList[0].salesAddr);
	
	var salesAddr="";
	var salesAddrEx="";
	salesAddr=invItem.salesAddr.toString();
	
	
	if(isClient){	
		if(salesAddr.length>25){
			
			var	salesAddrSub=salesAddr.substring(0,25);
				salesAddrEx=salesAddr.substring(25,salesAddr.length);					
				$("#salesAddr1").text(salesAddrSub);			
				var partNum=0;
				
				if(salesAddrEx.length%40==0){
					 partNum=salesAddrEx.length/40;
				}else{
					 partNum = salesAddrEx.length/40+ 1;
				}
				
				 for( var i = 0; i < partNum - 1; i++ ){
					 var addrSub=  salesAddrEx.substring( i * 40, (i+1)*40);
					 $("#salesAddr"+(i+1)).parent().after("<li name='li_salesAddr'><span  style='float:right;' id='salesAddr"+(i+2)+"'>&nbsp;&nbsp;"+addrSub+"</span></li>");
					 
				 }
				
				
			}else{
				$("#salesAddr1").text(salesAddr);
			}
	}else{
            if(salesAddr.length>12){
			var	salesAddrSub=salesAddr.substring(0,12);
				salesAddrEx=salesAddr.substring(12,salesAddr.length);					
				$("#salesAddr1").text(salesAddrSub);			
				var partNum=0;
				
				if(salesAddrEx.length%30==0){
					 partNum=salesAddrEx.length/30;
				}else{
					 partNum = salesAddrEx.length/30+ 1;
				}
				
				 for( var i = 0; i < partNum - 1; i++ ){
					 var addrSub=  salesAddrEx.substring( i * 30, (i+1)*30);
					 $("#salesAddr"+(i+1)).parent().after("<li name='li_salesAddr'><span  style='float:right;' id='salesAddr"+(i+2)+"'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+addrSub+"</span></li>");
					 
				 }
				
				
			}else{
				$("#salesAddr1").text(salesAddr);
			}
	}
	
	
	$("#invoiceCode1").text(invItem.invoiceCode==null?"":invItem.invoiceCode);
	$("#invoiceNum1").text(invItem.invoiceNum==null?"":invItem.invoiceNum);
	$("#amountWithoutTax").text(invItem.amountWithoutTax==null?"":invItem.amountWithoutTax+"元");
	$("#taxAmount").text(invItem.taxAmount==null?"":invItem.taxAmount+"元");
	$("#totalPrice").text(isNaN(parseFloat(invItem.amountWithoutTax)+parseFloat(invItem.taxAmount))?"暂无":(parseFloat(invItem.amountWithoutTax)+parseFloat(invItem.taxAmount)).toFixed(2)+"元");
	$("#invUrl").val(invItem.invoiceDownloadUrl);
	
	 //获取发票二维码信息
	 //获取发票二维码信息
	 //var zxingUrl = invItem.zxingUrl;
	 //var fullUrl = "http://wxqyh.51fapiao.cn/eyun/scan/"+zxingUrl+".do";
	 //发票第三个页面生成二维码
	// $("#showzxingId").html("");
	// jQuery('#showzxingId').qrcode({width:130,height:130,correctLevel:0,text:zxingUrl}); 
	
}

 



// 发送发票申请（具有发票抬头和订单串号）
function apply(orderImeiNum,invoiceTitle,mobile){
	// 开启蒙版
	//点击事件直接打开蒙版
	/**
	 *     //购方纳税人识别号
    private String buyRatePayer; 
    
    //购方地址电话
    private String buyAddTel;
    
    //购方银行帐号
    private String buyBank;
20,50(汉字),50

	 */
	openLoading();
	$.ajax({
		url:  contextPath+'/inv/apply/apply.do',
		dataType:'json',
		type : 'post',
		// async:false,
		data:{
			'orderImeiNum':orderImeiNum,
			'insName':invoiceTitle,
			'mobile':mobile,
			'Email':$("#hxEmail").val()
			/**lee_solar modify 2016-07-08 前台顾客开票流程优化 start*/
			,"buyRatePayer":$("#invTaxNo").val().trim()
			,"buyAddTel":$("#invAddrPhone").val().trim()
			,"buyBank":$("#invBank").val().trim()
			/**lee_solar modify 2016-07-08 前台顾客开票流程优化 end*/
		},error:function(){
			closeLoading();
			if(isClient)	{
				$.ocAlert("error",jsMsg.insError); 
			}else{
				alert(jsMsg.insError);
			}
		},
		success:function(data){
			// TODO
			// 申请成功
			if(data.resultFlag=='FAIL'){
				// 关闭蒙版
				closeLoading();
				if(isClient){
					$.ocAlert("error",jsMsg.insError); 
				}else{
					alert(jsMsg.insError);
				}
			}else if(data.resultJson.errorCode=='0000'){  
				closeLoading();
				// 成功
				suc();	
				if(isClient){
					$.ocAlert("success",jsMsg.inssus,{onOk:function(){
						// 成功
						// 成功时执行
						
					}});
				}else{
					alert(jsMsg.inssus);
					// 成功
					// 成功时执行					
				}				
				$(".state-box").removeClass("fail").addClass("success");
			}else{
				// 关闭蒙版
				jQuery('body').hideLoading();
				if(isClient)	{
					$.ocAlert("error",data.resultJson.msg,{onOk:function(){
						// 失败时执行的
						/**wang_mengyan modify 2016-07-27 申请发票失败返回第二个页面 start*/
						$("#apply-js").show();
						$("#state-js").hide();
						$("#login-js").hide();
						/**wang_mengyan modify 2016-07-27 申请发票失败返回第二个页面 end*/
					}});
				}else{
					alert(data.resultJson.msg);
					// 失败时执行的
					/**wang_mengyan modify 2016-07-27 申请发票失败返回第二个页面 start*/
					$("#apply-js").show();
					$("#state-js").hide();
					$("#login-js").hide();
					/**wang_mengyan modify 2016-07-27 申请发票失败返回第二个页面 end*/
				}
				
				$(".state-box").removeClass("success").addClass("fail");
				$("#stateMsg").html("");
				$("#stateMsg").html(data.resultJson.msg)
			}
		}
	});	
}





//验证码切换事件
function changeCode(){	 
	if(window.inte){
		//window.clearInterval(window.inte); 
		return;
	}
	$("#vCode").unbind('click').unbind('onclick').removeAttr('onclick');
	$("#vCodeHref").unbind('click').unbind('onclick').removeAttr('onclick');
	
	/*<!-- lee_solar modify 2016-07-05 验证码从60秒变成30秒 start -->*/
	window.timeNum=30;
	/*<!-- lee_solar modify 2016-07-05 验证码从60秒变成30秒 start -->*/
	var $vDiv=$("<div id='num-ggg'>");
	$vDiv.html("<span style='margin:6px;'>" +30+"</span>");	
	$vDiv.css("position","absolute");
	$vDiv.css("z-index","19999");
	$vDiv.css("top",$("#vCode").offset().top);
	$vDiv.css("left",$("#vCode").offset().left);
	$vDiv.width($("#vCode").width());
	$vDiv.height($("#vCode").height());
	$("#vCode").hide();
	//$("#vCodeHref").hide();
	$("body").append($vDiv);
	/*<!-- lee_solar modify 2016-07-05 验证码从60秒变成30秒 end -->*/
	window.inte=setInterval(reqCode,1000);

}

function reqCode(){
	//console.log("-------"+window.timeNum);
	window.timeNum=window.timeNum-1;
//	$("#num-ggg").text(window.timeNum);
	$("#num-ggg").html("<span style='margin-top:6px;'>" +window.timeNum+"</span>");
	if(window.timeNum<=0){
	var u = vUrl + "?uuidKey=" + vuuid +"&temp="+Math.random(); 
		$("#vCode").attr("src",u);	
		window.clearInterval(window.inte);
		window.inte=undefined;
		$("#num-ggg").remove();
		$("#vCode").show();
		//$("#vCodeHref").show();
		$("#vCode").click(changeCode);
		$("#vCodeHref").click(changeCode);
		$("#vCodeId").val("");
	}
}

function enableLoading(){	
	var width="150px"
	if(isClient)	{
		width="400px";
	}
	$("#amountWithoutTax").css("background","url("+contextPath+"/resources/css/images/inv-loading.gif) no-repeat center center").css("width",width).html("&nbsp;");
	$("#taxAmount").css("background","url("+contextPath+"/resources/css/images/inv-loading.gif) no-repeat center center").css("width",width).html("&nbsp;");
	$("#totalPrice").css("background"," url("+contextPath+"/resources/css/images/inv-loading.gif) no-repeat center center").css("width",width).html("&nbsp;");
	$("#salesName1").css("background"," url("+contextPath+"/resources/css/images/inv-loading.gif) no-repeat center center").css("width",width).html("&nbsp;");
	$("#salesAddr1").css("background"," url("+contextPath+"/resources/css/images/inv-loading.gif) no-repeat center center").css("width",width).html("&nbsp;");
	$("#invoiceCode1").css("background"," url("+contextPath+"/resources/css/images/inv-loading.gif) no-repeat center center").css("width",width).html("&nbsp;");
	$("#invoiceNum1").css("background"," url("+contextPath+"/resources/css/images/inv-loading.gif) no-repeat center center").css("width",width).html("&nbsp;");
}

function disableLoading(){	 
	$("#amountWithoutTax").css("background",'').css("width",'');
	$("#taxAmount").css("background",'').css("width",'');
	$("#totalPrice").css("background",'').css("width",'');
	$("#salesName1").css("background",'').css("width",'');
	$("#salesAddr1").css("background",'').css("width",'');
	$("#invoiceCode1").css("background",'').css("width",'');
	$("#invoiceNum1").css("background",'').css("width",'');
}

function download(){
	$("#apply-js").hide();
	$("#info-js").show();
	$("#login-js").hide();
	
	/**
	 * 	
			if(isClient){
				if(invoicetitle.length>25){
					invoicetitle=invoicetitle.substring(0,25)+"..."
					$("#invoiceTitle").attr("title",invoiceList[0].invoiceTitle);
					$("#invoiceTitle").text(invoicetitle);
				}else{				
					$("#invoiceTitle").text(invoiceList[0].invoiceTitle);
				}
			}else{
				if(invoicetitle.length>12){
					invoicetitle=invoicetitle.substring(0,12)+"..."
	 */
	$("#invoiceTitle").attr("title",$("#invTitle").val());	
	var title_25=$("#invTitle").val().length>25?($("#invTitle").val().substring(0,25)+"..."):$("#invTitle").val();
	var title_12=$("#invTitle").val().length>25?($("#invTitle").val().substring(0,12)+"..."):$("#invTitle").val(); 
	if(isClient)	{
		$("#invoiceTitle").text(title_25);		
	}else{
		$("#invoiceTitle").text(title_12);
	}
	$("#invoiceTitle").text=$("#invTitle").val();
	enableLoading();	
	
	$.ajax({
		url: contextPath+'/inv/apply/download.do',
		dataType:'json',
		type : 'post',
		async : false,
		data:{
			orderImeiNum:$("#orderImeiNum").val()
		},error:function(){
			//jQuery('body').hideLoading();
			if(isClient)
			{
				$.ocAlert("error",jsMsg.insError); 
			}
			else
			{
				alert(jsMsg.insError);
			}		
			disableLoading();
			$(".tips").html("<a href='javascript:void(0)' onclick='window.download()' >电子发票生成排队中，点击重新获取!</a>");
		},
		success:function(data){
				if(data.resultFlag=='FAIL'){
					/**lee_solar modify 2016-07-08 前台顾客开票流程优化 start*/
				//	jQuery('body').hideLoading();
					
					if(isClient)	{
						$.ocAlert("error",jsMsg.appSuccessButDownloadFail,{onOk:function(){
							$("#apply-js").hide();
							$("#info-js").hide();
							$("#login-js").hide();
							// 失败时执行的 
						     //  fail();
						}});
					}else{
						alert(jsMsg.appSuccessButDownloadFail);
						// 失败时执行的
					       //fail();
					}			
					/*
					$(".state-box").removeClass("success").addClass("fail");
					$("#stateMsg").html("");
					$("#stateMsg").html("发票申请成功，下载发票时出现问题")
						*/
					disableLoading();
					$(".tips").html("<a href='javascript:void(0)' onclick='window.download()' >电子发票生成排队中，点击重新获取!</a>");
					/**lee_solar modify 2016-07-08 前台顾客开票流程优化 end*/
					//location.reload();
				}else if(data.resultJson.errorCode=='0000'){
					// 关闭蒙版
				//	jQuery('body').hideLoading();
					$("#apply-js").hide();
					$("#info-js").show();
					$("#login-js").hide();
					if(data.resultJson.data[0].pdfExpireTime){
						$(".tips").html("电子发票已生成，请按需点击以下按钮获取!<br>发票PDF请于"+data.resultJson.data[0].pdfExpireTime+"之前下载！")
					}else{
						$(".tips").html("电子发票已生成，请按需点击以下按钮获取!<br>")
					}
					disableLoading();
					getInvList(data.resultJson.data);
				}else{	
					//发票下载失败，加上重试二次，每次相隔3秒
					downLoadTime++;
					if(downLoadTime<=2)
					{
					//	suc();
						setTimeout(suc(),3000);
					}
					
					
	//				jQuery('body').hideLoading();
					/**lee_solar modify 2016-07-08 前台顾客开票流程优化 start*/
					if(isClient)	{
						$.ocAlert("error",data.resultJson.msg+"，请稍后再试，或者拨打小票上的服务专线"); 
					}else{
						alert(data.resultJson.msg+"，请稍后再试，或者拨打小票上的服务专线");
					}
					disableLoading();
					$(".tips").html("<a href='javascript:void(0)' onclick='window.download()' >电子发票生成排队中，点击重新获取!</a>");
					/**lee_solar modify 2016-07-08 前台顾客开票流程优化 end*/
				}
			
			}
		})
}


// 获取cookie方法
function getCookie(name)
{
    var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
 
    if(arr=document.cookie.match(reg))
 
        return (arr[2]);
    else
        return null;
}



// 清除cookie
function clearCookie(name) {  
    setCookie(name, "", -1);  
}

// 设置cookie,
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

// 复制进入剪切板
//$(document).keydown(function(event) { 
//	if (event.keyCode == 13) {
////		$("#apply").click();
//		$("#apply").click();
//		$("#orderImeiNum").focus();
//	} 
//}); 

function bigImg()
{
	$("#smallId").show();
	$("#showzxingId").hide();
}

function smallImg()
{
	$("#smallId").hide();
	$("#showzxingId").show();
}

function openLoading(){
	/**lee_solar modify 2016-07-08 前台顾客开票流程优化 start*/
	jQuery('body').showLoading({"msg":"税务局发票开具受理中，请耐心等待…"});
	/**lee_solar modify 2016-07-08 前台顾客开票流程优化 start*/
}
function closeLoading(){
	jQuery('body').hideLoading();
}

function isClientPc()
{
	  //1:电脑，2：手机 
	 var system = { 
	            win: false, 
	            mac: false, 
	            xll: false, 
	            ipad:false 
	        }; 
	        //检测平台 
	        var p = navigator.platform; 
	        system.win = p.indexOf("Win") == 0; 
	        system.mac = p.indexOf("Mac") == 0; 
	        system.x11 = (p == "X11") || (p.indexOf("Linux") == 0); 
	        system.ipad = (navigator.userAgent.match(/iPad/i) != null)?true:false; 
	        //跳转语句
	        if (system.win || system.mac || system.xll||system.ipad) { 
	        	return "1";
	        } else { 
	        	return "2";   
	        }	
}


