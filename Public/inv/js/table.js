
$(document).ready(function() {
	
	var WindowHeight = $(window).height();//获取页面可视区域高度
	var JueSeTableBox01 = document.getElementById("JueSeTableBox01-Js");
	JueSeTableBox01.style.height = ( WindowHeight - 308 ) + "px";
	var JueSeTableBox02 = document.getElementById("JueSeTableBox02-Js");
	JueSeTableBox02.style.height = ( WindowHeight - 308 ) + "px";
	var JiaohuanBox = document.getElementById("JiaohuanBox-Js");
	JiaohuanBox.style.height = ( WindowHeight - 308 ) + "px";
	
	var JiaohuanBoxBH = $("#JiaohuanBoxB-Js").height();//获取页面可视区域高度
	$("#JiaohuanBoxB-Js").css("margin-top",(( WindowHeight - 308 )/2.5));
});

$(function() {
	//改变窗口时窗口大小设置
	$(window).resize(function() {
		var WindowHeight = $(window).height();//获取页面可视区域高度
		var JueSeTableBox01 = document.getElementById("JueSeTableBox01-Js");
		JueSeTableBox01.style.height = ( WindowHeight - 308 ) + "px";
		var JueSeTableBox02 = document.getElementById("JueSeTableBox02-Js");
		JueSeTableBox02.style.height = ( WindowHeight - 308 ) + "px";
		var JiaohuanBox = document.getElementById("JiaohuanBox-Js");
		JiaohuanBox.style.height = ( WindowHeight - 308 ) + "px";
		
		var JiaohuanBoxBH = $("#JiaohuanBoxB-Js").height();//获取页面可视区域高度
		$("#JiaohuanBoxB-Js").css("margin-top",(( WindowHeight - 308 )/2.5));
	});
});