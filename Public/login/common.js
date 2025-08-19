/*
Time:		2010-05-25
Function:	页面公用js文件，可扩展添加公用的js、css等文件
Creater:    刘立方
Editor:		陈耀泉 2010-11-15 16:31:29 重写

*/

//**************加载公用js文件********************************************

if (typeof (loadjquey) == "undefined" || loadjquey == true) {
    //alert(location.pathname);
    //alert(location.pathname.split("/").length);
    var share_url = window.location.pathname;
    var serverpath = "";
    if (share_url.substring(0, 1) == "/")
        share_url = share_url.substring(1, share_url.length);
    for (var num = 0; num < share_url.split("/").length - 2; num++) {
        serverpath = "../" + serverpath;
    }
    //alert(serverpath);   
    var share_JsList = [
        "js/c6-base.js",
        "JHsoft.UI.Lib/js/c6ui/c6ui.system.js",
        "JHSoft.MicroRecord/core/microNote.js"
    ];
    var share_cssList = [
        "css/style_set.css",
        "css/redmond/jquery-ui-1.8.4.custom.css",
        "css/custom-theme/jquery-ui-1.8.4.custom.css",
        "JHsoft.UI.Lib/css/reset.css",
        "css/Style_old_compatible.css"
    ];
    for (i = 0; i < share_cssList.length; i++) {
        document.write("<link type=\"text/css\" href=\"" + serverpath + share_cssList[i] + "\" rel=\"stylesheet\" />");
    }
    for (i = 0; i < share_JsList.length; i++) {
        document.write("<script type=\"text/javascript\" src=\"" + serverpath + share_JsList[i] + "\"></script>");
    }
}
//统一获取事件
function getEvent() {
    if (document.all) return window.event;
    func = getEvent.caller;
    while (func != null) {
        var arg0 = func.arguments[0];
        if (arg0) {
            if ((arg0.constructor == Event || arg0.constructor == MouseEvent)
             || (typeof (arg0) == "object" && arg0.preventDefault && arg0.stopPropagation)) {
                return arg0;
            }
        }
        func = func.caller;
    }
    return null;
}
//字符串转xml对象
var stringtoxml = function(str) {
    var objxml = null;
    if (window.ActiveXObject) {
        objxml = new ActiveXObject("Microsoft.XMLDOM");
        objxml.async = false;
        objxml.loadXML(str);
    }
    else {
        objxml = (new DOMParser()).parseFromString(str, "text/xml");
    }
    return objxml;
}
//xml对象转字符串
var xmltostring = function(dom) {
    if (dom instanceof jQuery) {
        dom = dom[0];
    }
    var str = null;
    if (window.ActiveXObject) {
        str = dom.xml;
    }
    else {
        str = (new XMLSerializer()).serializeToString(dom);
    }
    return str;
}
function jhscrollbar(id) {
    if (!/\((iPhone|iPad|iPod)/i.test(navigator.userAgent)) {
        return false;
    }
    var imagepath = "../images/s_bg.gif";
    if (location.pathname.split("/").length > 4) {
        imagepath = "../../images/s_bg.gif";
    }
    jQuery("#" + id).jscroll({ W: "15px"//设置滚动条宽度
					    , BgUrl: "url(" + imagepath + ")"//设置滚动条背景图片地址
					    , Bg: "right 0 repeat-y"//设置滚动条背景图片position,颜色等
					    , Bar: { Pos: "bottom"//设置滚动条初始化位置在底部
						     , Bd: { Out: "#bcbcbc", Hover: "#ccc"}//设置滚动滚轴边框颜色：鼠标离开(默认)，经过
						     , Bg: { Out: "-45px 0 repeat-y", Hover: "-58px 0 repeat-y", Focus: "-71px 0 repeat-y"}}//设置滚动条滚轴背景：鼠标离开(默认)，经过，点击
					    , Btn: { btn: true//是否显示上下按钮 false为不显示
						     , uBg: { Out: "0 0", Hover: "-15px 0", Focus: "-30px 0"}//设置上按钮背景：鼠标离开(默认)，经过，点击
						     , dBg: { Out: "0 -15px", Hover: "-15px -15px", Focus: "-30px -15px"}}//设置下按钮背景：鼠标离开(默认)，经过，点击
					    , Fn: function() { } //滚动时候触发的方法
    });
}

//刘立方 2011-03-17 判断是否是ipad的浏览器 
function isipad() {
    var ua = navigator.userAgent.toLowerCase();
    var s;
    s = ua.match(/iPad/i);

    if (s == "ipad") {
        return true;
    }
    else {
        return false;
    }
    return false;

}

//判断请求是否来自iPhone
function IsIphone() {
    return /iPhone/i.test(navigator.userAgent);
}

//判断请求是否来自Android
function IsAndroid() {
    return /Android/i.test(navigator.userAgent);
}

//姜少辉 2011-06-01 19:13:09 js获取页面url中的参数，getParam(参数名),后缀_Common以防重名
function getParam_Common(paramName) {
    var param = location.search;
    if (param.lastIndexOf("&") == param.length - 1) {
        param = "?" + base64decode(param.replace("?", ""));
    }
    var svalue = param.match(new RegExp("[\?\&]" + paramName + "=([^\&]*)(\&?)", "i"));
    return svalue ? svalue[1] : svalue;
}

/**
创 建 人：王曙光
创建时间：2011-06-29
功能描述：测算混合字符串的长度并按需求截取
参数说明：str原始字符串，length需要截取的长度(中文按两个字节)
返 回 值：截断后的字符串
*/
function CheckLength(str, length) {

    var index = 0;
    var len = 0;
    for (var i = 0; i < str.length; i++) {
        if (len - 1 >= length)
            return str.substring(0, index - 1);
        if (len >= length)
            return str.substring(0, index);
        if (str.charCodeAt(i) > 255)
            len += 2;
        else
            len++;
        index++;
    }
    return str;
}

/**
创 建 人：王曙光
创建时间：2011-06-09
功能描述：测算混合字符串的长度
参数说明：str原始字符串
返 回 值：要测算的字符串长度
*/
function GetLength(str) {
    var len = 0;
    for (var i = 0; i < str.length; i++) {
        if (str.charCodeAt(i) > 255)
            len += 2;
        else
            len++;
    }
    return len;
}

/*
创 建 人：王曙光 
创建时间：2011-06-29 
功能描述：快速显示用户卡片
调用方法：在需要显示用户卡片的html控件上触发onmouseover调用该方法即可
参数说明：obj控件对象，userCode显示用户ID
注明：用户状态问题待完善
*/
function ShowQuickUserInfo(obj, userCode, deptID, removedBtn) {
    var url = document.location.href;
    var splitUrlCount = url.split("?")[0].split("/").length;
    var realPath = "";
    for (var i = 0; i < splitUrlCount - 5; i++) {
        realPath += "../";
    }

    if (deptID == null) {
        deptID = "";
    }
    if (top.language == null || top.language == "undefined")
        var language = "zh-cn";
    else
        var language = top.language;

    var working = "";
    var out = "";
    var evection = "";
    var leave = "";
    var absence = "";
    var btn_behavior = "工作分析";
    switch (language.toLowerCase()) {
        case "zh-cn":
            working = "工作中";
            out = "外出";
            evection = "出差"
            leave = "休假";
            absence = "缺勤";
            btn_behavior = "工作分析";
            break;
        case "zh-tw":
            working = "工作中";
            out = "外出";
            evection = "出差"
            leave = "休假";
            absence = "缺勤";
            btn_behavior = "工作分析";
            break;
        case "en-us":
            working = "Working";
            out = "Out";
            evection = "Travel"
            leave = "Vacation";
            absence = "Absenteeism";
            btn_behavior = "Job analysis";
            break;
        case "ja":
            working = "工作中";
            out = "外出";
            evection = "出差"
            leave = "休假";
            absence = "缺勤";
            btn_behavior = "工作分析";
            break;
    }

    //需要显示的额外按钮
    var tbtnList = [btn_behavior];
    //是否需要保留寻呼按钮
    var tbtnMsg = true;
    //移除不需要显示的额外按钮

    if (removedBtn) {
        rbtnList = removedBtn.split(",");
        if (rbtnList.length > tbtnList.length)
            tbtnMsg = false;
        for (var i = 0; i < rbtnList.length; i++) {
            tbtnList.splice(Math.round(rbtnList[i]), 1);
        }        
    }

    $.ajax({
        url: realPath + "Control/QuickUserInfo.aspx",
        type: "post",
        contentType: "application/x-www-form-urlencoded; charset=utf-8",
        data: { "usercode": userCode, "deptID": deptID, "datatype": "json" },
        dataType: "json",
        success: function(data) {
            var statusTitle = "";
            var statusImg = "";
            switch (data.UserStatus) {
                case "":
                    statusTitle = "";
                    statusImg = realPath + "JHsoft.UI.Lib/skin/default/images/userinfo/null.png";
                    break;
                case "0":
                    statusTitle = "";
                    statusImg = realPath + "JHsoft.UI.Lib/skin/default/images/userinfo/null.png";
                    break;
                case "1":
                    statusTitle = working;
                    statusImg = realPath + "JHsoft.UI.Lib/skin/default/images/userinfo/zhengchang.png";
                    break;
                case "2":
                    statusTitle = out;
                    statusImg = realPath + "JHsoft.UI.Lib/skin/default/images/userinfo/waichu.png";
                    break;
                case "3":
                    statusTitle = evection;
                    statusImg = realPath + "JHsoft.UI.Lib/skin/default/images/userinfo/evection2.png";
                    break;
                case "4":
                    statusTitle = leave;
                    statusImg = realPath + "JHsoft.UI.Lib/skin/default/images/userinfo/xiujia.png";
                    break;
                case "5":
                    statusTitle = absence;
                    statusImg = realPath + "JHsoft.UI.Lib/skin/default/images/userinfo/queqin.png";
                    break;
            }
            if (data.PhotoURL != "") {
                data.PhotoURL = realPath + data.PhotoURL.substring(3);
            }

            var offset = $(obj).offset();
            $(document).userInfo({
                x: offset.left, //横向
                y: offset.top, //纵向
                name: CheckLength(data.UserName, 20), //姓名
                photo: data.PhotoURL == "" ? realPath + "JHSoft.UI.Lib/images/no_img_80.png" : data.PhotoURL, //照片 
                org: CheckLength(data.DeptName, 22), //机构
                duty: CheckLength(data.PosiName, 22), //职位 
                mobi: CheckLength(data.TeleNum, 22), //手机
                phone: CheckLength(data.UserTel, 22), //电话
                btnList: tbtnList,
                btnMsg: tbtnMsg,
                btnFn: function(id) {
                    //通过参数id判断点击的是哪个按钮
                    if (id == "eddy") {
                        var options = new Object();
                        options.curURL = realPath + "JHSoft.MicroRecord/";
                        options.userCode = userCode;
                        options.userName = data.UserName;
                        options.content = "";
                        $(document).microNote(options);
                    } else if (id == "eddy_0") {
                        top.CreateNewTabWin("../JHSoft.Web.WorkFlat/FlatWorksAttendBehavior.aspx?id=" + userCode);
                    }
                }, //寻呼事件
                hand: "lb", //箭头方向，可选参数:lb rb lt rt l r
                status: statusImg, //状态图标
                statusTxt: statusTitle//状态文字 
            });
        }
    });
}


/*
创 建 人：王曙光
创建时间：2011-6-15
功能描述：显示用户详细信息卡片
调用方式：在需要显示用户卡片的html控件上触发点击事件并且传递用户ID
参数说明：userCode用户ID
注明：状态问题待完善
*/
function ShowUserCard(userCode, deptID) {
    var url = document.location.href;
    var splitUrlCount = url.split("?")[0].split("/").length;
    var realPath = "";
    for (var i = 0; i < splitUrlCount - 5; i++) {
        realPath += "../";
    }
    if (deptID == null)
        deptID = "";
    var obj = new Object();

    if (top.language == null || top.language == "undefined")
        var language = "zh-cn";
    else
        var language = top.language;

    var working = "";
    var out = "";
    var evection = "";
    var leave = "";
    var absence = "";
    var _tel = "电　　话：";
    var _phone = "手　　机：";
    var _mail = "邮　　箱：";
    var _specialty = "岗位职责：";
    var _blog = "博　　客：";
    var _iblog = "的内部博客";
    var _adage = "个人格言：";
    var _ip = "登 录 IP：";
    var _time = "登录时间：";
    var _behavior = "工作分析";
    switch (language.toLowerCase()) {
        case "zh-cn":
            working = "工作中";
            out = "外出";
            evection = "出差"
            leave = "休假";
            absence = "缺勤";
            _tel = "电　　话：";
            _phone = "手　　机：";
            _mail = "邮　　箱：";
            _specialty = "岗位职责：";
            _blog = "博　　客：";
            _iblog = "的内部博客";
            _adage = "个人格言：";
            _ip = "登 录 IP：";
            _time = "登录时间：";
            _behavior = "工作分析";
            break;
        case "zh-tw":
            working = "工作中";
            out = "外出";
            evection = "出差"
            leave = "休假";
            absence = "缺勤";
            _tel = "電　　話：";
            _phone = "手　　機：";
            _mail = "郵　　箱：";
            _specialty = "崗位職責：";
            _blog = "博　　客：";
            _iblog = "的內部博客";
            _adage = "個人格言：";
            _ip = "登 錄 IP：";
            _time = "登錄時間：";
            _behavior = "工作分析";
            break;
        case "en-us":
            working = "Working";
            out = "Out";
            evection = "Travel"
            leave = "Vacation";
            absence = "Absenteeism";
            _tel = "Tel:";
            _phone = "Phone:";
            _mail = "E-mail:";
            _specialty = "Responsibilities:";
            _blog = "Blog:";
            _iblog = "Internal blog";
            _adage = "Personal motto:";
            _ip = "Login IP:";
            _time = "Login time:";
            _behavior = "Job analysis";
            break;
        case "ja":
            working = "工作中";
            out = "外出";
            evection = "出差"
            leave = "休假";
            absence = "缺勤";
            _tel = "电　　话：";
            _phone = "手　　机：";
            _mail = "邮　　箱：";
            _specialty = "岗位职责：";
            _blog = "博　　客：";
            _iblog = "的内部博客";
            _adage = "个人格言：";
            _ip = "登 录 IP：";
            _time = "登录时间：";
            _behavior = "工作分析";
            break;
    }

    $.ajax({
        url: realPath + "Control/QuickUserInfo.aspx",
        type: "post",
        contentType: "application/x-www-form-urlencoded; charset=utf-8",
        data: { "usercode": userCode, "deptID": deptID, "datatype": "json" },
        dataType: "json",
        success: function(data) {
            obj.name = data.UserName;  	    //姓名
            obj.photourl = data.PhotoURL;   //头像
            obj.dept = data.DeptName; 	    //机构 
            obj.posi = data.PosiName; 	    //职位 
            obj.phone = data.TeleNum;       //手机 
            obj.tel = data.UserTel; 		//电话
            obj.gender = data.Gender; 	    //性别
            obj.email = data.Email; 		//电子邮件
            obj.specialty = data.Specialty; //岗位职责
            obj.blog = data.Blog; 		    //博客
            obj.adage = data.Adage; 		//个人格言
            obj.ip = data.LoginIp; 		    //登录IP
            obj.time = data.LoginTime; 	    //登录时间
            obj.status = data.UserStatus;   //用户状态

            obj.statusimg = "";

            if (obj.photourl != "") {
                obj.photourl = realPath + obj.photourl.substring(3);
            }


            var statusTitle = "";
            var statusImg = "";
            switch (obj.status) {
                case "":
                    statusTitle = "";
                    statusImg = "";
                    break;
                case "0":
                    statusTitle = "";
                    statusImg = "";
                    break;
                case "1":
                    statusTitle = "[" + working + "]";
                    statusImg = "<img style='width:16px; height:16px;border:none;' alt='" + working + "' src='" + realPath + "JHsoft.UI.Lib/skin/default/images/userinfo/zhengchang.png' />";
                    break;
                case "2":
                    statusTitle = "[" + out + "]";
                    statusImg = "<img style='width:16px; height:16px;border:none;' alt='" + out + "' src='" + realPath + "JHsoft.UI.Lib/skin/default/images/userinfo/waichu.png' />";
                    break;
                case "3":
                    statusTitle = "[" + evection + "]";
                    statusImg = "<img style='width:16px; height:16px;border:none;' alt='" + evection + "' src='" + realPath + "JHsoft.UI.Lib/skin/default/images/userinfo/evection2.png' />";
                    break;
                case "4":
                    statusTitle = "[" + leave + "]";
                    statusImg = "<img style='width:16px; height:16px;border:none;' alt='" + leave + "' src='" + realPath + "JHsoft.UI.Lib/skin/default/images/userinfo/xiujia.png' />";
                    break;
                case "5":
                    statusTitle = "[" + absence + "]";
                    statusImg = "<img style='width:16px; height:16px;border:none;' alt='" + absence + "' src='" + realPath + "JHsoft.UI.Lib/skin/default/images/userinfo/queqin.png' />";
                    break;
            }

            //李国兴 2011-9-16
            //增加行为分析链接
            //            var cardStr = "<div style='position:absolute;' id=\"personCard\"><div class=\"cardBody\"><div class=\"cardCon\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"structTable\" bgcolor=\"#FFFFFF\"><tr><td colspan=\"2\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"innerTable\"><tr><td class=\"odd\"><img src=\"";
            //            if (obj.photourl == "") {
            //                cardStr += realPath + "JHSoft.UI.Lib/images/no_img_80.png";
            //            }
            //            else {
            //                cardStr += obj.photourl;
            //            }
            //            cardStr += "\" alt=\"\" class=\"personImg\"></td><td class=\"allPadding personDetail\"><b>";
            var cardStr = "<div style='position:absolute;' id=\"personCard\"><div class=\"cardBody\"><div class=\"cardCon\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"structTable\" bgcolor=\"#FFFFFF\"><tr><td colspan=\"2\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"innerTable\"><tr><td class=\"odd\"><table width=\"100%\"><tr><td><img src=\"";
            if (obj.photourl == "") {
                cardStr += realPath + "JHSoft.UI.Lib/images/no_img_80.png";
            }
            else {
                cardStr += obj.photourl;
            }
            cardStr += "\" alt=\"\" class=\"personImg\"></td></tr><tr><td><a style='margin-bottom:2px' href=\"#\" onclick=\"(function(){top.CreateNewTabWin('../JHSoft.Web.WorkFlat/FlatWorksAttendBehavior.aspx?id=" + userCode + "');$(top.document.body).find('#closeIt').click()})()\">" + _behavior + "</a></td></tr></table></td><td class=\"allPadding personDetail\"><b>";
            cardStr += obj.name;
            cardStr += "</b> <span>";
            cardStr += obj.gender;
            cardStr += "</span>";
            cardStr += statusImg;
            cardStr += statusTitle;
            cardStr += "<br />";
            cardStr += obj.dept;
            cardStr += "<br />";
            cardStr += obj.posi;
            cardStr += "</td></tr></table></td></tr><tr><td colspan=\"2\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"innerTable\"><tr><td class=\"odd topPadding\">" + _tel + "</td><td class=\"topPadding\">";
            cardStr += obj.tel;
            cardStr += "</td></tr><tr><td width=\"110\" class=\"odd\">" + _phone + "</td><td>";
            cardStr += obj.phone;
            cardStr += "</td></tr><tr><td class=\"odd botPadding\">" + _mail + "</td><td class=\"botPadding\">";
            cardStr += obj.email;
            cardStr += "</td></tr></table></td></tr><tr><td colspan=\"2\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"innerTable\"><tr><td class=\"odd allPadding\">" + _specialty + "</td><td class=\"allPadding\">";
            cardStr += obj.specialty;
            cardStr += "</td></tr></table></td></tr><tr><td colspan=\"2\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"innerTable\"><tr><td class=\"odd botPadding\">" + _blog + "</td><td>";

            if (obj.blog != "") {
                cardStr += "<a href='" + realPath + "JHSoft.Web.Blog/index.aspx?blogUserCode=";
                cardStr += obj.blog;
                cardStr += "'  target=\"_blank\">";
                cardStr += obj.name + _iblog;
                cardStr += "</a>"
            }

            cardStr += "</td></tr><tr><td class=\"odd botPadding\">" + _adage + "</td><td>";
            cardStr += obj.adage;
            cardStr += "</td></tr></table></td></tr><tr><td colspan=\"2\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"innerTable\"><tr><td class=\"odd topPadding\">" + _ip + "</td><td class=\"topPadding\">";
            cardStr += obj.ip;
            cardStr += "</td></tr><tr><td class=\"odd botPadding\">" + _time + "</td><td class=\"botPadding\">";
            cardStr += obj.time;
            cardStr += "</td></tr></table></td></tr></table></div><img src=\"" + realPath + "JHSoft.UI.Lib/images/blank.gif\" alt=\"close\" id=\"closeIt\"/> </div><div class=\"cardFoot\"> </div></div>";



            if ($(top.document.body).find("#personCard").length > 0) {
                $(top.document.body)[0].removeChild($(top.document.body).find("#personCard")[0]);
            }
            if ($(top.document.body).find("#personCardMark").length > 0) {
                $(top.document.body)[0].removeChild($(top.document.body).find("#personCardMark")[0]);
            }
            $(top.document.body).append("<div id=\"personCardMark\" style=\"position: absolute; background-color: rgb(255, 255, 255); top: 0pt; left: 0pt; z-index: 1000; width: 100%; height: 100%; filter:alpha(opacity=0);-moz-opacity:0;-khtml-opacity: 0;opacity: 0\"></div>");
            $(top.document.body).append(cardStr);
            $(top.document.body).find("#personCardMark").css("z-index", 9998);
            $(top.document.body).find("#personCard").css("z-index", 9999);
            $(top.document.body).find("#personCard").show();

            var halfW = $(top.document.body).find("#personCard")[0].offsetWidth;
            var halfH = $(top.document.body).find("#personCard")[0].offsetHeight;

            if (isipad()) {
                var itop = jQuery("#User" + userCode).offset().top - 70;
                $(top.document.body).find("#personCard").css({ "top": itop + "px", "left": (top.document.body.offsetWidth - halfW) / 2 });
            }
            else {
                $(top.document.body).find("#personCard").css({ "top": (top.document.body.offsetHeight - halfH) / 2 + "px", "left": (top.document.body.offsetWidth - halfW) / 2 });
            }
            $(top.document.body).find("#closeIt").click(function() {
                $(top.document.body)[0].removeChild($(top.document.body).find("#personCard")[0]);
                $(top.document.body)[0].removeChild($(top.document.body).find("#personCardMark")[0]);
            });
            //李国兴 2011-9-16
            //增加行为链接，去掉margin-bottom属性
            $(top.document.body).find(".personImg").css("margin-bottom", "0px");
        }
    });
}

//穿越框架的日历控件全局变量
var Datepicker_value = "";


//2011-07-19 沈繁荣  实现小区域单指滑动功能
/**
功能描述：实现IPAD上的单指滚动
参数描述： 
interval 灵敏度，以像素为单位当手指移动距离超过设定值时发生滚动
xSlip 横向滚动开关，bool类型，控制是否进行横向滚动
ySlip 纵向滚动开关，bool类型，控制是否进行纵向滚动
*/
function PopSlipControl(interval, xSlip, ySlip) {
    this.tem_obj;                   //滑动的对象
    this.startY;                    //滑动开始时的TOP位置
    this.startX;                    //滑动开始时的LEFT位置
    this.interval = interval;       //手指滑动interval距离时执行滑动
    this.xSlip = xSlip;             //是否左右滑动
    this.ySlip = ySlip;             //是否上下滑动

    this.onSlipTouchStart = function(obj) {

        var e = getEvent();
        tem_obj = obj;
        // 当手指刚触摸 Slip 时触发该函数,改为全局触点监控，这样更接近真实情况
        if (e.touches.length != 1)
            return false;

        startY = e.targetTouches[0].clientY;
        startX = e.targetTouches[0].clientX;
        obj.addEventListener('touchmove', this.onSlipTouchMove);
        obj.addEventListener('touchend', this.onSlipTouchEnd);
    };
    this.onSlipTouchMove = function(e) {

        if (!tem_obj)
            return;
        var sSlip = 2;
        // 如果是多点触摸的话就不再处理,全局触点监控        
        if (e.touches.length != 1)
            return false;
        var xMove = xSlip;
        var yMove = ySlip;


        //当滚动超出控件范围时，将滚动效果交给浏览器
        if (ySlip) {
            var deltaY = e.targetTouches[0].clientY - startY;
            if (tem_obj.scrollTop < tem_obj.scrollHeight - tem_obj.clientHeight - sSlip && tem_obj.scrollTop > sSlip) {
                e.preventDefault();
            }
            else if (tem_obj.scrollTop <= sSlip) {
                if (deltaY < 0) {
                    e.preventDefault();
                }
                else {
                    yMove = false;
                }
            }
            else if (tem_obj.scrollTop >= tem_obj.scrollHeight - tem_obj.clientHeight - sSlip) {
                if (deltaY > 0) {
                    e.preventDefault();
                }
                else {
                    yMove = false;
                }
            }
        }
        if (xSlip) {
            var deltaX = e.targetTouches[0].clientX - startX;
            if (tem_obj.scrollLeft < tem_obj.scrollWidth - tem_obj.clientWidth - sSlip && tem_obj.scrollLeft > sSlip) {
                e.preventDefault();
            }
            else if (tem_obj.scrollLeft <= sSlip) {

                if (deltaX < 0) {
                    e.preventDefault();
                }
                else {
                    xMove = false;
                }
            }
            else if (tem_obj.scrollLeft >= tem_obj.scrollWidth - tem_obj.clientWidth - sSlip) {
                if (deltaX > 0) {
                    e.preventDefault();
                }
                else {
                    xMove = false;
                }
            }
        }

        if (xMove || yMove)
            e.preventDefault();


        //上下滑动
        if (ySlip) {

            var itop = tem_obj.scrollTop;
            if (itop == null || itop == "")
                itop = 0;
            // 计算手指滑动的距离，如果超过我们设的 INTERVAL 的值时滑动
            var deltaY = e.targetTouches[0].clientY - startY;

            if (Math.abs(deltaY) > interval) {
                if (yMove)
                    tem_obj.scrollTop = itop - deltaY;
            }
            startY = e.targetTouches[0].clientY;
        }
        //左右滑动
        if (xSlip) {
            var ileft = tem_obj.scrollLeft;
            if (ileft == null || ileft == "")
                ileft = 0;
            var deltaX = e.targetTouches[0].clientX - startX;

            if (Math.abs(deltaX) > interval) {
                if (xMove)
                    tem_obj.scrollLeft = ileft - deltaX;
            }
            startX = e.targetTouches[0].clientX;
        }
    };
    this.onSlipTouchEnd = function(e) {
        // 阻止浏览器的默认行为 (scroll, zoom)
        e.preventDefault();
        if (tem_obj) {
            $(tem_obj).unbind("touchmove", this.onSlipTouchMove);
            $(tem_obj).unbind("touchend", this.onSlipTouchEnd);
            tem_obj = null;
        }
    };
}