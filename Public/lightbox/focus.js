$(function() {
			var sWidth = $("#focus").width(); //获取焦点图的宽度（显示面积）
			var len = $("#focus ul li").length; //获取焦点图个数
			var index = 0;
			var picTimer;
			
			//以下代码添加数字按钮和按钮后的半透明条，还有上一页、下一页两个按钮
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			
			$("#focus").append(btn);
			$("#focus .btnBg").css("opacity",0.5);
		
			//为小按钮添加鼠标滑入事件，以显示相应的内容
			$("#focus .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");
		
			//本例为左右滚动，即所有li元素都是在同一排向左浮动，所以这里需要计算出外围ul元素的宽度
			$("#focus ul").css("width",sWidth * (len));
			
			//鼠标滑上焦点图时停止自动播放，滑出时开始自动播放
			$("#focus").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //此4000代表自动播放的间隔，单位：毫秒
			}).trigger("mouseleave");
			
			//显示图片函数，根据接收的index值显示相应的内容
			function showPics(index) { //普通切换
				var nowLeft = -index*sWidth; //根据index值计算ul元素的left值
				$("#focus ul").stop(true,false).animate({"left":nowLeft},300); //通过animate()调整ul元素滚动到计算出的position
				//$("#focus .btn span").removeClass("on").eq(index).addClass("on"); //为当前的按钮切换到选中的效果
				$("#focus .btn span").stop(true,false).animate({"opacity":"0.4"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //为当前的按钮切换到选中的效果
			}
		});
		
		
		
		
		$(function() {
			var sWidth = $("#focus1").width(); //获取焦点图的宽度（显示面积）
			var len = $("#focus1 ul li").length; //获取焦点图个数
			var index = 0;
			var picTimer;
			
			//以下代码添加数字按钮和按钮后的半透明条，还有上一页、下一页两个按钮
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			
			$("#focus1").append(btn);
			$("#focus1 .btnBg").css("opacity",0.5);
		
			//为小按钮添加鼠标滑入事件，以显示相应的内容
			$("#focus1 .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus1 .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");
		
			//本例为左右滚动，即所有li元素都是在同一排向左浮动，所以这里需要计算出外围ul元素的宽度
			$("#focus1 ul").css("width",sWidth * (len));
			
			//鼠标滑上焦点图时停止自动播放，滑出时开始自动播放
			$("#focus1").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //此4000代表自动播放的间隔，单位：毫秒
			}).trigger("mouseleave");
			
			//显示图片函数，根据接收的index值显示相应的内容
			function showPics(index) { //普通切换
				var nowLeft = -index*sWidth; //根据index值计算ul元素的left值
				$("#focus1 ul").stop(true,false).animate({"left":nowLeft},300); //通过animate()调整ul元素滚动到计算出的position
				//$("#focus1 .btn span").removeClass("on").eq(index).addClass("on"); //为当前的按钮切换到选中的效果
				$("#focus1 .btn span").stop(true,false).animate({"opacity":"0.4"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //为当前的按钮切换到选中的效果
			}
		});
		
		
		
		$(function() {
			var sWidth = $("#focus2").width(); //获取焦点图的宽度（显示面积）
			var len = $("#focus2 ul li").length; //获取焦点图个数
			var index = 0;
			var picTimer;
			
			//以下代码添加数字按钮和按钮后的半透明条，还有上一页、下一页两个按钮
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			
			$("#focus2").append(btn);
			$("#focus2 .btnBg").css("opacity",0.5);
		
			//为小按钮添加鼠标滑入事件，以显示相应的内容
			$("#focus2 .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus2 .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");
		
			//本例为左右滚动，即所有li元素都是在同一排向左浮动，所以这里需要计算出外围ul元素的宽度
			$("#focus2 ul").css("width",sWidth * (len));
			
			//鼠标滑上焦点图时停止自动播放，滑出时开始自动播放
			$("#focus2").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //此4000代表自动播放的间隔，单位：毫秒
			}).trigger("mouseleave");
			
			//显示图片函数，根据接收的index值显示相应的内容
			function showPics(index) { //普通切换
				var nowLeft = -index*sWidth; //根据index值计算ul元素的left值
				$("#focus2 ul").stop(true,false).animate({"left":nowLeft},300); //通过animate()调整ul元素滚动到计算出的position
				//$("#focus2 .btn span").removeClass("on").eq(index).addClass("on"); //为当前的按钮切换到选中的效果
				$("#focus2 .btn span").stop(true,false).animate({"opacity":"0.4"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //为当前的按钮切换到选中的效果
			}
		});
		
		
		
		$(function() {
			var sWidth = $("#focus3").width(); //获取焦点图的宽度（显示面积）
			var len = $("#focus3 ul li").length; //获取焦点图个数
			var index = 0;
			var picTimer;
			
			//以下代码添加数字按钮和按钮后的半透明条，还有上一页、下一页两个按钮
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			
			$("#focus3").append(btn);
			$("#focus3 .btnBg").css("opacity",0.5);
		
			//为小按钮添加鼠标滑入事件，以显示相应的内容
			$("#focus3 .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus3 .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");
		
			//本例为左右滚动，即所有li元素都是在同一排向左浮动，所以这里需要计算出外围ul元素的宽度
			$("#focus3 ul").css("width",sWidth * (len));
			
			//鼠标滑上焦点图时停止自动播放，滑出时开始自动播放
			$("#focus3").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //此4000代表自动播放的间隔，单位：毫秒
			}).trigger("mouseleave");
			
			//显示图片函数，根据接收的index值显示相应的内容
			function showPics(index) { //普通切换
				var nowLeft = -index*sWidth; //根据index值计算ul元素的left值
				$("#focus3 ul").stop(true,false).animate({"left":nowLeft},300); //通过animate()调整ul元素滚动到计算出的position
				//$("#focus3 .btn span").removeClass("on").eq(index).addClass("on"); //为当前的按钮切换到选中的效果
				$("#focus3 .btn span").stop(true,false).animate({"opacity":"0.4"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //为当前的按钮切换到选中的效果
			}
		});
		
		
		
		
		$(function() {
			var sWidth = $("#focus4").width(); //获取焦点图的宽度（显示面积）
			var len = $("#focus4 ul li").length; //获取焦点图个数
			var index = 0;
			var picTimer;
			
			//以下代码添加数字按钮和按钮后的半透明条，还有上一页、下一页两个按钮
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			
			$("#focus4").append(btn);
			$("#focus4 .btnBg").css("opacity",0.5);
		
			//为小按钮添加鼠标滑入事件，以显示相应的内容
			$("#focus4 .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus4 .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");
		
			//本例为左右滚动，即所有li元素都是在同一排向左浮动，所以这里需要计算出外围ul元素的宽度
			$("#focus4 ul").css("width",sWidth * (len));
			
			//鼠标滑上焦点图时停止自动播放，滑出时开始自动播放
			$("#focus4").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //此4000代表自动播放的间隔，单位：毫秒
			}).trigger("mouseleave");
			
			//显示图片函数，根据接收的index值显示相应的内容
			function showPics(index) { //普通切换
				var nowLeft = -index*sWidth; //根据index值计算ul元素的left值
				$("#focus4 ul").stop(true,false).animate({"left":nowLeft},300); //通过animate()调整ul元素滚动到计算出的position
				//$("#focus4 .btn span").removeClass("on").eq(index).addClass("on"); //为当前的按钮切换到选中的效果
				$("#focus4 .btn span").stop(true,false).animate({"opacity":"0.4"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //为当前的按钮切换到选中的效果
			}
		});
		
		
		
		
		$(function() {
			var sWidth = $("#focus5").width(); //获取焦点图的宽度（显示面积）
			var len = $("#focus5 ul li").length; //获取焦点图个数
			var index = 0;
			var picTimer;
			
			//以下代码添加数字按钮和按钮后的半透明条，还有上一页、下一页两个按钮
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			
			$("#focus5").append(btn);
			$("#focus5 .btnBg").css("opacity",0.5);
		
			//为小按钮添加鼠标滑入事件，以显示相应的内容
			$("#focus5 .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus5 .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");
		
			//本例为左右滚动，即所有li元素都是在同一排向左浮动，所以这里需要计算出外围ul元素的宽度
			$("#focus5 ul").css("width",sWidth * (len));
			
			//鼠标滑上焦点图时停止自动播放，滑出时开始自动播放
			$("#focus5").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //此4000代表自动播放的间隔，单位：毫秒
			}).trigger("mouseleave");
			
			//显示图片函数，根据接收的index值显示相应的内容
			function showPics(index) { //普通切换
				var nowLeft = -index*sWidth; //根据index值计算ul元素的left值
				$("#focus5 ul").stop(true,false).animate({"left":nowLeft},300); //通过animate()调整ul元素滚动到计算出的position
				//$("#focus5 .btn span").removeClass("on").eq(index).addClass("on"); //为当前的按钮切换到选中的效果
				$("#focus5 .btn span").stop(true,false).animate({"opacity":"0.4"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //为当前的按钮切换到选中的效果
			}
		});
		
		
		
		
		
		$(function() {
			var sWidth = $("#focus11").width(); //获取焦点图的宽度（显示面积）
			var len = $("#focus11 ul li").length; //获取焦点图个数
			var index = 0;
			var picTimer;
			
			//以下代码添加数字按钮和按钮后的半透明条，还有上一页、下一页两个按钮
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			
			$("#focus11").append(btn);
			$("#focus11 .btnBg").css("opacity",0);
		
			//为小按钮添加鼠标滑入事件，以显示相应的内容
			$("#focus11 .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus11 .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");
		
			//本例为左右滚动，即所有li元素都是在同一排向左浮动，所以这里需要计算出外围ul元素的宽度
			$("#focus11 ul").css("width",sWidth * (len));
			
			//鼠标滑上焦点图时停止自动播放，滑出时开始自动播放
			$("#focus11").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //此4000代表自动播放的间隔，单位：毫秒
			}).trigger("mouseleave");
			
			//显示图片函数，根据接收的index值显示相应的内容
			function showPics(index) { //普通切换
				var nowLeft = -index*sWidth; //根据index值计算ul元素的left值
				$("#focus11 ul").stop(true,false).animate({"left":nowLeft},300); //通过animate()调整ul元素滚动到计算出的position
				//$("#focus11 .btn span").removeClass("on").eq(index).addClass("on"); //为当前的按钮切换到选中的效果
				$("#focus11 .btn span").stop(true,false).animate({"opacity":"0.4"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //为当前的按钮切换到选中的效果
			}
		});
		
		
		
		$(function() {
			var sWidth = $("#focus12").width(); //获取焦点图的宽度（显示面积）
			var len = $("#focus12 ul li").length; //获取焦点图个数
			var index = 0;
			var picTimer;
			
			//以下代码添加数字按钮和按钮后的半透明条，还有上一页、下一页两个按钮
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			
			$("#focus12").append(btn);
			$("#focus12 .btnBg").css("opacity",0);
		
			//为小按钮添加鼠标滑入事件，以显示相应的内容
			$("#focus12 .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus12 .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");
		
			//本例为左右滚动，即所有li元素都是在同一排向左浮动，所以这里需要计算出外围ul元素的宽度
			$("#focus12 ul").css("width",sWidth * (len));
			
			//鼠标滑上焦点图时停止自动播放，滑出时开始自动播放
			$("#focus12").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //此4000代表自动播放的间隔，单位：毫秒
			}).trigger("mouseleave");
			
			//显示图片函数，根据接收的index值显示相应的内容
			function showPics(index) { //普通切换
				var nowLeft = -index*sWidth; //根据index值计算ul元素的left值
				$("#focus12 ul").stop(true,false).animate({"left":nowLeft},300); //通过animate()调整ul元素滚动到计算出的position
				//$("#focus12 .btn span").removeClass("on").eq(index).addClass("on"); //为当前的按钮切换到选中的效果
				$("#focus12 .btn span").stop(true,false).animate({"opacity":"0.4"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //为当前的按钮切换到选中的效果
			}
		});
		
		
		$(function() {
			var sWidth = $("#focus13").width(); //获取焦点图的宽度（显示面积）
			var len = $("#focus13 ul li").length; //获取焦点图个数
			var index = 0;
			var picTimer;
			
			//以下代码添加数字按钮和按钮后的半透明条，还有上一页、下一页两个按钮
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			
			$("#focus13").append(btn);
			$("#focus13 .btnBg").css("opacity",0);
		
			//为小按钮添加鼠标滑入事件，以显示相应的内容
			$("#focus13 .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus13 .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");
		
			//本例为左右滚动，即所有li元素都是在同一排向左浮动，所以这里需要计算出外围ul元素的宽度
			$("#focus13 ul").css("width",sWidth * (len));
			
			//鼠标滑上焦点图时停止自动播放，滑出时开始自动播放
			$("#focus13").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //此4000代表自动播放的间隔，单位：毫秒
			}).trigger("mouseleave");
			
			//显示图片函数，根据接收的index值显示相应的内容
			function showPics(index) { //普通切换
				var nowLeft = -index*sWidth; //根据index值计算ul元素的left值
				$("#focus13 ul").stop(true,false).animate({"left":nowLeft},300); //通过animate()调整ul元素滚动到计算出的position
				//$("#focus13 .btn span").removeClass("on").eq(index).addClass("on"); //为当前的按钮切换到选中的效果
				$("#focus13 .btn span").stop(true,false).animate({"opacity":"0.4"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //为当前的按钮切换到选中的效果
			}
		});
		
		$(function() {
			var sWidth = $("#focus14").width(); //获取焦点图的宽度（显示面积）
			var len = $("#focus14 ul li").length; //获取焦点图个数
			var index = 0;
			var picTimer;
			
			//以下代码添加数字按钮和按钮后的半透明条，还有上一页、下一页两个按钮
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			
			$("#focus14").append(btn);
			$("#focus14 .btnBg").css("opacity",0);
		
			//为小按钮添加鼠标滑入事件，以显示相应的内容
			$("#focus14 .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus14 .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");
		
			//本例为左右滚动，即所有li元素都是在同一排向左浮动，所以这里需要计算出外围ul元素的宽度
			$("#focus14 ul").css("width",sWidth * (len));
			
			//鼠标滑上焦点图时停止自动播放，滑出时开始自动播放
			$("#focus14").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //此4000代表自动播放的间隔，单位：毫秒
			}).trigger("mouseleave");
			
			//显示图片函数，根据接收的index值显示相应的内容
			function showPics(index) { //普通切换
				var nowLeft = -index*sWidth; //根据index值计算ul元素的left值
				$("#focus14 ul").stop(true,false).animate({"left":nowLeft},300); //通过animate()调整ul元素滚动到计算出的position
				//$("#focus14 .btn span").removeClass("on").eq(index).addClass("on"); //为当前的按钮切换到选中的效果
				$("#focus14 .btn span").stop(true,false).animate({"opacity":"0.4"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //为当前的按钮切换到选中的效果
			}
		});
		
		$(function() {
			var sWidth = $("#focus15").width(); //获取焦点图的宽度（显示面积）
			var len = $("#focus15 ul li").length; //获取焦点图个数
			var index = 0;
			var picTimer;
			
			//以下代码添加数字按钮和按钮后的半透明条，还有上一页、下一页两个按钮
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			
			$("#focus15").append(btn);
			$("#focus15 .btnBg").css("opacity",0);
		
			//为小按钮添加鼠标滑入事件，以显示相应的内容
			$("#focus15 .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus15 .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");
		
			//本例为左右滚动，即所有li元素都是在同一排向左浮动，所以这里需要计算出外围ul元素的宽度
			$("#focus15 ul").css("width",sWidth * (len));
			
			//鼠标滑上焦点图时停止自动播放，滑出时开始自动播放
			$("#focus15").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //此4000代表自动播放的间隔，单位：毫秒
			}).trigger("mouseleave");
			
			//显示图片函数，根据接收的index值显示相应的内容
			function showPics(index) { //普通切换
				var nowLeft = -index*sWidth; //根据index值计算ul元素的left值
				$("#focus15 ul").stop(true,false).animate({"left":nowLeft},300); //通过animate()调整ul元素滚动到计算出的position
				//$("#focus15 .btn span").removeClass("on").eq(index).addClass("on"); //为当前的按钮切换到选中的效果
				$("#focus15 .btn span").stop(true,false).animate({"opacity":"0.4"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //为当前的按钮切换到选中的效果
			}
		});
		
		$(function() {
			var sWidth = $("#focus16").width(); //获取焦点图的宽度（显示面积）
			var len = $("#focus16 ul li").length; //获取焦点图个数
			var index = 0;
			var picTimer;
			
			//以下代码添加数字按钮和按钮后的半透明条，还有上一页、下一页两个按钮
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			
			$("#focus16").append(btn);
			$("#focus16 .btnBg").css("opacity",0);
		
			//为小按钮添加鼠标滑入事件，以显示相应的内容
			$("#focus16 .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus16 .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");
		
			//本例为左右滚动，即所有li元素都是在同一排向左浮动，所以这里需要计算出外围ul元素的宽度
			$("#focus16 ul").css("width",sWidth * (len));
			
			//鼠标滑上焦点图时停止自动播放，滑出时开始自动播放
			$("#focus16").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //此4000代表自动播放的间隔，单位：毫秒
			}).trigger("mouseleave");
			
			//显示图片函数，根据接收的index值显示相应的内容
			function showPics(index) { //普通切换
				var nowLeft = -index*sWidth; //根据index值计算ul元素的left值
				$("#focus16 ul").stop(true,false).animate({"left":nowLeft},300); //通过animate()调整ul元素滚动到计算出的position
				//$("#focus16 .btn span").removeClass("on").eq(index).addClass("on"); //为当前的按钮切换到选中的效果
				$("#focus16 .btn span").stop(true,false).animate({"opacity":"0.4"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //为当前的按钮切换到选中的效果
			}
		});
		
		