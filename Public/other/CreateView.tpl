{*<!--

/*********************************************************************************
*  开具发票单   2011-12-01
********************************************************************************/

-->*}
	<link rel="stylesheet" href="include/css/skins/default.css" type="text/css" />
	<script language="JavaScript" type="text/javascript" src="include/js/artDialog.min.js"></script>

{*<!-- module header -->*}
<div id="oper">
	{include file='OpenInvoiceMgr/Buttons_Open.tpl'}
	{*<!-- Contents -->homePageSeperator.gif*}
	<table border=1  cellspacing=0  height=800 cellpadding=0 width=100%  align=center   >
		<tr>
			<td class="showInvoicePanelBg" valign=top width="100%" height="100%" align="center" >
				{*<!-- PUBLIC CONTENTS STARTS-->*}
				<form id="CreateView" name="CreateView" method="POST" action="index.php" onsubmit="VtigerJS_DialogBox.block();">
					<input type="hidden" name="pagenumber" value="{$smarty.request.start|@vtlib_purify}">
					<input type="hidden" name="module" value="{$MODULE}">
					<input type="hidden" name="record" value="{$INVOICEID}">
					<input type="hidden" name="orderid" id="orderid" value="{$ORDERID}">
					<input type="hidden" name="action" value="Save">            
					<input type="hidden" name="parenttab" value="{$CATEGORY}">
					<input type="hidden" name="return_module" value="{$RETURN_MODULE}">
					<input type="hidden" name="return_id" value="{$INVOICEID}">
					<input type="hidden" name="return_action" value="{$RETURN_ACTION}">
					<input type="hidden" id="opendate" value="{$OPENDATE}">
					<input type="hidden" id="invoicetype" value="{$INVOICETYPE}"> 
					<input type="hidden" id="companyinvoicenumber" value="{$COMPANYINVOICENUMBER}">
					{*定义发票代码*}
					<div id="companycode" style="position:absolute; left: 865px; top: 174px ">
						<h3>{$INVOICECODE}</h3>
					</div>
					<div id="invoiceid" style="position:absolute; left: 864px; top: 214px ">
						<h3>{$INVOICEID}</h3>
					</div>
					<div id="opendate" style="position:absolute; left: 245px; top: 246px ">
						<font size="3">{$OPENDATE}</font>
					</div>

					{*行业类别*}
					<div id="invoicetype" style="position:absolute; left: 600px; top: 248px ">
						<font size="3">{$INVOICETYPE}</font>
					</div>
					{*发票抬头*}
					<div id="opencompany" style="position:absolute; left: 180px; top: 300px;border=1px;">
						<font size="5">付款单位:</font><font size="5" ><input id="invoiceheader" name="invoiceheader" type="text" style="font-family: Arial, Helvetica, sans-serif;
								font-size: 25px;
								color: #000000;
								border:1px solid #bababa;
								padding-left:5px;
								width:700px;
								background-color:#ffffff;" value="{$OPENCOMPANY}"></font>
					</div>

					{*支票号码*}    
					<div id="ticketcode" style="position:absolute; left: 980px; top: 350px;display:none;">
						<font size="4">200000001</font>
					</div>
					{*发票商品内容*}
					<div id="ordertxt" style="position:absolute; left: 150px; top:360px;font-size: 25px; ">
						<table  border="0" width="80%">
							<head>
								<tr>
									<td width="30%">商品名称</td>
									<td width="20%">单价</td>
									<td width="20%">数量</td>
									<td width="30%">金额</td>
								</tr>
							</head>
							<tr>
								<td width="30%"><input id="productname1" name="productname1" type="text" style="font-family: Arial, Helvetica, sans-serif;color: #000000;border:1px solid #bababa;padding-left:5px;background-color:#ffffff;font-size:20px;width:80%;text-align:center;" value="{$PRODUCTNAME}"></td>
								<td width="20%"><input id="price1" name="price1" type="text" style="font-family: Arial, Helvetica, sans-serif;color: #000000;border:1px solid #bababa;padding-left:5px;background-color:#ffffff;font-size:20px;width:80%;text-align:center;" value="{$PRICE}"></td>
								<td width="20%"><input id="quantity1" name="quantity1" type="text" style="font-family: Arial, Helvetica, sans-serif;color: #000000;border:1px solid #bababa;padding-left:5px;background-color:#ffffff;font-size:20px;width:80%;text-align:center;" value="{$QUANTITY}"></td>
								<td width="30%"><input id="productmoney1" name="productmoney1" type="text" style="font-family: Arial, Helvetica, sans-serif;color: #000000;border:1px solid #bababa;padding-left:5px;background-color:#ffffff;font-size:20px;width:80%;text-align:center;" value="{$PRODUCTMONEY}"></td>
							</tr>
							<tr>
								<td width="30%"><input id="productname2" name="productname2" type="text" style="font-family: Arial, Helvetica, sans-serif;color: #000000;border:1px solid #bababa;padding-left:5px;background-color:#ffffff;font-size:20px;width:80%;text-align:center;" value=""></td>
								<td width="20%"><input id="price2" name="price2" type="text" style="font-family: Arial, Helvetica, sans-serif;color: #000000;border:1px solid #bababa;padding-left:5px;background-color:#ffffff;font-size:20px;width:80%;text-align:center;" value=""></td>
								<td width="20%"><input id="quantity2" name="quantity2" type="text" style="font-family: Arial, Helvetica, sans-serif;color: #000000;border:1px solid #bababa;padding-left:5px;background-color:#ffffff;font-size:20px;width:80%;text-align:center;" value=""></td>
								<td width="30%"><input id="productmoney2" name="productmoney2" type="text" style="font-family: Arial, Helvetica, sans-serif;color: #000000;border:1px solid #bababa;padding-left:5px;background-color:#ffffff;font-size:20px;width:80%;text-align:center;" value=""></td>
							</tr>
							<tr>
								<td width="30%"><input id="productname3" name="productname3" type="text" style="font-family: Arial, Helvetica, sans-serif;color: #000000;border:1px solid #bababa;padding-left:5px;background-color:#ffffff;font-size:20px;width:80%;text-align:center;" value=""></td>
								<td width="20%"><input id="price3" name="price3" type="text" style="font-family: Arial, Helvetica, sans-serif;color: #000000;border:1px solid #bababa;padding-left:5px;background-color:#ffffff;font-size:20px;width:80%;text-align:center;background-color:transparent;" value=""></td>
								<td width="20%"><input id="quantity3" name="quantity3" type="text" style="font-family: Arial, Helvetica, sans-serif;color: #000000;border:1px solid #bababa;padding-left:5px;background-color:#ffffff;font-size:20px;width:80%;text-align:center;background-color:transparent;" value=""></td>
								<td width="30%"><input id="productmoney3" name="productmoney3" type="text" style="font-family: Arial, Helvetica, sans-serif;color: #000000;border:1px solid #bababa;padding-left:5px;background-color:#ffffff;font-size:20px;width:80%;text-align:center;background-color:transparent;" value=""></td>
							</tr>

						</table>
					</div>
					{*合计大写*}
					<div id="uppertotalmoney" style="position:absolute; left: 160px; top:550px;overflow:hidden;zoom:1;">
						<table width="850px" border="0">
							<tr>
								<td width="610px" style="font-size:20px;" align="left">合计金额:(大写)<input id="totalmoney" type="text" style="font-family: Arial, Helvetica, sans-serif;color: #000000;border:1px solid #bababa;padding-left:5px;background-color:#ffffff;font-size:20px;text-align:left;background-color:transparent;width:350px" value="{$UPPERTOTALMONEY}" readonly="readonly"></td>
								<td  style="font-size:20px;" align="left">(小写)<input id="amountmoney" name="amountmoney" type="text" style="font-family: Arial, Helvetica, sans-serif;color: #000000;border:1px solid #bababa;padding-left:5px;background-color:#ffffff;font-size:20px;text-align:left;width:120px;" value="{$LOWERTOTALMONEY}" readonly="readonly"></td>
							</tr>
						</table>
					</div>
					{*税号和开票人*}
					<div id="operationman" style="position:absolute; left: 160px; top: 580px;overflow:hidden;zoom:1; ">
						<table width="850px" border="0">
							<tr>
								<td width="500px" style="font-family: Arial, Helvetica, sans-serif;color: #000000;border:0px solid #bababa;padding-left:5px;font-size:20px;text-align:left;" align="left" >{$COMPANYINVOICENUMBER}</td>
								<td  style="font-size:25px;" align="left">开票人:<input id="openman" name="openman" type="text" style="font-family: Arial, Helvetica, sans-serif;color: #000000;border:1px solid #bababa;padding-left:5px;background-color:#ffffff;font-size:20px;text-align:left;" value="{$OPERATIONMAN}"></td>
							</tr>
						</table>

					</div>


				</form>
			</td>
		</tr>
	</table>

</div>

