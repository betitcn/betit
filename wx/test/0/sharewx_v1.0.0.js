(function (_aoWin) {
    var _sHost = 'https://open.weixin.qq.com';
	var _s = null,
		_t = 0,
		_closeS = null;
	
	var _oDlgEl, _oDivEl, _oMaskEl;

	
	
	function _bodyScroll(_asType) {
		if (_asType == "scrollTop" && typeof document.pageYOffset != "undefined" ) {
			return document.pageYOffset;
		} else {
			return document.documentElement[_asType] || document.body[_asType];
		}
	}
	function _screenCenter() {
		var _nWidth = _oDivEl.clientWidth || _oDivEl.offsetWidth,
			_nHeight = _oDivEl.clientHeight || _oDivEl.offsetHeight;
			
		_oDivEl.style.left = (_bodyScroll("clientWidth") - _nWidth)/2 + "px";
		_oDivEl.style.top = (_bodyScroll("clientHeight") - _nHeight - 100)/2
			+ _bodyScroll("scrollTop") + "px";
		
		_oMaskEl.style.height = _bodyScroll("scrollHeight") + "px";	
		_oMaskEl.style.width = _bodyScroll("clientWidth") + "px";	
	}
	function _attachEvent(_asEvent, _fCallback, _abAttach, _aoEl) {
		if (_aoWin.addEventListener) {
			(_aoEl || _aoWin)[_abAttach ? "addEventListener" : "removeEventListener"](_asEvent, _fCallback);
		} else {
			(_aoEl || _aoWin)[_abAttach ? "attachEvent" : "detachEvent"]("on"+_asEvent, _fCallback);
		}
    }
	function _loadJs(_asUrl) {
		try{
			var x=document.createElement('SCRIPT');
			x.type='text/javascript';
			x.src=_asUrl;
			x.charset='utf-8';
			document.getElementsByTagName('head')[0].appendChild(x);
			return x;
		}catch(e){}	
	}
	function _rmJs(_aoEl) {
		try{document.getElementsByTagName("head")[0].removeChild(_aoEl);}catch(e){}
	}
	function preventDefault(_aoEvent) {
		if (_aoEvent){
			if (_aoEvent.preventDefault) {
				_aoEvent.preventDefault();
			} else {
				_aoEvent.returnValue = false;
			}
		}
		return _aoEvent;
	}
	function stopPropagation(_aoEvent) {
		if (_aoEvent) { 
			if (_aoEvent.stopPropagation) {
				_aoEvent.stopPropagation();
			} else {
				_aoEvent.cancelBubble = true;
			 }
		}
		return _aoEvent;
	}	

	function _popup() {
		if (_oDlgEl) {return;}
		
		_oDlgEl = document.createElement("div");
		_oDlgEl.style.zIndex = "11000001";	
		_oDlgEl.innerHTML= ['<div style="position:absolute;top:0px;left:0px;width:100%;height:100%;background-color:#000;filter:alpha(opacity=50);opacity:0.5;-moz-opacity:0.5;-khtml-opacity:0.5;z-index:1000000" id="_weixin_share_mask_"></div>',
			'<div style="position:absolute;z-index:1000001;padding:0px;width:220px;height:240px;border:1px solid #333;backgroud-color:#fff;overflow:hidden;" id="_weixin_share_div_">',
				'<div style="height:20px;background-color:#333;color:#fff;padding:3px 0px;font-size:14px;line-height:20px;width:100%;text-align:center;">扫描二维码分享到微信朋友圈</div>',
				'<div style="height:220px;width:220px;background-color:#fff;" id="_weixin_share_img_"></div>',
			'</div>'
		].join("");
		
		document.body.appendChild(_oDlgEl);
		_oMaskEl = document.getElementById("_weixin_share_mask_");
		_oDivEl = document.getElementById("_weixin_share_div_");
		
		_screenCenter();
		_attachEvent("resize", _screenCenter, true);
		_attachEvent("scroll", _screenCenter, true);
		_attachEvent("click", _cancel, true, _oMaskEl);
	}
	
	function _cancel() {
		clearInterval(_t);_t = 0;
		_attachEvent("resize", _screenCenter, false);
		_attachEvent("scroll", _screenCenter, false);
		_attachEvent("click", _cancel, true, _oMaskEl);
		document.body.removeChild(_oDlgEl);
		_oDlgEl = _oDivEl = _oMaskEl = null;
	}

	

	//opt格式： {title:文章标题, url:文章链接, imgsrc:图片链接, appid}
	_aoWin.sharewx = function (_aoOpt) {
		if (!_aoOpt) return false;
		
		
		
		
		if (!/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent))
		{
			_popup();
		}

		_s = _loadJs(_sHost + '/qr/set/?title=' + (_aoOpt.title || document.title) + '&url=' + encodeURIComponent(_aoOpt.url || location.href) + '&img=' + encodeURIComponent(_aoOpt.imgsrc || "") + '&appid=' + (_aoOpt.appid || '') + "&r=" + Math.random());
		
		if (_aoWin.event){stopPropagation(_aoWin.event);preventDefault(_aoWin.event);}
		
		return false;
	};
	
	_aoWin.showWxBox = function(_uid) {

		if (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent))
		{
			window.location.href = "http://open.weixin.qq.com/qr/"+_uid+"#wechat_redirect";
		}else
		{
			clearInterval(_t);
		
			_t = setInterval(function() {
				_closeS = _loadJs(_sHost + '/qr/close/?uuid=' + _uid + "&r=" + Math.random());
			}, 2000);
			
			document.getElementById("_weixin_share_img_").innerHTML = '<img src="' + _sHost +'/qr/get/' + _uid + '/" width="220" height="220" alt="微信二维码"/>'; 
			
			_rmJs(_s);
		}
		
	}
	
	_aoWin.hideWxBox = function(flag) {
		if (flag){
			_cancel();
		}
		_rmJs(_closeS);
	}
})(window);
/*  |xGv00|5d118191ea62b30e939df97079b498d2 */