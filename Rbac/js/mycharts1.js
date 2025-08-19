if($(window).width() > 991) {
	var total = window.innerHeight;
	document.getElementById("title").style.height = total * 0.1 + "px";
	//document.getElementById("title_1").style.height = total * 0.03 + "px";
	document.getElementById("main").style.height = total * 0.85 + "px";
	title = document.getElementById("title");
	main = document.getElementById("main");

	box02 = document.getElementById("box02");
	box03 = document.getElementById("box03");
	box04 = document.getElementById("box04");
    box05 = document.getElementById("box05");
	box06 = document.getElementById("box06");
	box07 = document.getElementById("box07");
	//box08 = document.getElementById("box08");
	//box09 = document.getElementById("box09");
	box10 = document.getElementById("box10");
	box11 = document.getElementById("box11");
	box12 = document.getElementById("box12");
	title_h = title.offsetHeight;
	main_h = main.offsetHeight;

	box02_h = box02.offsetHeight;
	box03_h = box03.offsetHeight;
	box04_h = box04.offsetHeight;
	document.getElementById("box01").style.height = main_h * 0.552 + "px";
	box02.style.height = main_h * 0.4 + "px";
	box03.style.height = main_h * 0.24 + "px";
	box04.style.height = main_h * 0.23 + "px";
	box05.style.height = main_h * 0.24 + "px";
	box06.style.height = main_h * 0.24 + "px";
	box07.style.height = main_h * 0.24 + "px";
	//box08.style.height = main_h * 0.2 + "px";
	//box09.style.height = main_h * 0.2 + "px";
	box10.style.height = main_h * 0.23 + "px";
	box11.style.height = main_h * 0.23 + "px";
	box12.style.height = main_h * 0.23 + "px";
	box01 = document.getElementById("box01");
	box01_h = box01.offsetHeight;
	document.getElementById("total-mn1").style.height = box01_h * 0.02 + "px";
};
var app = angular.module('myApp', []);
app.controller('customersCtrl', function($scope, $http) {
	$http({
		method: 'get',
		url: 'data/da.json'
	}).then(function(res) {
		$scope.listHead = res.data.listHead; //数据列表-头
		$scope.listContent = res.data.listContent; //数据列表
		$scope.listData1 = res.data.listData1; //数据列表
		
		
		//上左					
		var worldMapContainer2 = document.getElementById('box2');
		var box01 = document.getElementById("box01");
		var box01_h = box01.offsetHeight;
		var box01_w = box01.offsetWidth;
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer2 = function() {
			worldMapContainer2.style.width = box01_w * 0.95 + 'px';
			worldMapContainer2.style.height = box01_h * 0.8 + 'px';
			worldMapContainer2.style.float = "center";
		};
		//设置容器高宽
		resizeWorldMapContainer2();
		// 基于准备好的dom，初始化echarts实例
		var myChart = echarts.init(worldMapContainer2);
		var option = {			
			color: ['#FCDA1E'],
			tooltip: {
				trigger: 'axis',
				formatter: "{b}<br>{c}({a})",
				axisPointer: { // 坐标轴指示器，坐标轴触发有效
					type: '' // 默认为直线，可选为：'line' | 'shadow'
				}
			},
			legend: {
				data: ['单位:kwh'],
				align: 'right',
				right:'0',
				textStyle:[{
					color:'#FCDA1E'
				}],
			},
			textStyle: {
				color: '#ccc'
			},
			grid: {
				top: '10%',
				left: '3%',
				right: '0%',
				bottom: '6%',
				containLabel: true
			},
			xAxis: [{
				type: 'category',
				data: ["0:00","1:00","2:00","3:00","4:00","5:00","6:00","7:00","8:00","9:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00","17:00","18:00","19:00","20:00","21:00","22:00","23:00","24:00"],
				axisTick:true,
				axisLine:{
                        lineStyle:{
                            color:'#0E4892',
                            width:1,//这里是为了突出显示加上的
                        }
                },
			}],
			yAxis: [{
				axisLine: {
					show: false
				},
				splitLine:{
                        lineStyle:{
                            color:['#053A62'],
                            width:1,//这里是为了突出显示加上的
                        }
                },
				axisTick:false,
				type: 'value'
			}],
			series: [{
				name: '单位:kwh',
				type: 'line',
				itemStyle : {
                    normal : {
						areaStyle: {
							color: new echarts.graphic.LinearGradient(
                                        0, 0, 0, 1,
                                        [
                                            {offset: 0, color: '#566B32'},
                                            {offset: 0.5, color: '#304C3E'},
                                            {offset: 1, color: '#0B3445'}
                                        ]
                                )
						},
                    }
                },				
				barWidth: '30%',				
				data: ["1208","1108","1000","995","980","1001","1008","1120","1150","1220","1248","1308","1368","1410","1480","1278","1500","1567","1500","1465","1346","1340","1300","1280","1200"],				
			}]
		};
		myChart.setOption(option);

		//用于使chart自适应高度和宽度
		window.onresize = function() {
			//重置容器高宽
			resizeWorldMapContainer2();
			myChart.resize();
		};
		
		//上右
		var worldMapContainer = document.getElementById('box3');
		box03 = document.getElementById("box02");
		box03_h = box03.offsetHeight;
		box03_w = box03.offsetWidth;
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer = function() {
			worldMapContainer.style.width = box03_w * 0.9 + 'px';
			worldMapContainer.style.height = box03_h * 0.9 + 'px';
			worldMapContainer.style.marginTop = '10px';
		};
		//设置容器高宽
		resizeWorldMapContainer();
		// 基于准备好的dom，初始化echarts实例
		var myChart = echarts.init(worldMapContainer);

		// 指定图表的配置项和数据
		var option = {
			tooltip: {
				trigger: 'item',
				formatter: "{a} <br/>{b}:{c} ({d}%)"
			},
			grid: {
				height: '100%',
				y: '10%',
				x: '0%'
			},
			legend: {
				orient : 'vertical',
				itemWidth: 15,
				itemHeight: 15,
				itemGap: 45,
				x: '70%',
				y: '10%',
				textStyle: {
					fontSize: 14,
					color: '#9FC4E1',
					
					
				},
				
				data: ["冲压车间","总装车间","总成车间","油漆车间","车身车间","其他"],
			},
			series: [
				{
					name: '能耗占比',
					type: 'pie',
					center: ['30%', '50%'],
					radius: ['50%', '65%'],
					color: ['#d9a503', '#2551bb', '#81b740', '#da70d6', '#ff7f50','#B5425A'],
					label: {
						normal: {
							formatter: "{b}:{c}"
						}
					},
					labelLine: {
						normal: {
							show: true,
							length:30
						}
					},
					data:[
						{value:335, name:'冲压车间'},
						{value:310, name:'总装车间'},
						{value:234, name:'总成车间'},
						{value:135, name:'油漆车间'},
						{value:1548, name:'车身车间'},
						{value:130, name:'其他'}
					]

				}
			]
		};

		// 使用刚指定的配置项和数据显示图表。
		myChart.setOption(option);				
		
		//下一
		var worldMapContainer4 = document.getElementById('box4');
		box04 = document.getElementById("box04");
		box04_h = box04.offsetHeight;
		box04_w = box04.offsetWidth;
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer4 = function() {
			worldMapContainer4.style.width = box04_w * 0.9 + 'px';
			worldMapContainer4.style.height = box04_h * 0.9 + 'px';
		};
		//设置容器高宽
		resizeWorldMapContainer4();
		// 基于准备好的dom，初始化echarts实例
		var myChart = echarts.init(worldMapContainer4);
		// 指定图表的配置项和数据
		option = {
			tooltip : {
				formatter: "{a}: {b}"
			},
			toolbox: {
				show: false
			},
			series : [
				{
					name: '今日能耗',
					type: 'gauge',
					center: ['50%', '60%'],    // 默认全局居中
					radius: '100%',
					z: 3,
					min: 0,
					max: 20000,
					startAngle:180,
					endAngle:0,
					splitNumber: 5,
					radius: '100%',
					axisLine: {            // 坐标轴线
						lineStyle: {       // 属性lineStyle控制线条样式
							width: 12,
							color: [[0.2, '#81b740'],[0.8, '#E5BD66'],[1, '#B5425A']],
						}
					},
					axisTick: {            // 坐标轴小标记
						length: -2,        // 属性length控制线长
						lineStyle: {       // 属性lineStyle控制线条样式
							color: 'auto'
						}
					},
					splitLine: {           // 分隔线
						length: 0,         // 属性length控制线长
						lineStyle: {       // 属性lineStyle（详见lineStyle）控制线条样式
							color: 'auto'
						}
					},
					axisLabel: {
						backgroundColor: 'auto',
						borderRadius: 2,
						color: '#eee',
						textShadowBlur: 2,
						textShadowOffsetX: 1,
						textShadowOffsetY: 1,
						textShadowColor: '#222',
						formatter:function(v){
							switch (v + '') {
								case '0' : return '0%';
								case '20000' : return '100%';
							}
						}
					},
					title : {
						// 其余属性默认使用全局文本样式，详见TEXTSTYLE
						textStyle: {
							fontWeight: 'bold',
							fontSize: 12,
							color:"#56C7E9"
						}
					},
					detail: {
						formatter:'6%',
						textStyle: {
							fontWeight: 'bold',
							fontSize: 12
						}
					},
					data:[{name: '1200kwh',value: 1200}]
				}
			]
		};

		// 使用刚指定的配置项和数据显示图表。
		myChart.setOption(option);

		//用于使chart自适应高度和宽度
		window.onresize = function() {
			//重置容器高宽
			resizeWorldMapContainer5();
			myChart.resize();
		};
		
		//下二
		var worldMapContainer4 = document.getElementById('box5');
		box04 = document.getElementById("box04");
		box04_h = box04.offsetHeight;
		box04_w = box04.offsetWidth;
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer4 = function() {
			worldMapContainer4.style.width = box04_w * 0.9 + 'px';
			worldMapContainer4.style.height = box04_h * 0.9 + 'px';
		};
		//设置容器高宽
		resizeWorldMapContainer4();
		// 基于准备好的dom，初始化echarts实例
		var myChart = echarts.init(worldMapContainer4);
		// 指定图表的配置项和数据
		option = {
			tooltip : {
				formatter: "{a}: {b}"
			},
			toolbox: {
				show: false
			},
			series : [
				{
					name: '今日节约能耗',
					type: 'gauge',
					center: ['50%', '60%'],    // 默认全局居中
					radius: '100%',
					z: 3,
					min: 0,
					max: 20000,
					startAngle:180,
					endAngle:0,
					splitNumber: 5,
					radius: '100%',
					axisLine: {            // 坐标轴线
						lineStyle: {       // 属性lineStyle控制线条样式
							width: 12,
							color: [[0.2, '#81b740'],[0.8, '#E5BD66'],[1, '#B5425A']],
						}
					},
					axisTick: {            // 坐标轴小标记
						length: -2,        // 属性length控制线长
						lineStyle: {       // 属性lineStyle控制线条样式
							color: 'auto'
						}
					},
					splitLine: {           // 分隔线
						length: 0,         // 属性length控制线长
						lineStyle: {       // 属性lineStyle（详见lineStyle）控制线条样式
							color: 'auto'
						}
					},
					axisLabel: {
						backgroundColor: 'auto',
						borderRadius: 2,
						color: '#eee',
						textShadowBlur: 2,
						textShadowOffsetX: 1,
						textShadowOffsetY: 1,
						textShadowColor: '#222',
						formatter:function(v){
							switch (v + '') {
								case '0' : return '0%';
								case '20000' : return '100%';
							}
						}
					},
					title : {
						// 其余属性默认使用全局文本样式，详见TEXTSTYLE
						textStyle: {
							fontWeight: 'bold',
							fontSize: 12,
							color:"#56C7E9"
						}
					},
					detail: {
						formatter:'5.8%',
						textStyle: {
							fontWeight: 'bold',
							fontSize: 12
						}
					},
					data:[{name: '1165kwh',value: 1165}]
				}
			]
		};

		// 使用刚指定的配置项和数据显示图表。
		myChart.setOption(option);

		//用于使chart自适应高度和宽度
		window.onresize = function() {
			//重置容器高宽
			resizeWorldMapContainer5();
			myChart.resize();
		};
		
		//下三
		var worldMapContainer4 = document.getElementById('box6');
		box04 = document.getElementById("box04");
		box04_h = box04.offsetHeight;
		box04_w = box04.offsetWidth;
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer4 = function() {
			worldMapContainer4.style.width = box04_w * 0.9 + 'px';
			worldMapContainer4.style.height = box04_h * 0.9 + 'px';
		};
		//设置容器高宽
		resizeWorldMapContainer4();
		// 基于准备好的dom，初始化echarts实例
		var myChart = echarts.init(worldMapContainer4);
		// 指定图表的配置项和数据
		option = {
			tooltip : {
				formatter: "{a}: {b}"
			},
			toolbox: {
				show: false
			},
			series : [
				{
					name: '今日能耗费用',
					type: 'gauge',
					center: ['50%', '60%'],    // 默认全局居中
					radius: '100%',
					z: 3,
					min: 0,
					max: 20000,
					startAngle:180,
					endAngle:0,
					splitNumber: 5,
					radius: '100%',
					axisLine: {            // 坐标轴线
						lineStyle: {       // 属性lineStyle控制线条样式
							width: 12,
							color: [[0.2, '#81b740'],[0.8, '#E5BD66'],[1, '#B5425A']],
						}
					},
					axisTick: {            // 坐标轴小标记
						length: -2,        // 属性length控制线长
						lineStyle: {       // 属性lineStyle控制线条样式
							color: 'auto'
						}
					},
					splitLine: {           // 分隔线
						length: 0,         // 属性length控制线长
						lineStyle: {       // 属性lineStyle（详见lineStyle）控制线条样式
							color: 'auto'
						}
					},
					axisLabel: {
						backgroundColor: 'auto',
						borderRadius: 2,
						color: '#eee',
						textShadowBlur: 2,
						textShadowOffsetX: 1,
						textShadowOffsetY: 1,
						textShadowColor: '#222',
						formatter:function(v){
							switch (v + '') {
								case '0' : return '0%';
								case '20000' : return '100%';
							}
						}
					},
					title : {
						// 其余属性默认使用全局文本样式，详见TEXTSTYLE
						textStyle: {
							fontWeight: 'bold',
							fontSize: 12,
							color:"#56C7E9"
						}
					},
					detail: {
						formatter:'72.5%',
						textStyle: {
							fontWeight: 'bold',
							fontSize: 12
						}
					},
					data:[{name: '14500元',value: 14500}]
				}
			]
		};

		// 使用刚指定的配置项和数据显示图表。
		myChart.setOption(option);

		//用于使chart自适应高度和宽度
		window.onresize = function() {
			//重置容器高宽
			resizeWorldMapContainer5();
			myChart.resize();
		};
		
		//下四
		var worldMapContainer4 = document.getElementById('box7');
		box04 = document.getElementById("box04");
		box04_h = box04.offsetHeight;
		box04_w = box04.offsetWidth;
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer4 = function() {
			worldMapContainer4.style.width = box04_w * 0.9 + 'px';
			worldMapContainer4.style.height = box04_h * 0.9 + 'px';
		};
		//设置容器高宽
		resizeWorldMapContainer4();
		// 基于准备好的dom，初始化echarts实例
		var myChart = echarts.init(worldMapContainer4);
		// 指定图表的配置项和数据
		option = {
			tooltip : {
				formatter: "{a}: {b}"
			},
			toolbox: {
				show: false
			},
			series : [
				{
					name: '今日COP',
					type: 'gauge',
					center: ['50%', '60%'],    // 默认全局居中
					radius: '100%',
					z: 3,
					min: 0,
					max: 20000,
					startAngle:180,
					endAngle:0,
					splitNumber: 5,
					radius: '100%',
					axisLine: {            // 坐标轴线
						lineStyle: {       // 属性lineStyle控制线条样式
							width: 12,
							color: [[0.2, '#81b740'],[0.8, '#E5BD66'],[1, '#B5425A']],
						}
					},
					axisTick: {            // 坐标轴小标记
						length: -2,        // 属性length控制线长
						lineStyle: {       // 属性lineStyle控制线条样式
							color: 'auto'
						}
					},
					splitLine: {           // 分隔线
						length: 0,         // 属性length控制线长
						lineStyle: {       // 属性lineStyle（详见lineStyle）控制线条样式
							color: 'auto'
						}
					},
					axisLabel: {
						backgroundColor: 'auto',
						borderRadius: 2,
						color: '#eee',
						textShadowBlur: 2,
						textShadowOffsetX: 1,
						textShadowOffsetY: 1,
						textShadowColor: '#222',
						formatter:function(v){
							switch (v + '') {
								case '0' : return '0%';
								case '20000' : return '100%';
							}
						}
					},
					title : {
						// 其余属性默认使用全局文本样式，详见TEXTSTYLE
						textStyle: {
							fontWeight: 'bold',
							fontSize: 12,
							color:"#56C7E9"
						}
					},
					detail: {
						formatter:'40%',
						textStyle: {
							fontWeight: 'bold',
							fontSize: 12
						}
					},
					data:[{name: '8000',value: 8000}]
				}
			]
		};

		// 使用刚指定的配置项和数据显示图表。
		myChart.setOption(option);

		//用于使chart自适应高度和宽度
		window.onresize = function() {
			//重置容器高宽
			resizeWorldMapContainer5();
			myChart.resize();
		};
	});
});