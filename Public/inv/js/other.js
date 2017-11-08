$(document).ready(function() {
	
	var WindowHeight = $(window).height();//获取页面可视区域高度
	var WindowWidth = $(window).width();//获取页面可视区域宽度
	var SecBoxH = $("#SecBox-Js").height();//获取搜索区域高度
	var TabBoxH = $("#TabBox-Js").height();//获取tab页高度
	var PagingBoxDivH = $("#PagingBoxDiv-Js").height();//获取分页高度
	var ListTableClassH = $("#TableDiv-js table").height();//获取滚动表格高度
	var TableDivH = WindowHeight - SecBoxH - TabBoxH - PagingBoxDivH - 130;//获取滚动表格可最大的高	
	var TableDiv = document.getElementById("TableDiv-js");
	
	if( ListTableClassH > TableDivH ){
		TableDiv.style.height = ( WindowHeight - SecBoxH - TabBoxH - PagingBoxDivH - 130 ) + "px";
	}
	else{
		TableDiv.style.height = ListTableClassH + "px";
	};
	if( WindowWidth <= 1024){
		$(".Col01-Js").width(110)
		$(".Col02-Js").width(200)
		$(".Col03-Js").width(150)
		$(".Col04-Js").width(500)
		$(".Col05-Js").width(110)
		$(".Col06-Js").width(200)
	}
	else if( WindowWidth <= 1280){
		$(".Col01-Js").width(150)
		$(".Col02-Js").width(250)
		$(".Col03-Js").width(250)
		$(".Col04-Js").width(500)
		$(".Col05-Js").width(140)
		$(".Col06-Js").width(250)
	}
	else if( WindowWidth <= 1366){
		$(".Col01-Js").width(150)
		$(".Col02-Js").width(300)
		$(".Col03-Js").width(300)
		$(".Col04-Js").width(500)
		$(".Col05-Js").width(150)
		$(".Col06-Js").width(300)
	}
	else if( WindowWidth <= 1440){
		$(".Col01-Js").width(150)
		$(".Col02-Js").width(400)
		$(".Col03-Js").width(300)
		$(".Col04-Js").width(600)
		$(".Col05-Js").width(150)
		$(".Col06-Js").width(350)
	}
	else{
		$(".Col01-Js").width(200)
		$(".Col02-Js").width(400)
		$(".Col03-Js").width(300)
		$(".Col04-Js").width(600)
		$(".Col05-Js").width(180)
		$(".Col06-Js").width(400)
	}
});

$(function() {
	//改变窗口时窗口大小设置
	$(window).resize(function() {
		var WindowHeight = $(window).height();//获取页面可视区域高度
		var WindowWidth = $(window).width();//获取页面可视区域宽度
		var SecBoxH = $("#SecBox-Js").height();//获取搜索区域高度
		var TabBoxH = $("#TabBox-Js").height();//获取tab页高度
		var PagingBoxDivH = $("#PagingBoxDiv-Js").height();//获取分页高度
		var ListTableClassH = $("#TableDiv-js table").height();//获取滚动表格高度
		var TableDivH = WindowHeight - SecBoxH - TabBoxH - PagingBoxDivH - 130;//获取滚动表格可最大的高
		var TableDiv = document.getElementById("TableDiv-js");
		if( ListTableClassH > TableDivH ){
			TableDiv.style.height = ( WindowHeight - SecBoxH - TabBoxH - PagingBoxDivH - 130 ) + "px";
		}
		else{
			TableDiv.style.height = ListTableClassH + "px";
		};
		if( WindowWidth <= 1024){
			$(".Col01-Js").width(110)
			$(".Col02-Js").width(200)
			$(".Col03-Js").width(150)
			$(".Col04-Js").width(500)
			$(".Col05-Js").width(110)
			$(".Col06-Js").width(200)
		}
		else if( WindowWidth <= 1280){
			$(".Col01-Js").width(150)
			$(".Col02-Js").width(250)
			$(".Col03-Js").width(250)
			$(".Col04-Js").width(500)
			$(".Col05-Js").width(140)
			$(".Col06-Js").width(250)
		}
		else if( WindowWidth <= 1366){
			$(".Col01-Js").width(150)
			$(".Col02-Js").width(300)
			$(".Col03-Js").width(300)
			$(".Col04-Js").width(500)
			$(".Col05-Js").width(150)
			$(".Col06-Js").width(300)
		}
		else if( WindowWidth <= 1440){
			$(".Col01-Js").width(150)
			$(".Col02-Js").width(400)
			$(".Col03-Js").width(300)
			$(".Col04-Js").width(600)
			$(".Col05-Js").width(150)
			$(".Col06-Js").width(350)
		}
		else{
			$(".Col01-Js").width(200)
			$(".Col02-Js").width(400)
			$(".Col03-Js").width(300)
			$(".Col04-Js").width(600)
			$(".Col05-Js").width(180)
			$(".Col06-Js").width(400)
		}
	});
});