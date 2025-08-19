/**
 * 演示程序当前的 “注册/登录” 等操作，是基于 “本地存储” 完成的
 * 当您要参考这个演示程序进行相关 app 的开发时，
 * 请注意将相关方法调整成 “基于服务端Service” 的实现。
 **/
// ajax 对象

//var server_url = "http://192.168.31.170:18080/EPMServer";
//var server_url = "http://192.168.1.2:18080/EPMServer";
var server_url = "http://180.209.64.82:8080/electric-power/app";
var server_url_235 = "http://120.26.215.235:18080/EPMServer";
//var server_url = "http://192.168.1.105:18080/EPMServer";
function ajaxObject() {
    var xmlHttp;
    try {
        // Firefox, Opera 8.0+, Safari
        xmlHttp = new XMLHttpRequest();
        } 
    catch (e) {
        // Internet Explorer
        try {
                xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
            try {
                xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {
                alert("您的浏览器不支持AJAX！");
                return false;
            }
        }
    }
    return xmlHttp;
}
 
// ajax post请求：
function ajaxPost ( url , data) {// , fnSucceed , fnFail , fnLoading 
    var ajax = ajaxObject();
    ajax.open( "post" , url , true );
    ajax.setRequestHeader( "Content-Type" , "application/x-www-form-urlencoded" );
    ajax.onreadystatechange = function () {
        if( ajax.readyState == 4 ) {
            if( ajax.status == 200 ) {
                /*fnSucceed( ajax.responseText );*/
                alert(ajax.responseText);
            }
            else {
                /*fnFail( "HTTP请求错误！错误码："+ajax.status );*/
                alert("HTTP请求错误！错误码："+ajax.status );
            }
        }
        else {
            /*fnLoading();*/
        }
    }
    ajax.send( data );
 
}

function httpGet(theUrl){
    var xmlHttp = null;
 
    xmlHttp = new XMLHttpRequest();
    xmlHttp.open( "GET", theUrl, false );
    xmlHttp.send( null );
    return xmlHttp.responseText;
}

function httpPost(theUrl, body){
    var xmlHttp = null;
    xmlHttp = new XMLHttpRequest();
    //xmlHttp.timeout = 3000;
    xmlHttp.open( "POST", theUrl, false );
    xmlHttp.setRequestHeader("Content-Type", "application/json"); 
    xmlHttp.send( body );
    return xmlHttp.responseText;
}

// 异步post请求
function httpPostAsync(theUrl, body, processResponse){
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.timeout = 5000;
    // 设置处理响应的回调函数  
    xmlHttp.onreadystatechange = processResponse;  
    xmlHttp.open( "POST", theUrl, true );
    xmlHttp.setRequestHeader("Content-Type", "application/json"); 
    xmlHttp.send( body );
}

(function($, owner) {
	/**
	 * 用户登录
	 **/
	owner.login = function(loginInfo, callback) {
		
		callback = callback || $.noop;
		loginInfo = loginInfo || {};
		loginInfo.username = loginInfo.username || '';
		loginInfo.password = loginInfo.password || '';
		if (loginInfo.username.length == 0) {
			return callback('请填写账号');
		}
		if (loginInfo.password.length == 0) {
			return callback('请填写密码');
		}
		
		return owner.createState(username, callback);
		/*
		var users = JSON.parse(localStorage.getItem('$users') || '[]');
		var authed = users.some(function(user) {
			return loginInfo.username == user.username && loginInfo.password == user.password;
		});
		*/
		//发送http请求
		var url = server_url+"/login";
		//alert(loginInfo.username);
		//alert(loginInfo.password);
		
		var params = {
			userName: loginInfo.username,
			userPwd: loginInfo.password
		}
		
		var loginInfo1 = {
			appKey: "app.android",
			params: params
		};
		
		alert(111);
		return owner.createState(loginInfo.username, callback);
		
		var body = JSON.stringify(loginInfo1);
		//alert(body);
		var responseText = httpPost(url,body);
		
		//alert(responseText);
		
		var loginRes = JSON.parse(responseText);
		
		//alert(loginRes.retCode);
		//alert(loginRes.retMessage);
		
		
		if("0000"!=loginRes.code){
			return callback("登录失败："+loginRes.msg);
		}
		
		var username = loginInfo.username;
		var roleid = loginRes.roleId;
		
		//alert("获取到用户名： "+loginRes.userInfo.username);
		
		/*var responseText=httpGet(url);
		var jobj=eval(responseText);
		console.log(jobj);*/
		//alert(responseText);alert(jobj);
		//alert(responseText[0]);alert(jobj[0]);
		//authed		
		/*if(jobj[0]=="帐号不存在")
		{
			return callback('帐号不存在');
		}
		else if(jobj[0]=="密码错误")
		{
			return callback('密码错误');
		}
		else
		{
			authed=jobj[0];
		}*/
		localStorage.setItem("username",username);
		localStorage.setItem("roleid",roleid);
		
		
                    
		return owner.createState(username, callback);
	};

	owner.createState = function(name, callback) {		
		var state = owner.getState();
		state.account = name;
		state.token = "token123456789";
		owner.setState(state); 
		return callback();
	};
	
	/**
	 * 获取所有电站列表
	 **/
	owner.getAllStationInfos = function() {
		//发送http请求
		var url = server_url+"/stationLists";
		
		var params = {
			orgCode: "320113",
		}
		
		var req = {
			appKey: "app.android",
			params: params
		};
		
		var body = JSON.stringify(req);
		//alert(body);
		var responseText = httpPost(url,body);
		
		//alert(responseText);
		
		var res = JSON.parse(responseText);
		
		
		if("0000"!=res.code){
			plus.nativeUI.toast(res.msg);
			return;
		}
		return res.data;
	};
	
		/**
	 * 获取所有区域信息
	 **/
	owner.getAllStationInfos_235 = function() {
		//发送http请求
		var url = server_url_235+"/api/getAllStationInfos";
		var responseText = httpGet(url);
		var res = JSON.parse(responseText);
		
		if("0"!=res.retCode){
			plus.nativeUI.toast(res.retMessage);
			return;
		}
		return res.stationAreaInfoList;
	};
	
	owner.getNowFormatDate = function() {
	    var date = new Date();
	    var seperator1 = "-";
	    var seperator2 = ":";
	    var month = date.getMonth() + 1;
	    var strDate = date.getDate();
	    if (month >= 1 && month <= 9) {
	        month = "0" + month;
	    }
	    if (strDate >= 0 && strDate <= 9) {
	        strDate = "0" + strDate;
	    }
	    var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate;
	    return currentdate;
	};
	
	/**
	 * 获取所有区域列表
	 **/
	owner.getAllStationArea = function() {
		//发送http请求
		var url = server_url_235+"/api/getAllStationArea";
		var responseText = httpGet(url);
		var res = JSON.parse(responseText);
		
		if("0"!=res.retCode){
			plus.nativeUI.toast(res.retMessage);
			return;
		}
		//alert("获取到区域： "+res.stationAreas);
		return res.stationAreas;
	};
	
	/**
	 * 按照区域查询电站信息
	 **/
	owner.getStationInfoByStationArea = function(stationAreaVal) {
		
		var getStationInfoByStationAreaReq = {
							stationArea: stationAreaVal,
						};
						
		var body = JSON.stringify(getStationInfoByStationAreaReq);
		
		//发送http请求
		var url = server_url_235+"/api/getStationInfoByStationArea";
		var responseText = httpPost(url,body);
		var res = JSON.parse(responseText);
		
		if("0"!=res.retCode){
			plus.nativeUI.toast(res.retMessage);
			return;
		}
		//alert("获取到区域： "+res.stationAreas);
		return res.stationInfoList;
	};
	
	/**
	 * 获取发电概况
	 **/
	owner.getPowerRecordsByDatetime = function(datetime, stationtag) {
		
		var getPowerRecordsByDatetimeReq = {
							datetime: datetime,
							stationtag: stationtag
						};
						
		var body = JSON.stringify(getPowerRecordsByDatetimeReq);
		
		//发送http请求
		var url = server_url_235+"/api/getPowerRecordsByDatetime";
		var responseText = httpPost(url,body);
		var resObj = JSON.parse(responseText);
		
		if("0"!=resObj.retCode){
			plus.nativeUI.toast(resObj.retMessage);
			return;
		}
		return resObj;
	};
	
	/**
	 * 获取用电概况
	 **/
	owner.getConsumptionByDatetime = function(datetime, stationtag) {
		
		var getConsumptionByDatetimeReq = {
							datetime: datetime,
							stationtag: stationtag
						};
						
		var body = JSON.stringify(getConsumptionByDatetimeReq);
		
		//发送http请求
		var url = server_url_235+"/api/getConsumptionByDatetime";
		var responseText = httpPost(url,body);
		var resObj = JSON.parse(responseText);
		
		if("0"!=resObj.retCode){
			plus.nativeUI.toast(resObj.retMessage);
			return;
		}
		return resObj;
	};
	
	/**
	 * 获取用电概况
	 **/
	owner.getQXZ = function(datetime, stationtag) {
		
		var getQxzReq = {
							datetime: datetime,
							stationtag: stationtag
						};
						
		var body = JSON.stringify(getQxzReq);
		
		//发送http请求
		var url = server_url_235+"/api/getQXZ";
		var responseText = httpPost(url,body);
		var resObj = JSON.parse(responseText);
		
		if("0"!=resObj.retCode){
			plus.nativeUI.toast(resObj.retMessage);
			return;
		}
		return resObj;
	};
	
	/**
	 * 根据电站获取功率
	 **/
	owner.getGongLvByStationTag = function(datetime, stationtag) {
		
		var commonReq = {
							datetime: datetime,
							stationtag: stationtag
						};
						
		var body = JSON.stringify(commonReq);
		
		//发送http请求
		var url = server_url_235+"/api/getGongLvByStationTag";
		var responseText = httpPost(url,body);
		var resObj = JSON.parse(responseText);
		
		if("0"!=resObj.retCode){
			plus.nativeUI.toast(resObj.retMessage);
			return;
		}
		return resObj;
	};
	
	/**
	 * 根据电站类型（风力、光伏）获取功率
	 **/
	owner.getGongLvByType = function(datetime, type) {
		
		var getGongLvByTypeReq = {
							datetime: datetime,
							type: type
						};
						
		var body = JSON.stringify(getGongLvByTypeReq);
		
		//发送http请求
		var url = server_url_235+"/api/getGongLvByType";
		var responseText = httpPost(url,body);
		var resObj = JSON.parse(responseText);
		
		if("0"!=resObj.retCode){
			plus.nativeUI.toast(resObj.retMessage);
			return;
		}
		return resObj;
	};
	
	/**
	 * 获取电站详情
	 **/
	owner.stationDetail = function(stationId,callback) {
		callback = callback || $.noop;
		//发送http请求
		var url = server_url+"/stationDetail";
		
		var params = {
			stationId: stationId,
		}
		
		var req = {
			appKey: "app.android",
			params: params
		};
		
		var body = JSON.stringify(req);
		//alert(body);
		httpPostAsync(url,body,callback);
		//alert(responseText);
	};
	
	/**
	 * 获取电站逆变器列表
	 **/
	owner.inverterLists = function(stationId,type,callback) {
		//发送http请求
		var url = server_url+"/inverterLists";
		
		var params = {
			stationId: stationId,
			type:type
		}
		
		var req = {
			appKey: "app.android",
			params: params
		};
		
		var body = JSON.stringify(req);
		//alert(body);
		httpPostAsync(url,body,callback);
		
		//alert(responseText);
		
		/*
		var res = JSON.parse(responseText);
		
		
		if("0000"!=res.code){
			return;
		}
		return res.data;
		*/
	};
	
	owner.lightEchartList = function(orgCode, stationId,ivtId,dateTime) {
		//发送http请求
		var url = server_url+"/lightEchartList";
		
		var params = {
			orgCode:orgCode,
			stationId: stationId,
			ivtId:ivtId,
			dateTime:dateTime
		}
		
		var req = {
			appKey: "app.android",
			params: params
		};
		
		var body = JSON.stringify(req);
		//alert(body);
		var responseText = httpPost(url,body);
		
		//alert(responseText);
		
		var res = JSON.parse(responseText);
		
		
		if("0000"!=res.code){
			return;
		}
		return res;
	};
	
	/**
	 * 获取用电概况
	 **/
	owner.getStationId = function() {
		var stationId = "2";
		var stationIdLocal = localStorage.getItem("stationId");
				
		if(stationIdLocal != null &&stationIdLocal.length>0){
			stationId = stationIdLocal;
		}
		return stationId;
	};
	
	owner.windEchartList = function(orgCode, stationId,ivtId,dateTime) {
		//发送http请求
		var url = server_url+"/windEchartList";
		
		var params = {
			orgCode:orgCode,
			stationId: stationId,
			ivtId:ivtId,
			dateTime:dateTime
		}
		
		var req = {
			appKey: "app.android",
			params: params
		};
		
		var body = JSON.stringify(req);
		//alert(body);
		var responseText = httpPost(url,body);
		
		//alert(responseText);
		
		var res = JSON.parse(responseText);
		
		
		if("0000"!=res.code){
			return;
		}
		return res;
	};
	
	owner.vLightEchartList = function(orgCode, stationId,isFilter,dateTime) {
		//发送http请求
		var url = server_url+"/vLightEchartList";
		
		var params = {
			orgCode:orgCode,
			stationId: stationId,
			isFilter:isFilter,
			dateTime:dateTime
		}
		
		var req = {
			appKey: "app.android",
			params: params
		};
		
		var body = JSON.stringify(req);
		//alert(body);
		var responseText = httpPost(url,body);
		
		//alert(responseText);
		
		var res = JSON.parse(responseText);
		
		
		if("0000"!=res.code){
			return;
		}
		return res;
	};
	
	owner.vLoadEchartList = function(orgCode, unitId,dateTime) {
		//发送http请求
		var url = server_url+"/vLoadEchartList";
		
		var params = {
			orgCode:orgCode,
			unitId: unitId,
			dateTime:dateTime
		}
		
		var req = {
			appKey: "app.android",
			params: params
		};
		
		var body = JSON.stringify(req);
		//alert(body);
		var responseText = httpPost(url,body);
		
		//alert(responseText);
		
		var res = JSON.parse(responseText);
		
		
		if("0000"!=res.code){
			return;
		}
		return res;
	};
	
	owner.loadUnitLists = function(orgCode) {
		//发送http请求
		var url = server_url+"/loadUnitLists";
		
		var params = {
			orgCode:orgCode,
		}
		
		var req = {
			appKey: "app.android",
			params: params
		};
		
		var body = JSON.stringify(req);
		//alert(body);
		var responseText = httpPost(url,body);
		
		//alert(responseText);
		
		var res = JSON.parse(responseText);
		
		
		if("0000"!=res.code){
			return;
		}
		return res.data;
	};

	owner.getAllFaultaccept = function(callback) {
		callback = callback || $.noop;
		var url = server_url_235 + "/api/getAllFaultaccept";
		httpPostAsync(url,null,callback);
	};
	
	
	/**
	 * 新用户注册
	 **/
	owner.reg = function(regInfo, callback) {
		callback = callback || $.noop;
		regInfo = regInfo || {};
		regInfo.account = regInfo.account || '';
		regInfo.password = regInfo.password || '';
		if (regInfo.account.length < 5) {
			return callback('用户名最短需要 5 个字符');
		}
		if (regInfo.password.length < 6) {
			return callback('密码最短需要 6 个字符');
		}
		/*
		if (!checkEmail(regInfo.email)) {
			return callback('邮箱地址不合法');
		}*/
		var users = JSON.parse(localStorage.getItem('$users') || '[]');
		users.push(regInfo);
		localStorage.setItem('$users', JSON.stringify(users));
		return callback();
	};

	/**
	 * 获取当前状态
	 **/
	owner.getState = function() {
		var stateText = localStorage.getItem('$state') || "{}";
		return JSON.parse(stateText);
	};

	/**
	 * 设置当前状态
	 **/
	owner.setState = function(state) {
		state = state || {};
		localStorage.setItem('$state', JSON.stringify(state));
		//var settings = owner.getSettings();
		//settings.gestures = '';
		//owner.setSettings(settings);
	};

	var checkEmail = function(email) {
		email = email || '';
		return (email.length > 3 && email.indexOf('@') > -1);
	};

	/**
	 * 找回密码
	 **/
	owner.forgetPassword = function(email, callback) {
		callback = callback || $.noop;
		if (!checkEmail(email)) {
			return callback('邮箱地址不合法');
		}
		return callback(null, '新的随机密码已经发送到您的邮箱，请查收邮件。');
	};

	/**
	 * 获取应用本地配置
	 **/
	owner.setSettings = function(settings) {
		settings = settings || {};
		localStorage.setItem('$settings', JSON.stringify(settings));
	}

	/**
	 * 设置应用本地配置
	 **/
	owner.getSettings = function() {
			var settingsText = localStorage.getItem('$settings') || "{}";
			return JSON.parse(settingsText);
		}
		/**
		 * 获取本地是否安装客户端
		 **/
	owner.isInstalled = function(id) {
		if (id === 'qihoo' && mui.os.plus) {
			return true;
		}
		if (mui.os.android) {
			var main = plus.android.runtimeMainActivity();
			var packageManager = main.getPackageManager();
			var PackageManager = plus.android.importClass(packageManager)
			var packageName = {
				"qq": "com.tencent.mobileqq",
				"weixin": "com.tencent.mm",
				"sinaweibo": "com.sina.weibo"
			}
			try {
				return packageManager.getPackageInfo(packageName[id], PackageManager.GET_ACTIVITIES);
			} catch (e) {}
		} else {
			switch (id) {
				case "qq":
					var TencentOAuth = plus.ios.import("TencentOAuth");
					return TencentOAuth.iphoneQQInstalled();
				case "weixin":
					var WXApi = plus.ios.import("WXApi");
					return WXApi.isWXAppInstalled()
				case "sinaweibo":
					var SinaAPI = plus.ios.import("WeiboSDK");
					return SinaAPI.isWeiboAppInstalled()
				default:
					break;
			}
		}
	}
}(mui, window.app = {}));


Date.prototype.format = function(fmt) { 
     var o = { 
        "M+" : this.getMonth()+1,                 //月份 
        "d+" : this.getDate(),                    //日 
        "h+" : this.getHours(),                   //小时 
        "m+" : this.getMinutes(),                 //分 
        "s+" : this.getSeconds(),                 //秒 
        "q+" : Math.floor((this.getMonth()+3)/3), //季度 
        "S"  : this.getMilliseconds()             //毫秒 
    }; 
    if(/(y+)/.test(fmt)) {
            fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length)); 
    }
     for(var k in o) {
        if(new RegExp("("+ k +")").test(fmt)){
             fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
         }
     }
    return fmt; 
}