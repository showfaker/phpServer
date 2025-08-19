//var server_url = "http://192.168.31.171:38080/OA/api/";
//var server_context = "http://192.168.0.108:38080/OA/";
var server_context = "http://192.168.0.41:80/icp/";
//var server_url = "http://192.168.31.171:38080/icp/api/";
var loglevel = 0;
var server_url = server_context+"api/";

var Ajax = {
	get: function(url, fn) {
		url = server_url+url;
		var obj = new XMLHttpRequest(); // XMLHttpRequest对象用于在后台与服务器交换数据          
		obj.open('GET', url, true);
		obj.onreadystatechange = function() {
			if(obj.readyState == 4 && (obj.status == 200 || obj.status == 304)) { // readyState == 4说明请求已完成
				//alert(obj.responseText);
				fn.call(this, JSON.parse(obj.responseText)); //从服务器获得数据
			}
		};
		obj.send();
	},
	post: function(url, data, fn) { // data应为'a=a1&b=b1'这种字符串格式，在jq里如果data为对象会自动将对象转成这种字符串格式
		url = server_url+url;
		var obj = new XMLHttpRequest();
		obj.open("POST", url, true);
		obj.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); // 添加http头，发送信息至服务器时内容编码类型
		obj.onreadystatechange = function() {
			if(obj.readyState == 4 && (obj.status == 200 || obj.status == 304)) { // 304未修改
				fn.call(this, JSON.parse(obj.responseText));
			}
		};
		obj.send(data);
	},
	postjson: function(url, data, fn) { // data应为json格式
		url = server_url+url;
		var datastr = JSON.stringify(data);
		
		if(loglevel==1){
			alert("url["+url+"] data["+datastr+"]");
		}
		
		var obj = new XMLHttpRequest();
		obj.timeout = 15000;
		obj.open("POST", url, true);
		obj.setRequestHeader("Content-type", "application/json"); // 添加http头，发送信息至服务器时内容编码类型
		obj.onreadystatechange = function() {
			if(obj.readyState == 4 && (obj.status == 200 || obj.status == 304)) { // 304未修改
				if(loglevel==1){
					alert(obj.responseText);
				}
				fn.call(obj, JSON.parse(obj.responseText));
			}
		};
		obj.send(datastr);
	}
};

// 对Date的扩展，将 Date 转化为指定格式的String   
// 月(M)、日(d)、小时(h)、分(m)、秒(s)、季度(q) 可以用 1-2 个占位符，   
// 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字)   
// 例子：   
// (new Date()).Format("yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423   
// (new Date()).Format("yyyy-M-d h:m:s.S")      ==> 2006-7-2 8:9:4.18   
Date.prototype.Format = function(fmt)   
{ //author: meizz   
  var o = {   
    "M+" : this.getMonth()+1,                 //月份   
    "d+" : this.getDate(),                    //日   
    "h+" : this.getHours(),                   //小时   
    "m+" : this.getMinutes(),                 //分   
    "s+" : this.getSeconds(),                 //秒   
    "q+" : Math.floor((this.getMonth()+3)/3), //季度   
    "S"  : this.getMilliseconds()             //毫秒   
  };   
  if(/(y+)/.test(fmt))   
    fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));   
  for(var k in o)   
    if(new RegExp("("+ k +")").test(fmt))   
  fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));   
  return fmt;   
}