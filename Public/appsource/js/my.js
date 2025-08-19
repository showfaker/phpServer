(function() {
	/**
	 * 格式化时间的辅助类，将一个时间转换成x小时前、y天前等
	 */
	var dateUtils = {
		UNITS: {
			'年': 31557600000,
			'月': 2629800000,
			'天': 86400000,
			'小时': 3600000,
			'分钟': 60000,
			'秒': 1000
		},
		humanize: function(milliseconds) {
			var humanize = '';
			mui.each(this.UNITS, function(unit, value) {
				if(milliseconds >= value) {
					humanize = Math.floor(milliseconds / value) + unit + '前';
					return false;
				}
				return true;
			});
			return humanize || '刚刚';
		},
		format: function(dateStr) {
			var date = this.parse(dateStr)
			var diff = Date.now() - date.getTime();
			if(diff < this.UNITS['天']) {
				return this.humanize(diff);
			}
			
			var _format = function(number) {
				return(number < 10 ? ('0' + number) : number);
			};
			return date.getFullYear() + '/' + _format(date.getMonth() + 1) + '/' + _format(date.getDay()) + '-' + _format(date.getHours()) + ':' + _format(date.getMinutes());
		},
		parse:function (str) {//将"yyyy-mm-dd HH:MM:ss"格式的字符串，转化为一个Date对象
			var a = str.split(/[^0-9]/);
			return new Date (a[0],a[1]-1,a[2],a[3],a[4],a[5] );
		}
	};
			
	var mydates = document.getElementsByClassName("mydate");
	var currDate = new Date();
	for(var i = 0; i < mydates.length; i++) {

		mydates[i].firstElementChild.innerText = currDate.format("yyyy-MM-dd");
		mydates[i].addEventListener('tap', function() {
			var dDate = new Date();
			//设置当前日期（不设置默认当前日期）
			var minDate = new Date();
			//最小时间
			minDate.setFullYear(1990, 0, 1);
			var maxDate = new Date();
			//最大时间
			maxDate.setFullYear(2030, 11, 31);

			var thisNode = this;
			plus.nativeUI.pickDate(function(e) {
				var d = e.date;
				thisNode.firstElementChild.innerText = d.format("yyyy-MM-dd");
				/*
				var chils = thisNode.childNodes;
				for(var j=0;j<chils.length;j++){
					//alert(chils[j].tagName);
					if(chils[j].tagName=="A"){
						chils[j].innerText = d.format("yyyy-MM-dd");
					}
				}*/

			}, function(e) {
				mui.toast("您没有选择日期");
			}, {
				title: '请选择日期',
				date: dDate,
				minDate: minDate,
				maxDate: maxDate
			});
		});
	}

	var timeNavs = document.getElementsByClassName("timeNav");
	for(var i = 0; i < timeNavs.length; i++) {
		timeNavs[i].addEventListener("tap", function() {
			var timeNavs = document.getElementsByClassName("timeNav");
			for(var j = 0; j < timeNavs.length; j++) {
				if(this.id == timeNavs[j].id) {
					timeNavs[j].style.backgroundColor = "#187b7f";
				} else {
					timeNavs[j].style.backgroundColor = "#293742";
				}
			}
		});
	}
})();

