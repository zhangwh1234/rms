$.extend({
	ocAlert : function(type, txt, option) {
		//window.wxc.xcConfirm(txt,window.wxc.xcConfirm.typeEnum.info);
		var settings = {
			onOk : function() {
			},
			onCancel : function() {
			}
		}

		$.extend(settings, option);

		if (type == "error") {
			window.wxc.xcConfirm(txt, window.wxc.xcConfirm.typeEnum.error,
					settings);
		} else if (type == "success") {
			window.wxc.xcConfirm(txt, window.wxc.xcConfirm.typeEnum.success,
					settings);
		} else if (type == "confirm") {
			window.wxc.xcConfirm(txt, window.wxc.xcConfirm.typeEnum.confirm,
					settings);
		}else if(type=="info"){
			window.wxc.xcConfirm(txt, window.wxc.xcConfirm.typeEnum.info,
					settings);
		}
	}
});
(jQuery);